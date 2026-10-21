<?php

declare(strict_types=1);

namespace Armasquads\Controller;

use Armasquads\Context;
use Armasquads\Repository\UserRepository;
use Armasquads\Service\LoginHandoff;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Switches between armasquads.de (German) and armasquads.com (English) and keeps the user logged in.
 */
final class LanguageController extends Controller
{
    private const array TOP_LEVEL_DOMAINS = ['de' => 'de', 'en' => 'com'];

    public function __construct(
        private readonly LoginHandoff $handoff,
        private readonly UserRepository $users,
    ) {
    }

    /**
     * @param array{language: string} $args
     */
    public function switch(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $context = Context::of($request);
        $path = self::localPath($request->getQueryParams()['path'] ?? null) ?? '/';
        $target = $context->view->serverUrl(self::TOP_LEVEL_DOMAINS[$args['language']]);

        if ($context->user === null) {
            return self::redirect($response, $target . $path);
        }

        $token = $this->handoff->issue($context->user->id, self::clientIp($request), new DateTimeImmutable());

        return self::redirect($response, $target . $context->view->url('language-handoff', [], ['token' => $token, 'path' => $path]));
    }

    public function handoff(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $context = Context::of($request);
        $query = $request->getQueryParams();
        $path = self::localPath($query['path'] ?? null) ?? '/';

        $userId = is_string($query['token'] ?? null)
            ? $this->handoff->redeem($query['token'], self::clientIp($request), new DateTimeImmutable())
            : null;
        $user = $userId !== null ? $this->users->find($userId) : null;

        if ($user !== null && !$user->disabled) {
            $context->session->login($user->id);
        }

        return self::redirect($response, $path);
    }
}
