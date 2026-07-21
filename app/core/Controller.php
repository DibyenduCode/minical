<?php

namespace App\Core;

abstract class Controller {
    protected Request $request;
    protected Response $response;

    public function __construct() {
        $this->request = new Request();
        $this->response = new Response();
    }

    protected function render(string $view, array $params = []): void {
        extract($params);
        $csrf_token = Session::generateCsrfToken();
        $currentUser = Session::get('user');
        
        $viewFile = TEMPLATES_DIR . '/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View file `{$view}` not found.");
        }
    }

    protected function requireAuth(): array {
        $user = Session::get('user');
        if (!$user) {
            Session::flash('error', 'Please log in to access this page.');
            $this->response->redirect(APP_URL . '/login');
        }
        return $user;
    }

    protected function requireAdmin(): array {
        $user = $this->requireAuth();
        if (($user['role'] ?? '') !== 'admin') {
            Session::flash('error', 'Access denied. Administrator privileges required.');
            $this->response->redirect(APP_URL . '/dashboard');
        }
        return $user;
    }
}
