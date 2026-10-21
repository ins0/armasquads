<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Context;
use Armasquads\Model\ApiKey;
use Armasquads\Repository\ApiKeyRepository;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ApiDocsController extends Controller
{
    public function __construct(private readonly ApiKeyRepository $keys)
    {
    }

    public function public(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (Context::of($request)->user !== null) {
            return self::redirectToRoute($request, $response, 'user-api');
        }

        return self::render($request, $response, 'api/docs', [
            'public' => true,
            'userKey' => null,
            'nextRateReset' => ApiKey::nextRateReset(new DateTimeImmutable()),
        ]);
    }

    public function user(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $context = Context::of($request);
        $userId = $context->user()->id;
        $userKey = $this->keys->findByUser($userId);

        if ($userKey === null && ($request->getQueryParams()['getKey'] ?? null) === '1') {
            $this->keys->createForUser($userId);
            $userKey = $this->keys->findByUser($userId);
            $context->session->flash('success', 'Nice! Subscribe to API successfully. Can\'t wait to hear about your application');
        }

        return self::render($request, $response, 'api/docs', [
            'public' => false,
            'userKey' => $userKey,
            'nextRateReset' => ApiKey::nextRateReset(new DateTimeImmutable()),
        ]);
    }
}
