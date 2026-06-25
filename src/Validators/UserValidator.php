<?php

declare(strict_types=1);

namespace App\Validators;

use App\DTOs\UserDTO;
use App\Models\User;

/**
 * Validator untuk input create/edit user.
 */
class UserValidator
{
    private array $errors = [];
    private ?int $exceptUserId = null; // for update: exclude this user from unique check

    /**
     * Set the user ID to exclude from unique email check (for updates).
     */
    public function exceptUserId(?int $id): self
    {
        $this->exceptUserId = $id;
        return $this;
    }

    /**
     * Validate user input. Returns true if valid.
     */
    public function validate(UserDTO $dto, bool $isCreate = true): bool
    {
        $this->errors = [];

        // Name
        if ($dto->name === '') {
            $this->errors[] = 'Nama wajib diisi.';
        } elseif (strlen($dto->name) > 100) {
            $this->errors[] = 'Nama maksimal 100 karakter.';
        }

        // Email
        if ($dto->email === '') {
            $this->errors[] = 'Email wajib diisi.';
        } elseif (!filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Format email tidak valid.';
        } else {
            // Check uniqueness
            $existing = User::findByEmail($dto->email);
            if ($existing && ($this->exceptUserId === null || (int)$existing['id'] !== $this->exceptUserId)) {
                $this->errors[] = 'Email sudah digunakan oleh user lain.';
            }
        }

        // Password
        if ($isCreate) {
            if ($dto->password === null || strlen($dto->password) < 6) {
                $this->errors[] = 'Password minimal 6 karakter (wajib saat membuat user).';
            }
        } else {
            // Update: optional, but if provided must be >= 6
            if ($dto->password !== null && strlen($dto->password) < 6) {
                $this->errors[] = 'Password minimal 6 karakter.';
            }
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error message.
     */
    public function firstError(): string
    {
        return $this->errors[0] ?? 'Unknown error';
    }
}
