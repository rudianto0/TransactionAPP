<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

/**
 * DashboardController — halaman utama setelah login.
 */
class DashboardController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * GET / — Dashboard page.
     */
    public function index(): void
    {
        $user = $this->authService->user();

        $this->view('dashboard/index', [
            'title' => 'Dashboard — TransactionAPP',
            'user'  => $user,
        ]);
    }
}
