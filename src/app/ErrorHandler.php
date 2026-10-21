<?php

declare(strict_types=1);

namespace Armasquads;

use Armasquads\Middleware\ContextMiddleware;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

final readonly class ErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private ContextMiddleware $contextMiddleware,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
    ): ResponseInterface {
        $notFound = $exception instanceof HttpNotFoundException || $exception instanceof HttpMethodNotAllowedException;
        $status = $notFound ? 404 : 500;

        if (!$notFound && $logErrors) {
            error_log((string) $exception);
        }

        $response = $this->responseFactory->createResponse($status)->withHeader('Content-Type', 'text/html; charset=utf-8');

        try {
            $view = Context::of($this->contextMiddleware->withContext($request))->view;
            $response->getBody()->write($view->render($notFound ? 'error/404' : 'error/500', [
                'exception' => $displayErrorDetails ? $exception : null,
            ]));
        } catch (Throwable) {
            $response->getBody()->write($notFound ? 'Page not found' : 'An error occurred');
        }

        return $response;
    }
}
