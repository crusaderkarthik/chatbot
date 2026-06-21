<?php
abstract class Controller {
    protected function view(string $view, array $data = []): void {
        extract($data);
        $viewFile = APP . '/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            die("View not found: $view");
        }
        require $viewFile;
    }

    protected function json(mixed $data, int $status = 200): never {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth(): void {
        if (!Auth::check()) {
            redirect('auth/login');
        }
    }

    protected function requirePermission(string $perm): void {
        $this->requireAuth();
        if (!Auth::can($perm)) {
            http_response_code(403);
            $this->view('errors.403');
            exit;
        }
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function input(string $key, mixed $default = ''): mixed {
        return $_POST[$key] ?? $default;
    }

    protected function sanitize(string $val): string {
        return trim(strip_tags($val));
    }
}
