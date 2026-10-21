<?php

declare(strict_types=1);

namespace Armasquads\Tests\Service;

use Armasquads\Service\Passwords;
use PHPUnit\Framework\TestCase;

final class PasswordsTest extends TestCase
{
    /** Created by Zend\Crypt\Password\Bcrypt with a Pbkdf2 salt, like Benutzer::setPassword() did. */
    private const string ZEND_HASH = '$2y$10$RiiORCZK4oVq7DHT/N6R2ujkp3M9dPjAnc4RXoJM7o5FZ5oMyC00W';

    public function testVerifiesHashesCreatedByZendCrypt(): void
    {
        self::assertTrue(Passwords::verify('secret123', self::ZEND_HASH));
        self::assertFalse(Passwords::verify('secret1234', self::ZEND_HASH));
        self::assertFalse(Passwords::isLegacyHash(self::ZEND_HASH));
    }

    public function testVerifiesLegacyMd5Hashes(): void
    {
        $hash = md5('legacy1');

        self::assertTrue(Passwords::isLegacyHash($hash));
        self::assertTrue(Passwords::verify('legacy1', $hash));
        self::assertFalse(Passwords::verify('legacy2', $hash));
    }

    public function testCreatesBcryptHashesWithTheSameFormatAsBefore(): void
    {
        $hash = Passwords::hash('hunter22');

        self::assertStringStartsWith('$2y$10$', $hash);
        self::assertSame(60, strlen($hash));
        self::assertTrue(Passwords::verify('hunter22', $hash));
        self::assertSame($hash, crypt('hunter22', $hash), 'Zend\Crypt verifies with crypt(), so old code can still log in');
    }
}
