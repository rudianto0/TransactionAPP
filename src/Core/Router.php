<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple URL Router
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function addRoute(string $method, string $path, string $controller, string $action, array $middlewares = []): void
    {
        // Convert {id} to named regex group
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'     => strtoupper($method),
            'pattern'    => $pattern,
            'controller' => $controller,
            'action'     => $action,
            'middlewares' => $middlewares,
        ];
    }

    /**
     * Register a route group with shared middlewares
     */
    public function group(array $middlewares, callable $callback): void
    {
        $previousMiddlewares = $this->middlewares;
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        $callback($this);
        $this->middlewares = $previousMiddlewares;
    }

    public function get(string $path, string $controller, string $action): void
    {
        $this->addRoute('GET', $path, $controller, $action, $this->middlewares);
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->addRoute('POST', $path, $controller, $action, $this->middlewares);
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove base path (untuk development di subfolder)
        $basePath = '/TransactionAPP';
        if (str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . trim($uri, '/');
        if ($uri === '/') {
            $uri = '/';
        }

        // Handle trailing slash
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($method !== $route['method']) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run middlewares
                foreach ($route['middlewares'] as $middlewareSpec) {
                    // Support "Class:param" syntax
                    $parts = explode(':', $middlewareSpec, 2);
                    $middlewareClass = $parts[0];
                    $middlewareParam = $parts[1] ?? null;

                    if ($middlewareParam !== null) {
                        $middleware = new $middlewareClass($middlewareParam);
                    } else {
                        $middleware = new $middlewareClass();
                    }

                    $result = $middleware->handle($params);
                    if ($result !== null) {
                        // Middleware returned a response (redirect, 403, etc.)
                        return;
                    }
                }

                // Instantiate controller & call action
                $controllerClass = $route['controller'];
                $controller = new $controllerClass();
                $controller->{$route['action']}(...array_values($params));
                return;
            }
        }

        // 404 — no route matched
        http_response_code(404);
        require VIEWS_PATH . '/errors/404.php';
    }
}
