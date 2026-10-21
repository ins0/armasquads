<?php

declare(strict_types=1);

namespace Armasquads\Tests;

use Armasquads\View;
use PHPUnit\Framework\TestCase;
use ReflectionClassConstant;

/**
 * Collects every text that is shown through the translator and checks that lang/de.php translates it.
 */
final class TranslationCoverageTest extends TestCase
{
    private const string ROOT = __DIR__ . '/..';

    private const string STRING = "'((?:[^'\\\\]|\\\\.)*)'";

    /** Calls whose first string argument is translated. */
    private const array PATTERNS = [
        '/->(?:t|translate|headTitle)\(\s*' . self::STRING . '/',
        '/->translatePlural\(\s*' . self::STRING . ',\s*' . self::STRING . '/',
        '/->flash\(\s*\'[a-z]+\',\s*' . self::STRING . '/',
        '/->add\(\s*\'[A-Za-z0-9]+\',\s*Validator::[A-Z_]+,\s*' . self::STRING . '/',
        '/\$errors(?:\[\])?\s*=\s*\[?\s*' . self::STRING . '/',
        '/return\s+\'([A-Z][A-Z0-9_]+)\'/',
    ];

    public function testEveryTranslatedTextHasAGermanTranslation(): void
    {
        $german = require self::ROOT . '/lang/de.php';
        $missing = array_values(array_filter(self::translatedTexts(), static fn (string $text): bool => !isset($german[$text])));

        self::assertSame([], $missing, "missing in lang/de.php:\n" . implode("\n", $missing));
    }

    public function testEveryTranslationKeyHasAnEnglishText(): void
    {
        $english = require self::ROOT . '/lang/en.php';
        $keys = array_filter(self::translatedTexts(), static fn (string $text): bool => preg_match('/^[A-Z][A-Z0-9]*_[A-Z0-9_]+$/', $text) === 1);
        $missing = array_values(array_filter($keys, static fn (string $key): bool => !isset($english[$key])));

        self::assertSame([], $missing, "missing in lang/en.php:\n" . implode("\n", $missing));
    }

    public function testCollectsTextsFromTemplatesAndCode(): void
    {
        $texts = self::translatedTexts();

        self::assertContains('Squad successfully created!', $texts, 'flash() in a controller');
        self::assertContains('Please enter a Squad Tag', $texts, 'Validator::add()');
        self::assertContains('Members', $texts, 'translatePlural() in a template');
        self::assertContains('Success', $texts, 'flash headings in View');
    }

    /**
     * @return list<string>
     */
    private static function translatedTexts(): array
    {
        $files = [
            ...glob(self::ROOT . '/templates/*.phtml') ?: [],
            ...glob(self::ROOT . '/templates/*/*.phtml') ?: [],
            ...glob(self::ROOT . '/app/*.php') ?: [],
            ...glob(self::ROOT . '/app/*/*.php') ?: [],
        ];

        $texts = [];
        foreach ($files as $file) {
            $source = (string) file_get_contents($file);
            foreach (self::PATTERNS as $pattern) {
                preg_match_all($pattern, $source, $matches);
                foreach (array_slice($matches, 1) as $group) {
                    array_push($texts, ...array_map(stripslashes(...), $group));
                }
            }
        }

        foreach ((new ReflectionClassConstant(View::class, 'FLASH_TYPES'))->getValue() as [, $heading]) {
            $texts[] = $heading;
        }

        return array_values(array_unique(array_filter($texts, static fn (string $text): bool => $text !== '')));
    }
}
