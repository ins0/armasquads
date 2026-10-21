<?php

declare(strict_types=1);

namespace Armasquads\Tests\Validation;

use Armasquads\Validation\MemberInput;
use Armasquads\Validation\SquadInput;
use Armasquads\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class InputTest extends TestCase
{
    public function testBlankFollowsZendNotEmpty(): void
    {
        self::assertTrue(Validator::isBlank(null));
        self::assertTrue(Validator::isBlank(''));
        self::assertTrue(Validator::isBlank("  \t"));
        self::assertTrue(Validator::isBlank('0'));
        self::assertFalse(Validator::isBlank('00'));
        self::assertFalse(Validator::isBlank('a'));
    }

    public function testSquadRequiresTagAndName(): void
    {
        $validator = SquadInput::fromArray(['tag' => '', 'name' => ' '])->validate(null);

        self::assertSame([
            'tag' => [Validator::IS_EMPTY => 'Please enter a Squad Tag'],
            'name' => [Validator::IS_EMPTY => 'Please enter a Squad Name'],
        ], $validator->errors());
    }

    public function testSquadChecksOptionalHomepageAndEmailOnlyWhenFilled(): void
    {
        self::assertTrue(SquadInput::fromArray(['tag' => 'T', 'name' => 'N', 'homepage' => '', 'email' => ''])->validate(null)->isValid());
        self::assertTrue(SquadInput::fromArray(['tag' => 'T', 'name' => 'N', 'homepage' => 'https://example.com/a', 'email' => 'a@example.com'])->validate(null)->isValid());

        $validator = SquadInput::fromArray(['tag' => 'T', 'name' => 'N', 'homepage' => 'example.com', 'email' => 'no mail'])->validate(null);

        self::assertSame(['homepage', 'email'], array_keys($validator->errors()));
        self::assertArrayHasKey(Validator::INVALID_URI, $validator->errors()['homepage']);
        self::assertArrayHasKey(Validator::INVALID_EMAIL, $validator->errors()['email']);
    }

    public function testSquadRejectsUnsupportedLogoExtension(): void
    {
        $validator = SquadInput::fromArray(['tag' => 'T', 'name' => 'N'])->validate(['file' => '/dev/null', 'name' => 'logo.bmp']);

        self::assertSame(['logo' => [Validator::WRONG_EXTENSION => 'FRONTEND_SQUAD_LOGO_INVALID_EXTENSION']], $validator->errors());
    }

    public function testSquadKeepsMissingFieldsAsNull(): void
    {
        $input = SquadInput::fromArray(['tag' => 'T', 'name' => 7, 'title' => ['array']]);

        self::assertSame('7', $input->name);
        self::assertNull($input->title);
        self::assertNull($input->homepage);
    }

    public function testMemberRequiresPlayerIdAndUsername(): void
    {
        $validator = MemberInput::validate(['uuid' => '', 'email' => 'x']);

        self::assertSame(['PlayerID required!', 'Username required!', 'Please enter a valid Email!'], $validator->messages());
    }

    public function testMemberToModel(): void
    {
        $member = MemberInput::toMember(['uuid' => '42', 'username' => 'Neo', 'icq' => 0]);

        self::assertSame(['uuid' => '42', 'username' => 'Neo', 'name' => null, 'email' => null, 'icq' => '0', 'remark' => null], $member->toArray());
    }
}
