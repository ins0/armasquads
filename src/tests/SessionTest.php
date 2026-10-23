<?php

declare(strict_types=1);

namespace Armasquads\Tests;

use Armasquads\Session;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testRegisterFormKeepsTheTimeItWasFirstShownUntilItIsForgotten(): void
    {
        $session = new Session();
        self::assertNull($session->registerFormShownAt());

        $session->markRegisterFormShown(100);
        $session->markRegisterFormShown(105);
        self::assertSame(100, $session->registerFormShownAt());

        $session->forgetRegisterFormShown();
        self::assertNull($session->registerFormShownAt());

        $session->markRegisterFormShown(110);
        self::assertSame(110, $session->registerFormShownAt());
    }
}
