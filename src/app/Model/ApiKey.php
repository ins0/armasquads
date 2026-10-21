<?php

declare(strict_types=1);

namespace Armasquads\Model;

use DateTimeImmutable;

final class ApiKey
{
    public const int DEFAULT_RATE_LIMIT = 100;

    public function __construct(
        public readonly int $id,
        public readonly string $key,
        public readonly int $userId,
        public ?DateTimeImmutable $lastRequest,
        public int $requests,
        public readonly int $limit,
        public readonly bool $active,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['KEY_ID'],
            key: (string) $row['KEY_Value'],
            userId: (int) $row['BEN_ID'],
            lastRequest: $row['KEY_LastRequest'] !== null ? new DateTimeImmutable($row['KEY_LastRequest']) : null,
            requests: (int) $row['KEY_Requests'],
            limit: (int) $row['KEY_Limit'],
            active: (bool) $row['KEY_Status'],
        );
    }

    /**
     * The limit window always ends at the next full hour.
     */
    public static function nextRateReset(DateTimeImmutable $now): DateTimeImmutable
    {
        $minutes = (int) $now->format('i');
        $seconds = (int) $now->format('s');

        if ($minutes > 0) {
            return $now->modify('+1 hour')->modify("-$minutes minutes")->modify("-$seconds seconds");
        }

        return $now;
    }

    public function resetRateIfWindowPassed(DateTimeImmutable $now): void
    {
        $windowStart = self::nextRateReset($now)->modify('-1 hour');

        if ($this->lastRequest !== null && $this->lastRequest->getTimestamp() <= $windowStart->getTimestamp()) {
            $this->requests = 1;
        }
    }

    public function isLimitExceeded(): bool
    {
        if ($this->lastRequest === null || $this->requests === 0) {
            return false;
        }

        return $this->requests > $this->limit;
    }

    public function remainingRate(): int
    {
        return max(0, $this->limit - $this->requests);
    }

    public function registerRequest(DateTimeImmutable $now): void
    {
        $this->lastRequest = $now;
        $this->requests++;
    }
}
