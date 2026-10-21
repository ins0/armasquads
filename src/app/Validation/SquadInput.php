<?php

declare(strict_types=1);

namespace Armasquads\Validation;

use Armasquads\Service\LogoService;

/**
 * Squad fields as entered in the web form or sent to the REST API.
 */
final readonly class SquadInput
{
    public function __construct(
        public ?string $tag,
        public ?string $name,
        public ?string $title,
        public ?string $homepage,
        public ?string $email,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            tag: self::string($data, 'tag'),
            name: self::string($data, 'name'),
            title: self::string($data, 'title'),
            homepage: self::string($data, 'homepage'),
            email: self::string($data, 'email'),
        );
    }

    /**
     * @param ?array{file: string, name: string} $logo uploaded image and its original file name
     */
    public function validate(?array $logo): Validator
    {
        $validator = new Validator();

        if (Validator::isBlank($this->tag)) {
            $validator->add('tag', Validator::IS_EMPTY, 'Please enter a Squad Tag');
        }
        if (Validator::isBlank($this->name)) {
            $validator->add('name', Validator::IS_EMPTY, 'Please enter a Squad Name');
        }
        if ($this->homepage !== null && $this->homepage !== '' && !Validator::isAbsoluteUri($this->homepage)) {
            $validator->add('homepage', Validator::INVALID_URI, 'FRONTEND_SQUAD_URL_INVALID_FORMAT');
        }
        if ($this->email !== null && $this->email !== '' && !Validator::isEmail($this->email)) {
            $validator->add('email', Validator::INVALID_EMAIL, 'FRONTEND_SQUAD_EMAIL_INVALID_FORMAT');
        }

        if ($logo !== null) {
            if (!LogoService::hasAllowedExtension($logo['name'])) {
                $validator->add('logo', Validator::WRONG_EXTENSION, 'FRONTEND_SQUAD_LOGO_INVALID_EXTENSION');
            } elseif (!LogoService::hasAllowedDimensions($logo['file'])) {
                $validator->add('logo', Validator::INVALID_VALUE, 'FRONTEND_SQUAD_LOGO_INVALID_FORMAT');
            }
        }

        return $validator;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function string(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }
}
