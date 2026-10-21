<?php

declare(strict_types=1);

namespace Armasquads\Model;

final readonly class Member
{
    public function __construct(
        public string $uuid,
        public ?string $username,
        public ?string $name,
        public ?string $email,
        public ?string $icq,
        public ?string $remark,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            uuid: (string) $row['MEM_UID'],
            username: $row['MEM_Username'],
            name: $row['MEM_Name'],
            email: $row['MEM_Email'],
            icq: $row['MEM_ICQ'],
            remark: $row['MEM_Remark'],
        );
    }

    /**
     * @return array{uuid: string, username: ?string, name: ?string, email: ?string, icq: ?string, remark: ?string}
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'icq' => $this->icq,
            'remark' => $this->remark,
        ];
    }
}
