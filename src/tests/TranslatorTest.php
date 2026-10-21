<?php

declare(strict_types=1);

namespace Armasquads\Tests;

use Armasquads\Translator;
use PHPUnit\Framework\TestCase;

final class TranslatorTest extends TestCase
{
    private const string LANGUAGE_PATH = __DIR__ . '/../lang';

    public function testGermanForDeDomains(): void
    {
        $translator = Translator::forHost('armasquads.de', self::LANGUAGE_PATH);

        self::assertSame('de_DE', $translator->locale());
        self::assertSame('<span class="fa fa-sign-out"></span> Abmelden', $translator->translate('FRONTEND_LOGIN_NAV_LOGOUT'));
    }

    public function testEnglishForAllOtherDomains(): void
    {
        self::assertSame('en_EN', Translator::forHost('armasquads.com', self::LANGUAGE_PATH)->locale());
        self::assertSame('en_EN', Translator::forHost('localhost', self::LANGUAGE_PATH)->locale());
    }

    public function testUnknownMessagesStayAsTheyAre(): void
    {
        self::assertSame('My Squads', Translator::forHost('armasquads.de', self::LANGUAGE_PATH)->translate('My Squads'));
    }

    public function testPlural(): void
    {
        $translator = Translator::forHost('armasquads.com', self::LANGUAGE_PATH);

        self::assertSame('Member', $translator->translatePlural('Member', 'Members', 1));
        self::assertSame('Members', $translator->translatePlural('Member', 'Members', 0));
        self::assertSame('Members', $translator->translatePlural('Member', 'Members', 2));
    }

    public function testBothLanguagesHaveTheSameKeys(): void
    {
        $german = array_keys(require self::LANGUAGE_PATH . '/de.php');
        $english = array_keys(require self::LANGUAGE_PATH . '/en.php');

        self::assertSame([], array_values(array_diff($english, $german)), 'keys missing in de.php');
    }
}
