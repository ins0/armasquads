<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Controller
{
    /**
     * @param array<string, mixed> $variables
     */
    protected static function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $template,
        array $variables = [],
        int $status = 200,
    ): ResponseInterface {
        $response->getBody()->write(Context::of($request)->view->render($template, $variables));

        return $response->withStatus($status)->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * @param array<string, string|int> $parameters
     * @param array<string, string|int> $query
     */
    protected static function redirectToRoute(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $route,
        array $parameters = [],
        array $query = [],
    ): ResponseInterface {
        return self::redirect($response, Context::of($request)->view->url($route, $parameters, $query));
    }

    protected static function redirect(ResponseInterface $response, string $location): ResponseInterface
    {
        return $response->withStatus(302)->withHeader('Location', $location);
    }

    /**
     * @return array<string, mixed>
     */
    protected static function post(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();

        return is_array($body) ? $body : [];
    }

    protected static function isValidCsrf(ServerRequestInterface $request): bool
    {
        return Context::of($request)->session->isValidCsrfToken(self::post($request)['csrf'] ?? null);
    }
}
