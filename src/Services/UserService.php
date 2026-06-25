<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use App\Validators\UserValidator;

/**
 * UserService — business logic untuk manajemen user CRUD.
 */
class UserService
{
    private UserValidator $validator;

    public function __construct()
    {
        $this->validator = new UserValidator();
    }

    /**
     * Get all users with roles.
     */
    public function getAll(): array
    {
        return User::all();
    }

    /**
     * Get user by ID with roles.
     */
    public function getById(int $id): ?array
    {
        return User::find($id);
    }

    /**
     * Create a new user. Returns user ID on success, or throws on validation error.
     *
     * @throws \RuntimeException
     */
    public function create(UserDTO $dto): string
    {
        if (!$this->validator->validate($dto, true)) {
            throw new \RuntimeException($this->validator->firstError());
        }

        $hashedPassword = password_hash($dto->password, PASSWORD_BCRYPT);
        $userId = User::create($dto->name, $dto->email, $hashedPassword, $dto->isActive);

        // Attach roles
        if (!empty($dto->roleIds)) {
            User::syncRoles((int)$userId, $dto->roleIds);
        }

        return $userId;
    }

    /**
     * Update an existing user. Returns affected row count.
     *
     * @throws \RuntimeException
     */
    public function update(int $id, UserDTO $dto): int
    {
        $this->validator->exceptUserId($id);

        if (!$this->validator->validate($dto, false)) {
            throw new \RuntimeException($this->validator->firstError());
        }

        $affected = User::update($id, $dto->name, $dto->email, $dto->isActive);

        // Update password if provided
        if ($dto->password !== null) {
            User::updatePassword($id, password_hash($dto->password, PASSWORD_BCRYPT));
        }

        // Sync roles
        User::syncRoles($id, $dto->roleIds);

        return $affected;
    }

    /**
     * Delete a user.
     */
    public function delete(int $id): int
    {
        return User::delete($id);
    }
}
