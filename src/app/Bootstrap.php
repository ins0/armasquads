<?php

declare(strict_types=1);

namespace Armasquads;

use Armasquads\Controller\PageController;
use Armasquads\Middleware\ContextMiddleware;
use Armasquads\Middleware\RequireUser;
use Armasquads\Repository\SquadRepository;
use Armasquads\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

final class Bootstrap
{
    public static function createApp(Config $config): App
    {
        date_default_timezone_set('Europe/Berlin');

        $app = AppFactory::create();
        $responseFactory = $app->getResponseFactory();
        $routeParser = $app->getRouteCollector()->getRouteParser();

        $db = Database::connect($config);
        $users = new UserRepository($db);
        $squads = new SquadRepository($db);

        $context = new ContextMiddleware($config, $routeParser, $users);
        $requireUser = new RequireUser($responseFactory, $routeParser);
        $pages = new PageController($users, $squads);

        $notMigrated = static function ($request, ResponseInterface $response): ResponseInterface {
            $response->getBody()->write('Not migrated yet');

            return $response->withStatus(501);
        };

        $app->group('', function (RouteCollectorProxy $web) use ($pages, $requireUser, $notMigrated): void {
            $web->get('/', [$pages, 'home'])->setName('home');
            $web->get('/impressum', [$pages, 'impressum'])->setName('impressum');
            $web->map(['GET', 'POST'], '/login', $notMigrated)->setName('login');
            $web->map(['GET', 'POST'], '/register', $notMigrated)->setName('register');
            $web->get('/logout', $notMigrated)->setName('logout');
            $web->get('/RESTful/api', $notMigrated)->setName('api-docs');

            $web->group('/user', function (RouteCollectorProxy $user) use ($notMigrated): void {
                $user->get('', $notMigrated)->setName('user');
                $user->get('/home', $notMigrated)->setName('dashboard');
                $user->get('/support', $notMigrated)->setName('support');
                $user->map(['GET', 'POST'], '/account', $notMigrated)->setName('account');
                $user->get('/api', $notMigrated)->setName('user-api');
                $user->get('/squads', $notMigrated)->setName('squads');
                $user->map(['GET', 'POST'], '/squads/create', $notMigrated)->setName('squad-create');
                $user->map(['GET', 'POST'], '/squads/edit/{id}', $notMigrated)->setName('squad-edit');
                $user->get('/squads/delete/{id}', $notMigrated)->setName('squad-delete');
                $user->map(['GET', 'POST'], '/squads/member/{id}', $notMigrated)->setName('squad-members');
                $user->get('/squads/download/{id}', $notMigrated)->setName('squad-download');
            })->add($requireUser);
        })->add($context);

        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();
        $errors = $app->addErrorMiddleware($config->displayErrors, true, true);
        $errors->setDefaultErrorHandler(new ErrorHandler($responseFactory, $context));

        return $app;
    }
}
