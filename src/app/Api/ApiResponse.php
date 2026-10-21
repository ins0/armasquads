<?php

declare(strict_types=1);

namespace Armasquads\Api;

/**
 * Result of an API action, rendered as {"success": true, "results": ...} or {"success": false, "error": [...]}.
 */
final readonly class ApiResponse
{
    /**
     * @param string|array<string, mixed>|null $error
     */
    public function __construct(
        public mixed $result = null,
        public string|array|null $error = null,
        public int $status = 200,
    ) {
    }

    public static function error(string|array $error, int $status): self
    {
        return new self(null, $error, $status);
    }

    public function hasError(): bool
    {
        return (bool) $this->error;
    }

    public function body(): string
    {
        if ($this->hasError()) {
            return (string) json_encode(['success' => false, 'error' => is_array($this->error) ? $this->error : [$this->error]]);
        }

        return (string) json_encode(['success' => true, 'results' => $this->result]);
    }
}
