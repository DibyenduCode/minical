<?php

namespace App\Core;

class App {
    private static array $routes = [];

    public static function get(string $path, array $callback): void {
        self::$routes['GET'][$path] = $callback;
    }

    public static function post(string $path, array $callback): void {
        self::$routes['POST'][$path] = $callback;
    }

    public static function put(string $path, array $callback): void {
        self::$routes['PUT'][$path] = $callback;
    }

    public static function delete(string $path, array $callback): void {
        self::$routes['DELETE'][$path] = $callback;
    }

    public function run(): void {
        $request = new Request();
        $response = new Response();
        
        $method = $request->getMethod();
        $path = $request->getUri();

        // 1. Direct route match
        if (isset(self::$routes[$method][$path])) {
            $callback = self::$routes[$method][$path];
            $controllerClass = '\\' . ltrim($callback[0], '\\');
            $controller = new $controllerClass();
            $action = $callback[1];
            call_user_func([$controller, $action]);
            return;
        }

        // 2. Pattern route match (e.g., /u/{username}, /booking/confirmation/{id}, /api/v1/...)
        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $routePattern => $callback) {
                $regexPattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $routePattern);
                $regexPattern = "#^" . $regexPattern . "$#";

                if (preg_match($regexPattern, $path, $matches)) {
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $controllerClass = '\\' . ltrim($callback[0], '\\');
                    $controller = new $controllerClass();
                    $action = $callback[1];
                    call_user_func_array([$controller, $action], $params);
                    return;
                }
            }
        }

        // 404 Fallback
        $response->setStatusCode(404);
        if (str_starts_with($path, '/api/')) {
            $response->json(['status' => 'error', 'message' => 'API endpoint not found'], 404);
        } else {
            echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'><h1>404 Page Not Found</h1><p>The page <code>" . htmlspecialchars($path) . "</code> does not exist.</p><a href='" . APP_URL . "'>Go Home</a></div>";
        }
    }
}
