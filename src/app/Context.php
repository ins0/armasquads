<?php

declare(strict_types=1);

namespace Armasquads;

use Armasquads\Model\User;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Per request state of the website (not used by squad files and the API): session, language, logged in user, view.
 */
final readonly class Context
{
    public function __construct(
        public Session $session,
        public Translator $translator,
        public ?User $user,
        public View $view,
    ) {
    }

    public static function of(ServerRequestInterface $request): self
    {
        $context = $request->getAttribute(self::class);
        if (!$context instanceof self) {
            throw new \LogicException('The request has no context, the route is missing the ContextMiddleware.');
        }

        return $context;
    }

    public function user(): User
    {
        return $this->user ?? throw new \LogicException('No user is logged in.');
    }
}
