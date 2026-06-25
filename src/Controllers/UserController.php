<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\DTOs\UserDTO;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Services\RoleService;
use App\Services\UserService;

/**
 * UserController — menangani CRUD User.
 * Hanya bisa diakses oleh user dengan permission "manage-users".
 */
class UserController extends Controller
{
    private UserService $userService;
    private RoleService $roleService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->roleService = new RoleService();
    }

    /**
     * GET /users — Tampilkan tabel daftar user.
     */
    public function index(): void
    {
        $users = $this->userService->getAll();

        $this->view('users/index', [
            'title' => 'Users — TransactionAPP',
            'users' => $users,
        ]);
    }

    /**
     * GET /users/create — Tampilkan form tambah user.
     */
    public function create(): void
    {
        $roles = $this->roleService->all();

        $this->view('users/create', [
            'title' => 'Add User — TransactionAPP',
            'roles' => $roles,
        ]);
    }

    /**
     * POST /users — Simpan user baru.
     */
    public function store(): void
    {
        try {
            $dto = UserDTO::fromRequest(Request::all());
            $userId = $this->userService->create($dto);

            Flash::success('User berhasil ditambahkan.');
            Redirect::to('/users');
        } catch (\RuntimeException $e) {
            Flash::error($e->getMessage());
            Redirect::back();
        }
    }

    /**
     * GET /users/{id}/edit — Tampilkan form edit user.
     */
    public function edit(string $id): void
    {
        $user = $this->userService->getById((int)$id);

        if (!$user) {
            Flash::error('User tidak ditemukan.');
            Redirect::to('/users');
            return;
        }

        $roles = $this->roleService->all();

        $this->view('users/edit', [
            'title' => 'Edit User — TransactionAPP',
            'user'  => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * POST /users/{id} — Update user.
     */
    public function update(string $id): void
    {
        try {
            $dto = UserDTO::fromRequest(Request::all());
            $this->userService->update((int)$id, $dto);

            Flash::success('User berhasil diupdate.');
            Redirect::to('/users');
        } catch (\RuntimeException $e) {
            Flash::error($e->getMessage());
            Redirect::back();
        }
    }

    /**
     * POST /users/{id}/delete — Hapus user.
     */
    public function delete(string $id): void
    {
        // Prevent deleting self
        $currentUser = Session::user();
        if ($currentUser && (int)$currentUser['id'] === (int)$id) {
            Flash::error('Anda tidak dapat menghapus akun sendiri.');
            Redirect::to('/users');
            return;
        }

        $this->userService->delete((int)$id);
        Flash::success('User berhasil dihapus.');
        Redirect::to('/users');
    }
}
