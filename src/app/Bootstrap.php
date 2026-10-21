<?php

declare(strict_types=1);

namespace Armasquads;

use Armasquads\Api\ApiController;
use Armasquads\Controller\AccountController;
use Armasquads\Controller\ApiDocsController;
use Armasquads\Controller\AuthController;
use Armasquads\Controller\LanguageController;
use Armasquads\Controller\MemberController;
use Armasquads\Controller\PageController;
use Armasquads\Controller\SquadController;
use Armasquads\Controller\SquadFileController;
use Armasquads\Middleware\ContextMiddleware;
use Armasquads\Middleware\RequireUser;
use Armasquads\Repository\ApiKeyRepository;
use Armasquads\Repository\SquadRepository;
use Armasquads\Repository\UserRepository;
use Armasquads\Service\LoginHandoff;
use Armasquads\Service\LogoService;
use Armasquads\Service\SquadFiles;
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
        $keys = new ApiKeyRepository($db);
        $logos = new LogoService($config->publicPath, $config->armakeBinary);
        $squadFiles = new SquadFiles($squads, $logos);

        $context = new ContextMiddleware($config, $routeParser, $users);
        $requireUser = new RequireUser($responseFactory, $routeParser);

        $pages = new PageController($users, $squads, $config->publicPath . '/uploads/logos', $config->templatePath . '/news');
        $auth = new AuthController($users);
        $language = new LanguageController(new LoginHandoff(sys_get_temp_dir() . '/armasquads-login-handoff'), $users);
        $account = new AccountController($users);
        $apiDocs = new ApiDocsController($keys);
        $squadPages = new SquadController($squads, $logos, $squadFiles);
        $members = new MemberController($squads, $logos);
        $files = new SquadFileController($squads, $logos, $squadFiles);
        $api = new ApiController($config, $keys, $squads, $logos);

        $app->group('', function (RouteCollectorProxy $web) use ($pages, $auth, $language, $account, $apiDocs, $squadPages, $members, $requireUser): void {
            $web->get('/', [$pages, 'home'])->setName('home');
            $web->get('/impressum', [$pages, 'impressum'])->setName('impressum');
            $web->map(['GET', 'POST'], '/login', [$auth, 'login'])->setName('login');
            $web->map(['GET', 'POST'], '/register', [$auth, 'register'])->setName('register');
            $web->get('/logout', [$auth, 'logout'])->setName('logout');
            $web->get('/RESTful/api', [$apiDocs, 'public'])->setName('api-docs');
            $web->get('/language/{language:de|en}', [$language, 'switch'])->setName('language');
            $web->get('/language/handoff', [$language, 'handoff'])->setName('language-handoff');

            $web->group('/user', function (RouteCollectorProxy $user) use ($pages, $account, $apiDocs, $squadPages, $members): void {
                $user->get('', [$pages, 'dashboard'])->setName('user');
                $user->get('/home', [$pages, 'dashboard'])->setName('dashboard');
                $user->get('/support', [$pages, 'support'])->setName('support');
                $user->map(['GET', 'POST'], '/account', [$account, 'index'])->setName('account');
                $user->get('/api', [$apiDocs, 'user'])->setName('user-api');
                $user->get('/squads', [$squadPages, 'index'])->setName('squads');
                $user->map(['GET', 'POST'], '/squads/create', [$squadPages, 'create'])->setName('squad-create');
                $user->map(['GET', 'POST'], '/squads/edit/{id}', [$squadPages, 'edit'])->setName('squad-edit');
                $user->get('/squads/delete/{id}', [$squadPages, 'delete'])->setName('squad-delete');
                $user->map(['GET', 'POST'], '/squads/member/{id}', [$members, 'edit'])->setName('squad-members');
                $user->get('/squads/download/{id}', [$squadPages, 'download'])->setName('squad-download');
            })->add($requireUser);
        })->add($context);

        $app->get('/user/squads/xml/{id}/[squad.xml]', [$files, 'xml'])->setName('squad-xml');
        $app->get('/user/squads/xml/{id}/{logo:[a-f0-9]{32}}.paa', [$files, 'logo'])->setName('squad-logo');
        $app->get('/user/squads/xml/{id}/squad.dtd', [$files, 'dtd'])->setName('squad-dtd');

        $app->any('/api', [$api, 'selectVersion']);
        $app->any('/api/v1', [$api, 'selectResource']);
        $app->any('/api/v1/squads', [$api, 'squads']);
        $app->any('/api/v1/squads/{id}', [$api, 'squad']);
        $app->any('/api/v1/squads/{id}/members', [$api, 'members']);
        $app->any('/api/v1/squads/{id}/members/{mid}', [$api, 'member']);

        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();
        $errors = $app->addErrorMiddleware($config->displayErrors, true, true);
        $errors->setDefaultErrorHandler(new ErrorHandler($responseFactory, $context));

        return $app;
    }
}
