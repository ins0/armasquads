<?php

declare(strict_types=1);

namespace Armasquads\Repository;

use Armasquads\Model\ApiKey;
use PDO;

final readonly class ApiKeyRepository
{
    private const string KEY_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    public function __construct(private PDO $db)
    {
    }

    public function findByKey(string $key): ?ApiKey
    {
        return $this->fetchOne('SELECT * FROM api_keys_s83dks WHERE KEY_Value = ? LIMIT 1', $key);
    }

    public function findByUser(int $userId): ?ApiKey
    {
        return $this->fetchOne('SELECT * FROM api_keys_s83dks WHERE BEN_ID = ? LIMIT 1', $userId);
    }

    public function createForUser(int $userId): void
    {
        $key = '';
        for ($i = 0; $i < 50; $i++) {
            $key .= self::KEY_CHARS[random_int(0, strlen(self::KEY_CHARS) - 1)];
        }

        $this->db->prepare(
            'INSERT INTO api_keys_s83dks (KEY_Value, KEY_Status, KEY_Limit, KEY_LastRequest, KEY_Requests, BEN_ID) VALUES (?, 1, ?, NULL, 0, ?)'
        )->execute([$key, ApiKey::DEFAULT_RATE_LIMIT, $userId]);
    }

    public function saveUsage(ApiKey $apiKey): void
    {
        $this->db->prepare('UPDATE api_keys_s83dks SET KEY_Requests = ?, KEY_LastRequest = ? WHERE KEY_ID = ?')
            ->execute([$apiKey->requests, $apiKey->lastRequest?->format('Y-m-d H:i:s'), $apiKey->id]);
    }

    private function fetchOne(string $sql, string|int $value): ?ApiKey
    {
        $statement = $this->db->prepare($sql);
        $statement->execute([$value]);
        $row = $statement->fetch();

        return $row ? ApiKey::fromRow($row) : null;
    }
}
