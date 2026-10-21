<?php

declare(strict_types=1);

namespace Armasquads;

final class Translator
{
    private const array LOCALES = ['de' => 'de_DE', 'en' => 'en_EN'];

    /**
     * @param array<string, string> $messages
     */
    public function __construct(
        private readonly string $locale,
        private readonly array $messages,
    ) {
    }

    /**
     * German for every host containing ".de" (armasquads.de), English otherwise.
     */
    public static function forHost(string $host, string $languagePath): self
    {
        $language = str_contains($host, '.de') ? 'de' : 'en';

        return new self(self::LOCALES[$language], require $languagePath . '/' . $language . '.php');
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function translate(string $message): string
    {
        return $this->messages[$message] ?? $message;
    }

    public function translatePlural(string $singular, string $plural, int $count): string
    {
        return $this->translate($count === 1 ? $singular : $plural);
    }
}
