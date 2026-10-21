<?php

declare(strict_types=1);

namespace Armasquads\Model;

final class Squad
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $privateId,
        public ?string $logo,
        public string $tag,
        public string $name,
        public ?string $title,
        public ?string $homepage,
        public ?string $email,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['SQA_ID'],
            userId: (int) $row['BEN_ID'],
            privateId: (string) $row['SQA_PrivateID'],
            logo: $row['SQA_Logo'],
            tag: (string) $row['SQA_Tag'],
            name: (string) $row['SQA_Name'],
            title: $row['SQA_Title'],
            homepage: $row['SQA_Homepage'],
            email: $row['SQA_Email'],
        );
    }
}
