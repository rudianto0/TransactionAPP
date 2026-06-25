<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\DTOs\LoginDTO;
use App\Helpers\Flash;
use App\Helpers\Redirect;
use App\Services\AuthService;
use App\Validators\LoginValidator;

/**
 * AuthController — menangani login, logout.
 */
class AuthController extends Controller
{
    private AuthService $authService;
    private LoginValidator $loginValidator;

    public function __construct()
    {
        $this->authService    = new AuthService();
        $this->loginValidator = new LoginValidator();
    }

    /**
     * GET /login — Tampilkan halaman login.
     */
    public function loginForm(): void
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->authService->user()) {
            Redirect::to('/');
            return;
        }

        $this->viewPlain('auth/login');
    }

    /**
     * POST /login — Proses login.
     */
    public function login(): void
    {
        $dto = LoginDTO::fromRequest(Request::all());

        // Validasi
        if (!$this->loginValidator->validate($dto)) {
            Flash::error($this->loginValidator->firstError());
            Redirect::to('/login');
            return;
        }

        // Attempt login
        $user = $this->authService->login($dto->email, $dto->password);

        if ($user === null) {
            Flash::error('Email atau password salah, atau akun tidak aktif.');
            Redirect::to('/login');
            return;
        }

        Flash::success('Selamat datang, ' . htmlspecialchars($user['name']) . '!');
        Redirect::to('/');
    }

    /**
     * POST /logout — Proses logout.
     */
    public function logout(): void
    {
        $this->authService->logout();
        Flash::success('Anda telah logout.');
        Redirect::to('/login');
    }
}
