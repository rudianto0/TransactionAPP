<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * User Data Transfer Object — untuk create/edit user.
 */
class UserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null,  // null if not changing
        public readonly bool $isActive = true,
        public readonly array $roleIds = [],
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name:     trim((string)($data['name'] ?? '')),
            email:    trim((string)($data['email'] ?? '')),
            password: isset($data['password']) && $data['password'] !== '' ? (string)$data['password'] : null,
            isActive: (bool)($data['is_active'] ?? false),
            roleIds:  array_map('intval', $data['role_ids'] ?? []),
        );
    }
}
