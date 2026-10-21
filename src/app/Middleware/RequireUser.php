<?php

declare(strict_types=1);

namespace Armasquads\Middleware;

use Armasquads\Context;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteParserInterface;

final readonly class RequireUser implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private RouteParserInterface $routeParser,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Context::of($request)->user === null) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', $this->routeParser->urlFor('login'));
        }

        return $handler->handle($request);
    }
}
