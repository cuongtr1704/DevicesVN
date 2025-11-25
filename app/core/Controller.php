<?php
/**
 * Base Controller Class
 */

class Controller {
    
    public function model($model) {
        require_once ROOT_PATH . '/app/models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = [], $layout = 'main') {
        extract($data);

        // Load categories globally for navigation
        if ($layout === 'main') {
            $categoryModel = $this->model('Category');
            $navCategories = $categoryModel->getCategoryTree();
        }

        if (file_exists(ROOT_PATH . '/resources/views/' . $view . '.php')) {
            ob_start();
            require_once ROOT_PATH . '/resources/views/' . $view . '.php';
            $content = ob_get_clean();
            
            if ($layout) {
                $layoutPath = ROOT_PATH . '/resources/views/layouts/' . $layout . '.php';
                if (file_exists($layoutPath)) {
                    require_once $layoutPath;
                } else {
                    echo $content;
                }
            } else {
                echo $content;
            }
        } else {
            die("View does not exist: " . $view);
        }
    }

    public function redirect($url) {
        header('Location: ' . BASE_URL . ltrim($url, '/'));
        exit;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }

    public function setFlash($name, $message, $type = 'info') {
        $_SESSION['flash_' . $name] = [
            'message' => $message,
            'type' => $type
        ];
    }

    public function getFlash($name) {
        if (isset($_SESSION['flash_' . $name])) {
            $flash = $_SESSION['flash_' . $name];
            unset($_SESSION['flash_' . $name]);
            return $flash;
        }
        return null;
    }
}
