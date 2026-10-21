<?php

declare(strict_types=1);

namespace Armasquads\Tests\Service;

use Armasquads\Service\LoginHandoff;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class LoginHandoffTest extends TestCase
{
    private const string IP = '203.0.113.7';

    private string $directory;
    private LoginHandoff $handoff;
    private DateTimeImmutable $now;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/armasquads-handoff-test-' . bin2hex(random_bytes(4));
        $this->handoff = new LoginHandoff($this->directory);
        $this->now = new DateTimeImmutable('2026-10-21 12:00:00');
    }

    protected function tearDown(): void
    {
        exec('rm -rf ' . escapeshellarg($this->directory));
    }

    public function testTokenLogsInTheIssuingUser(): void
    {
        $token = $this->handoff->issue(12065, self::IP, $this->now);

        self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
        self::assertSame(12065, $this->handoff->redeem($token, self::IP, $this->now->modify('+59 seconds')));
    }

    public function testTokenWorksOnlyOnce(): void
    {
        $token = $this->handoff->issue(12065, self::IP, $this->now);

        self::assertSame(12065, $this->handoff->redeem($token, self::IP, $this->now));
        self::assertNull($this->handoff->redeem($token, self::IP, $this->now));
    }

    public function testExpiredTokenIsRejectedAndRemoved(): void
    {
        $token = $this->handoff->issue(12065, self::IP, $this->now);

        self::assertNull($this->handoff->redeem($token, self::IP, $this->now->modify('+61 seconds')));
        self::assertNull($this->handoff->redeem($token, self::IP, $this->now), 'a failed attempt uses the token up');
    }

    public function testTokenFromAnotherIpIsRejectedAndRemoved(): void
    {
        $token = $this->handoff->issue(12065, self::IP, $this->now);

        self::assertNull($this->handoff->redeem($token, '198.51.100.1', $this->now));
        self::assertNull($this->handoff->redeem($token, self::IP, $this->now), 'a failed attempt uses the token up');
    }

    public function testRejectsUnknownAndMalformedTokens(): void
    {
        $this->handoff->issue(12065, self::IP, $this->now);

        self::assertNull($this->handoff->redeem(str_repeat('a', 64), self::IP, $this->now));
        self::assertNull($this->handoff->redeem('../../etc/passwd', self::IP, $this->now));
        self::assertNull($this->handoff->redeem('', self::IP, $this->now));
    }

    public function testTokenIsNotStoredOnDisk(): void
    {
        $token = $this->handoff->issue(12065, self::IP, $this->now);

        $files = glob($this->directory . '/*') ?: [];
        self::assertCount(1, $files);
        self::assertStringNotContainsString($token, basename($files[0]));
        self::assertStringNotContainsString($token, (string) file_get_contents($files[0]));
        self::assertSame('700', substr(sprintf('%o', fileperms($this->directory)), -3));
    }

    public function testIssuingRemovesStaleTokens(): void
    {
        $this->handoff->issue(12065, self::IP, $this->now);
        $stale = glob($this->directory . '/*') ?: [];
        touch($stale[0], time() - 3600);

        $this->handoff->issue(12065, self::IP, new DateTimeImmutable());

        self::assertFileDoesNotExist($stale[0]);
        self::assertCount(1, glob($this->directory . '/*') ?: []);
    }
}
