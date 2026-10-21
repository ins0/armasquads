<?php

declare(strict_types=1);

namespace Armasquads\Tests\Model;

use Armasquads\Model\ApiKey;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ApiKeyTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function rateResets(): iterable
    {
        yield 'within the hour' => ['2026-10-21 10:37:12', '2026-10-21 11:00:00'];
        yield 'last minute of the day' => ['2026-10-21 23:59:59', '2026-10-22 00:00:00'];
        yield 'full hour keeps the seconds' => ['2026-10-21 10:00:42', '2026-10-21 10:00:42'];
    }

    #[DataProvider('rateResets')]
    public function testNextRateResetIsTheNextFullHour(string $now, string $expected): void
    {
        self::assertSame($expected, ApiKey::nextRateReset(new DateTimeImmutable($now))->format('Y-m-d H:i:s'));
    }

    public function testRequestsResetToOneWhenTheLastRequestWasInAnEarlierHour(): void
    {
        $key = self::key(lastRequest: '2026-10-21 09:59:00', requests: 150);
        $key->resetRateIfWindowPassed(new DateTimeImmutable('2026-10-21 10:15:00'));

        self::assertSame(1, $key->requests);
        self::assertFalse($key->isLimitExceeded());
    }

    public function testRequestsStayWithinTheSameHour(): void
    {
        $key = self::key(lastRequest: '2026-10-21 10:01:00', requests: 101);
        $key->resetRateIfWindowPassed(new DateTimeImmutable('2026-10-21 10:15:00'));

        self::assertSame(101, $key->requests);
        self::assertTrue($key->isLimitExceeded());
        self::assertSame(0, $key->remainingRate());
    }

    public function testNewKeyIsNeverLimited(): void
    {
        $key = self::key(lastRequest: null, requests: 0);

        self::assertFalse($key->isLimitExceeded());
        self::assertSame(100, $key->remainingRate());
    }

    public function testRegisterRequestCountsAndStoresTheTime(): void
    {
        $key = self::key(lastRequest: null, requests: 0);
        $key->registerRequest(new DateTimeImmutable('2026-10-21 10:15:00'));

        self::assertSame(1, $key->requests);
        self::assertSame('2026-10-21 10:15:00', $key->lastRequest?->format('Y-m-d H:i:s'));
    }

    private static function key(?string $lastRequest, int $requests): ApiKey
    {
        return new ApiKey(1, 'key', 1, $lastRequest !== null ? new DateTimeImmutable($lastRequest) : null, $requests, ApiKey::DEFAULT_RATE_LIMIT, true);
    }
}
