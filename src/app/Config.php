<?php

declare(strict_types=1);

namespace Armasquads;

final readonly class Config
{
    public function __construct(
        public string $dbHost,
        public string $dbUser,
        public string $dbPassword,
        public string $dbName,
        public string $publicPath,
        public string $templatePath,
        public string $languagePath,
        public bool $displayErrors = false,
        public string $armakeBinary = 'armake',
    ) {
    }

    public static function fromEnvironment(string $appRoot): self
    {
        return new self(
            dbHost: self::env('MYSQL_HOST', 'localhost'),
            dbUser: self::env('MYSQL_USER'),
            dbPassword: self::env('MYSQL_PWD'),
            dbName: self::env('MYSQL_DB'),
            publicPath: $appRoot . '/public',
            templatePath: $appRoot . '/templates',
            languagePath: $appRoot . '/lang',
            displayErrors: self::env('APP_DISPLAY_ERRORS') === '1',
            armakeBinary: self::env('ARMAKE_BINARY', 'armake'),
        );
    }

    private static function env(string $name, string $default = ''): string
    {
        $value = $_SERVER[$name] ?? getenv($name);

        return is_string($value) && $value !== '' ? $value : $default;
    }
}
