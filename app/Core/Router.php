<?php
class Router {
    private array $routes = [];

    public function get(string $pattern, string $action): void {
        $this->routes['GET'][$pattern] = $action;
    }

    public function post(string $pattern, string $action): void {
        $this->routes['POST'][$pattern] = $action;
    }

    public function dispatch(string $url): void {
        $url    = trim($url, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] ?? [] as $pattern => $action) {
            $regex = '@^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$@';
            if (preg_match($regex, $url, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $method_name] = explode('@', $action);
                require_once APP . '/Controllers/' . $class . '.php';
                $controller = new $class();
                $controller->$method_name(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        require APP . '/Views/errors/404.php';
    }
}
