<?php

declare(strict_types=1);

namespace App\Core;

/**
 * View renderer — handles layout + partial views.
 */
class View
{
    private string $layout = 'layouts/main';

    /**
     * Render view inside the main layout (with sidebar, navbar, footer).
     */
    public function render(string $view, array $data = []): void
    {
        // Extract data to variables
        extract($data, EXTR_SKIP);

        // Capture the view content
        ob_start();
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }
        require $viewFile;
        $content = ob_get_clean();

        // Render inside layout
        $layoutFile = VIEWS_PATH . '/' . str_replace('.', '/', $this->layout) . '.php';
        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout not found: {$layoutFile}");
        }
        require $layoutFile;
    }

    /**
     * Render view without layout — untuk halaman seperti login.
     */
    public function renderPlain(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }
        require $viewFile;
    }
}
