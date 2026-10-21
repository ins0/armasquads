<?php

declare(strict_types=1);

namespace Armasquads\Model;

use DateTimeImmutable;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email,
        public string $passwordHash,
        public bool $disabled,
        public ?DateTimeImmutable $lastLogin,
        public ?DateTimeImmutable $registerDate,
    ) {
    }

    public function gravatar(int $size = 80): string
    {
        return 'https://secure.gravatar.com/avatar/' . md5($this->email) . '?r=g&d=mm&s=' . $size;
    }
}
