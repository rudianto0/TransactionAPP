<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base Controller — menyediakan helper untuk render view & redirect.
 */
abstract class Controller
{
    /**
     * Render a view file within the main layout.
     */
    protected function view(string $view, array $data = []): void
    {
        $viewRenderer = new View();
        $viewRenderer->render($view, $data);
    }

    /**
     * Render a view without the layout (e.g., login page).
     */
    protected function viewPlain(string $view, array $data = []): void
    {
        $viewRenderer = new View();
        $viewRenderer->renderPlain($view, $data);
    }

    /**
     * Redirect to a given URL.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Get flashed message from session.
     */
    protected function flash(string $key, ?string $default = null): ?string
    {
        return \App\Helpers\Flash::get($key, $default);
    }
}
