<?php

declare(strict_types=1);

namespace Armasquads\Repository;

use Armasquads\Model\User;
use DateTimeImmutable;
use PDO;

final readonly class UserRepository
{
    private const int ROLE_USER = 2;

    public function __construct(private PDO $db)
    {
    }

    public function find(int $id): ?User
    {
        return $this->fetchOne('SELECT * FROM ben_benutzer_91c48c WHERE BEN_ID = ?', [$id]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->fetchOne('SELECT * FROM ben_benutzer_91c48c WHERE BEN_Email = ? LIMIT 1', [$email]);
    }

    public function findLatest(): ?User
    {
        return $this->fetchOne('SELECT * FROM ben_benutzer_91c48c ORDER BY BEN_ID DESC LIMIT 1', []);
    }

    /**
     * @return list<int> all accounts with this username, old accounts can share one
     */
    public function idsByUsername(string $username): array
    {
        $statement = $this->db->prepare('SELECT BEN_ID FROM ben_benutzer_91c48c WHERE BEN_Username = ?');
        $statement->execute([$username]);

        return array_map(intval(...), $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * @param list<int> $ids
     * @return array<int, string> usernames by user id
     */
    public function usernames(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = $this->db->prepare("SELECT BEN_ID, BEN_Username FROM ben_benutzer_91c48c WHERE BEN_ID IN ($placeholders)");
        $statement->execute($ids);

        return array_map(strval(...), $statement->fetchAll(PDO::FETCH_KEY_PAIR));
    }

    public function usernameExists(string $username): bool
    {
        return $this->exists('SELECT 1 FROM ben_benutzer_91c48c WHERE BEN_Username = ? LIMIT 1', $username);
    }

    public function emailExists(string $email): bool
    {
        return $this->exists('SELECT 1 FROM ben_benutzer_91c48c WHERE BEN_Email = ? LIMIT 1', $email);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(BEN_ID) FROM ben_benutzer_91c48c')->fetchColumn();
    }

    public function create(string $username, string $email, string $passwordHash, DateTimeImmutable $registerDate): int
    {
        $this->db->beginTransaction();

        $this->db->prepare(
            'INSERT INTO ben_benutzer_91c48c (BEN_Username, BEN_Password, BEN_Email, BEN_Disabled, BEN_LastLogin, BEN_Register)'
            . ' VALUES (?, ?, ?, 0, NULL, ?)'
        )->execute([$username, $passwordHash, $email, $registerDate->format('Y-m-d H:i:s')]);
        $id = (int) $this->db->lastInsertId();

        $this->db->prepare('INSERT INTO auth_user_role (user_id, role_id) VALUES (?, ?)')->execute([$id, self::ROLE_USER]);

        $this->db->commit();

        return $id;
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        $this->db->prepare('UPDATE ben_benutzer_91c48c SET BEN_Password = ? WHERE BEN_ID = ?')->execute([$passwordHash, $id]);
    }

    public function updateLastLogin(int $id, DateTimeImmutable $lastLogin): void
    {
        $this->db->prepare('UPDATE ben_benutzer_91c48c SET BEN_LastLogin = ? WHERE BEN_ID = ?')
            ->execute([$lastLogin->format('Y-m-d H:i:s'), $id]);
    }

    /**
     * @param list<mixed> $params
     */
    private function fetchOne(string $sql, array $params): ?User
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();

        return $row ? self::hydrate($row) : null;
    }

    private function exists(string $sql, string $value): bool
    {
        $statement = $this->db->prepare($sql);
        $statement->execute([$value]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function hydrate(array $row): User
    {
        return new User(
            id: (int) $row['BEN_ID'],
            username: (string) $row['BEN_Username'],
            email: (string) $row['BEN_Email'],
            passwordHash: (string) $row['BEN_Password'],
            disabled: (bool) $row['BEN_Disabled'],
            lastLogin: self::date($row['BEN_LastLogin']),
            registerDate: self::date($row['BEN_Register']),
        );
    }

    private static function date(?string $value): ?DateTimeImmutable
    {
        if ($value === null || str_starts_with($value, '0000-00-00')) {
            return null;
        }

        return new DateTimeImmutable($value);
    }
}
