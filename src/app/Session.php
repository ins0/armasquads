<?php

declare(strict_types=1);

namespace Armasquads;

final class Session
{
    private const string USER_KEY = 'user_id';
    private const string FLASH_KEY = 'flash';
    private const string CSRF_KEY = 'csrf';

    public static function start(bool $secure): self
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => $secure,
                'cookie_samesite' => 'Lax',
                'use_strict_mode' => true,
            ]);
        }

        return new self();
    }

    public function userId(): ?int
    {
        $id = $_SESSION[self::USER_KEY] ?? null;

        return is_int($id) ? $id : null;
    }

    public function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION[self::USER_KEY] = $userId;
    }

    public function logout(): void
    {
        unset($_SESSION[self::USER_KEY]);
        session_regenerate_id(true);
    }

    public function flash(string $type, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$type][] = $message;
    }

    /**
     * @return array<string, list<string>>
     */
    public function takeFlashes(): array
    {
        $flashes = $_SESSION[self::FLASH_KEY] ?? [];
        unset($_SESSION[self::FLASH_KEY]);

        return $flashes;
    }

    public function csrfToken(): string
    {
        if (!isset($_SESSION[self::CSRF_KEY]) || !is_string($_SESSION[self::CSRF_KEY])) {
            $_SESSION[self::CSRF_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CSRF_KEY];
    }

    public function isValidCsrfToken(mixed $token): bool
    {
        return is_string($token) && hash_equals($this->csrfToken(), $token);
    }
}
