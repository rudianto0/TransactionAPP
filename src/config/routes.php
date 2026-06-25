<?php

/**
 * Route Definitions — Fase 1
 *
 * Semua route aplikasi didaftarkan di sini.
 */

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

$router = new Router();

// ─── Public Routes (tanpa login) ───────────────────────────
$router->get('/login', AuthController::class, 'loginForm');
$router->post('/login', AuthController::class, 'login');

// ─── Protected Routes (harus login) ────────────────────────
$router->group([AuthMiddleware::class], function (Router $router) {

    // Dashboard — semua user yang login bisa akses
    $router->get('/', DashboardController::class, 'index');

    // Logout
    $router->post('/logout', AuthController::class, 'logout');

    // User Management — hanya yang punya permission "manage-users"
    $router->group([PermissionMiddleware::class . ':manage-users'], function (Router $router) {
        $router->get('/users', UserController::class, 'index');
        $router->get('/users/create', UserController::class, 'create');
        $router->post('/users', UserController::class, 'store');
        $router->get('/users/{id}/edit', UserController::class, 'edit');
        $router->post('/users/{id}', UserController::class, 'update');
        $router->post('/users/{id}/delete', UserController::class, 'delete');
    });

});

return $router;
