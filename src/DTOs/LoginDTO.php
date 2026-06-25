<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Login Data Transfer Object
 */
class LoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            email:    trim((string)($data['email'] ?? '')),
            password: (string)($data['password'] ?? ''),
        );
    }
}
