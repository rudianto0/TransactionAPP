<?php

declare(strict_types=1);

namespace App\Validators;

use App\DTOs\LoginDTO;

/**
 * Validator untuk input login.
 */
class LoginValidator
{
    private array $errors = [];

    /**
     * Validate login input. Returns true if valid, false otherwise.
     */
    public function validate(LoginDTO $dto): bool
    {
        $this->errors = [];

        if ($dto->email === '') {
            $this->errors[] = 'Email wajib diisi.';
        } elseif (!filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Format email tidak valid.';
        }

        if ($dto->password === '') {
            $this->errors[] = 'Password wajib diisi.';
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
