<?php

declare(strict_types=1);

namespace Armasquads\Middleware;

use Armasquads\Config;
use Armasquads\Context;
use Armasquads\Repository\UserRepository;
use Armasquads\Session;
use Armasquads\Translator;
use Armasquads\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteParserInterface;

final readonly class ContextMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Config $config,
        private RouteParserInterface $routeParser,
        private UserRepository $users,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($this->withContext($request));
    }

    public function withContext(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request->getAttribute(Context::class) instanceof Context) {
            return $request;
        }

        $scheme = self::isHttps($request) ? 'https' : 'http';
        $host = strtolower($request->getHeaderLine('Host'));

        $session = Session::start($scheme === 'https');
        $translator = Translator::forHost($host, $this->config->languagePath);
        $user = $session->userId() !== null ? $this->users->find($session->userId()) : null;
        $view = new View($this->config->templatePath, $this->routeParser, $translator, $session, $user, $host, $scheme, $request->getUri()->getPath());

        return $request->withAttribute(Context::class, new Context($session, $translator, $user, $view));
    }

    private static function isHttps(ServerRequestInterface $request): bool
    {
        $server = $request->getServerParams();

        return strtolower($request->getHeaderLine('X-Forwarded-Proto')) === 'https'
            || (isset($server['HTTPS']) && $server['HTTPS'] !== '' && $server['HTTPS'] !== 'off');
    }
}
