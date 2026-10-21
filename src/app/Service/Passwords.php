<?php

declare(strict_types=1);

namespace Armasquads\Service;

/**
 * Accounts store bcrypt hashes ($2y$10$, created by Zend\Crypt). A few very old accounts still have an unsalted md5
 * hash, which is replaced by a bcrypt hash on the next successful login.
 */
final class Passwords
{
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    public static function verify(string $password, string $hash): bool
    {
        if (self::isLegacyHash($hash)) {
            return hash_equals($hash, md5($password));
        }

        return password_verify($password, $hash);
    }

    public static function isLegacyHash(string $hash): bool
    {
        return preg_match('/^[0-9a-f]{32}$/', $hash) === 1;
    }
}
