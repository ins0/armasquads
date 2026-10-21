<?php

declare(strict_types=1);

namespace Armasquads\Validation;

use Armasquads\Model\Member;

final class MemberInput
{
    /**
     * @param array<string, mixed> $data
     */
    public static function validate(array $data): Validator
    {
        $validator = new Validator();

        if (Validator::isBlank(self::string($data, 'uuid'))) {
            $validator->add('uuid', Validator::IS_EMPTY, 'PlayerID required!');
        }
        if (Validator::isBlank(self::string($data, 'username'))) {
            $validator->add('username', Validator::IS_EMPTY, 'Username required!');
        }

        $email = self::string($data, 'email');
        if ($email !== null && $email !== '' && !Validator::isEmail($email)) {
            $validator->add('email', Validator::INVALID_EMAIL, 'Please enter a valid Email!');
        }

        return $validator;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function toMember(array $data): Member
    {
        return new Member(
            uuid: (string) self::string($data, 'uuid'),
            username: self::string($data, 'username'),
            name: self::string($data, 'name'),
            email: self::string($data, 'email'),
            icq: self::string($data, 'icq'),
            remark: self::string($data, 'remark'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function string(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }
}
