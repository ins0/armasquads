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

    /**
     * @param array<string, mixed> $post
     */
    protected static function field(array $post, string $name): string
    {
        return is_string($post[$name] ?? null) ? $post[$name] : '';
    }

    /**
     * Only a local path is accepted as redirect target, so the parameter cannot send users to another site.
     */
    protected static function localPath(mixed $url): ?string
    {
        if (!is_string($url) || $url === '') {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (!is_string($path) || !str_starts_with($path, '/') || str_starts_with($path, '//') || str_contains($path, '\\')) {
            return null;
        }

        return $path;
    }

    /**
     * The nginx-proxy in front of the container appends the client address to X-Forwarded-For.
     */
    protected static function clientIp(ServerRequestInterface $request): string
    {
        $forwarded = array_map(trim(...), explode(',', $request->getHeaderLine('X-Forwarded-For')));
        $last = end($forwarded);

        return $last !== false && $last !== '' ? $last : (string) ($request->getServerParams()['REMOTE_ADDR'] ?? '');
    }

    protected static function isValidCsrf(ServerRequestInterface $request): bool
    {
        return Context::of($request)->session->isValidCsrfToken(self::post($request)['csrf'] ?? null);
    }
}
