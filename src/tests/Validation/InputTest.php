<?php

declare(strict_types=1);

namespace Armasquads\Tests\Validation;

use Armasquads\Validation\EditorInput;
use Armasquads\Validation\MemberInput;
use Armasquads\Validation\RegistrationInput;
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

    public function testFlagsSquadTagsThatCanHideTheLogo(): void
    {
        foreach (['W.S.C.', 'ABC ', 'A/B', 'A\\B', 'A:B', 'A*B', 'A?B', 'A"B', 'A<B>', 'A|B'] as $tag) {
            self::assertTrue(SquadInput::isRiskyLogoTag($tag), $tag);
        }
        foreach (['WSC', 'F.O.O', '[ABC]', 'A-B', 'A B', '(SOC)', 'Ünited'] as $tag) {
            self::assertFalse(SquadInput::isRiskyLogoTag($tag), $tag);
        }
    }

    public function testEditorMustBeExactlyOneOtherAccountThatIsNotAddedYet(): void
    {
        $cases = [
            ['', [], [Validator::IS_EMPTY => 'FRONTEND_SQUAD_EDITOR_ENTER_USERNAME']],
            ['nobody', [], [Validator::INVALID_VALUE => 'FRONTEND_SQUAD_EDITOR_UNKNOWN']],
            ['twins', [7, 8], [Validator::INVALID_VALUE => 'FRONTEND_SQUAD_EDITOR_AMBIGUOUS']],
            ['owner', [1], [Validator::INVALID_VALUE => 'FRONTEND_SQUAD_EDITOR_IS_OWNER']],
            ['friend', [5], [Validator::ALREADY_EXISTS => 'FRONTEND_SQUAD_EDITOR_ALREADY_ADDED']],
        ];
        foreach ($cases as [$username, $matches, $error]) {
            self::assertSame(['username' => $error], EditorInput::validate($username, $matches, 1, [5])->errors(), $username);
        }

        self::assertTrue(EditorInput::validate('newfriend', [9], 1, [5])->isValid());
    }

    public function testRegistrationRequiresAllFields(): void
    {
        $validator = RegistrationInput::validate(['username' => ' ', 'email' => '', 'password' => '', 'password2' => ''], false, false);

        self::assertSame(['Choose a Username', 'Enter a valid Email', 'Enter a Password', 'Please repeat your Password!'], $validator->messages());
    }

    public function testRegistrationReportsTakenUsernameAndEmailAndDifferentPasswords(): void
    {
        $validator = RegistrationInput::validate(['username' => 'Neo', 'email' => 'neo@example.com', 'password' => 'a', 'password2' => 'b'], true, true);

        self::assertSame(['Username already exists!', 'Account with this Email already exists!', 'Both entered passwords are different!'], $validator->messages());
    }

    public function testRegistrationLimitsTheUsernameTo32Characters(): void
    {
        $values = ['email' => 'neo@example.com', 'password' => 'a', 'password2' => 'a'];

        self::assertTrue(RegistrationInput::validate(['username' => str_repeat('Ü', 32)] + $values, false, false)->isValid());
        self::assertSame(
            ['username' => [Validator::INVALID_VALUE => 'FRONTEND_REGISTER_USERNAME_TOO_LONG']],
            RegistrationInput::validate(['username' => str_repeat('a', 33)] + $values, false, false)->errors(),
        );
    }

    public function testRegistrationRejectsLinksInTheUsername(): void
    {
        $values = ['email' => 'neo@example.com', 'password' => 'a', 'password2' => 'a'];

        foreach (['READ IT https://example.com', 'Get telegra.ph/payout', 'www.example.com', 'twitch.tv/neo', 'ftp://host'] as $username) {
            self::assertSame(
                ['username' => [Validator::INVALID_VALUE => 'FRONTEND_REGISTER_USERNAME_LINK']],
                RegistrationInput::validate(['username' => $username] + $values, false, false)->errors(),
                $username,
            );
        }
        foreach (['Mr.Smith', 'F.O.O', '[GER] Hans', 'neo@example.com', 'Sgt. Pepper/2', '0516'] as $username) {
            self::assertTrue(RegistrationInput::validate(['username' => $username] + $values, false, false)->isValid(), $username);
        }
    }

    public function testRegistrationLooksAutomatedWhenTheHiddenFieldIsFilledOrTheFormWasSentTooFast(): void
    {
        $shownAt = 1_000_000;

        self::assertNull(RegistrationInput::automationHint('', $shownAt, $shownAt + RegistrationInput::MIN_FILL_SECONDS));
        self::assertSame('hidden field filled in', RegistrationInput::automationHint('https://example.com', $shownAt, $shownAt + 60));
        self::assertSame('form not shown in this session', RegistrationInput::automationHint('', null, $shownAt));
        self::assertSame('submitted after 2 seconds', RegistrationInput::automationHint('', $shownAt, $shownAt + 2));
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
