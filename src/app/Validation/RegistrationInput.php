<?php

declare(strict_types=1);

namespace Armasquads\Validation;

/**
 * Checks the registration form.
 */
final class RegistrationInput
{
    public const int USERNAME_MAX_LENGTH = 32;
    public const int MIN_FILL_SECONDS = 3;

    /**
     * @param array{username: string, email: string, password: string, password2: string} $values
     */
    public static function validate(array $values, bool $usernameExists, bool $emailExists): Validator
    {
        $validator = new Validator();

        if (Validator::isBlank($values['username'])) {
            $validator->add('username', Validator::IS_EMPTY, 'Choose a Username');
        } elseif (mb_strlen($values['username']) > self::USERNAME_MAX_LENGTH) {
            $validator->add('username', Validator::INVALID_VALUE, 'FRONTEND_REGISTER_USERNAME_TOO_LONG');
        } elseif (self::containsLink($values['username'])) {
            $validator->add('username', Validator::INVALID_VALUE, 'FRONTEND_REGISTER_USERNAME_LINK');
        } elseif ($usernameExists) {
            $validator->add('username', Validator::ALREADY_EXISTS, 'Username already exists!');
        }

        if (Validator::isBlank($values['email'])) {
            $validator->add('email', Validator::IS_EMPTY, 'Enter a valid Email');
        } elseif (!Validator::isEmail($values['email'])) {
            $validator->add('email', Validator::INVALID_EMAIL, 'Please enter a valid Email!');
        } elseif ($emailExists) {
            $validator->add('email', Validator::ALREADY_EXISTS, 'Account with this Email already exists!');
        }

        if (Validator::isBlank($values['password'])) {
            $validator->add('password', Validator::IS_EMPTY, 'Enter a Password');
        }

        if (Validator::isBlank($values['password2'])) {
            $validator->add('password2', Validator::IS_EMPTY, 'Please repeat your Password!');
        } elseif ($values['password2'] !== $values['password']) {
            $validator->add('password2', Validator::NOT_SAME, 'Both entered passwords are different!');
        }

        return $validator;
    }

    /**
     * Returns why a submission looks automated, or null. The form has a field "homepage" that people do not see, and
     * people need a few seconds to fill in the form, while bots fill in every field and submit right away.
     */
    public static function automationHint(string $homepage, ?int $formShownAt, int $now): ?string
    {
        if ($homepage !== '') {
            return 'hidden field filled in';
        }
        if ($formShownAt === null) {
            return 'form not shown in this session';
        }
        if ($now - $formShownAt < self::MIN_FILL_SECONDS) {
            return sprintf('submitted after %d seconds', $now - $formShownAt);
        }

        return null;
    }

    private static function containsLink(string $username): bool
    {
        return preg_match('~://|www\.|[a-z0-9-]\.[a-z]{2,}/~i', $username) === 1;
    }
}
