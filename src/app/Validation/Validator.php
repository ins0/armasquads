<?php

declare(strict_types=1);

namespace Armasquads\Validation;

/**
 * Collects errors per field as [field => [code => message]]. The codes are the ones Zend\Validator used, because the
 * REST API returns this structure to its clients.
 */
final class Validator
{
    public const string IS_EMPTY = 'isEmpty';
    public const string INVALID_EMAIL = 'emailAddressInvalidFormat';
    public const string INVALID_URI = 'notUri';
    public const string NOT_SAME = 'notSame';
    public const string ALREADY_EXISTS = 'objectFound';
    public const string WRONG_EXTENSION = 'fileExtensionFalse';
    public const string INVALID_VALUE = 'callbackValue';

    /** @var array<string, array<string, string>> */
    private array $errors = [];

    /**
     * Empty means missing, only whitespace or "0", like the default of Zend\Validator\NotEmpty.
     */
    public static function isBlank(?string $value): bool
    {
        return $value === null || trim($value) === '' || $value === '0';
    }

    public static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isAbsoluteUri(string $value): bool
    {
        $parts = parse_url($value);

        return is_array($parts)
            && isset($parts['scheme'])
            && preg_match('/^[a-z][a-z0-9+.-]*$/i', $parts['scheme']) === 1
            && (isset($parts['host']) || isset($parts['path']));
    }

    public function add(string $field, string $code, string $message): void
    {
        $this->errors[$field][$code] = $message;
    }

    public function has(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return list<string>
     */
    public function messages(): array
    {
        return array_values(array_merge(...array_values(array_map(array_values(...), $this->errors))));
    }
}
