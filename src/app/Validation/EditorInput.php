<?php

declare(strict_types=1);

namespace Armasquads\Validation;

/**
 * Checks the username entered to add another account as editor of a squad.
 */
final class EditorInput
{
    /**
     * @param list<int> $matchingUserIds accounts with the entered username
     * @param list<int> $editorIds current editors of the squad
     */
    public static function validate(string $username, array $matchingUserIds, int $ownerId, array $editorIds): Validator
    {
        $validator = new Validator();

        if (Validator::isBlank($username)) {
            $validator->add('username', Validator::IS_EMPTY, 'FRONTEND_SQUAD_EDITOR_ENTER_USERNAME');
        } elseif ($matchingUserIds === []) {
            $validator->add('username', Validator::INVALID_VALUE, 'FRONTEND_SQUAD_EDITOR_UNKNOWN');
        } elseif (count($matchingUserIds) > 1) {
            $validator->add('username', Validator::INVALID_VALUE, 'FRONTEND_SQUAD_EDITOR_AMBIGUOUS');
        } elseif ($matchingUserIds[0] === $ownerId) {
            $validator->add('username', Validator::INVALID_VALUE, 'FRONTEND_SQUAD_EDITOR_IS_OWNER');
        } elseif (in_array($matchingUserIds[0], $editorIds, true)) {
            $validator->add('username', Validator::ALREADY_EXISTS, 'FRONTEND_SQUAD_EDITOR_ALREADY_ADDED');
        }

        return $validator;
    }
}
