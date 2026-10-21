<?php

declare(strict_types=1);

namespace Armasquads\Service;

use DateTimeImmutable;

/**
 * Keeps a user logged in when switching between armasquads.com and armasquads.de. The session cookie cannot cross
 * domains, so the current domain issues a one-time token that the other domain redeems. Both domains are served by
 * the same container, which is why the tokens can live in a local directory.
 */
final readonly class LoginHandoff
{
    public const int LIFETIME_SECONDS = 60;

    public function __construct(private string $directory)
    {
    }

    public function issue(int $userId, string $clientIp, DateTimeImmutable $now): string
    {
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0700, true);
        }
        $this->removeExpired($now);

        $token = bin2hex(random_bytes(32));
        file_put_contents($this->file($token), (string) json_encode([
            'user' => $userId,
            'ip' => $clientIp,
            'expires' => $now->getTimestamp() + self::LIFETIME_SECONDS,
        ]));

        return $token;
    }

    /**
     * Returns the user id once; the token is gone afterwards, even if it was expired or used from another IP.
     */
    public function redeem(string $token, string $clientIp, DateTimeImmutable $now): ?int
    {
        if (preg_match('/^[a-f0-9]{64}$/', $token) !== 1) {
            return null;
        }

        $file = $this->file($token);
        $claimed = $file . '.' . bin2hex(random_bytes(4));
        if (!@rename($file, $claimed)) {
            return null;
        }
        $data = json_decode((string) file_get_contents($claimed), true);
        unlink($claimed);

        if (!is_array($data) || ($data['expires'] ?? 0) < $now->getTimestamp() || ($data['ip'] ?? null) !== $clientIp) {
            return null;
        }

        return is_int($data['user'] ?? null) ? $data['user'] : null;
    }

    /**
     * The file name is a hash, so the token itself is never written to disk.
     */
    private function file(string $token): string
    {
        return $this->directory . '/' . hash('sha256', $token);
    }

    private function removeExpired(DateTimeImmutable $now): void
    {
        foreach (glob($this->directory . '/*') ?: [] as $file) {
            if (filemtime($file) < $now->getTimestamp() - self::LIFETIME_SECONDS) {
                @unlink($file);
            }
        }
    }
}
