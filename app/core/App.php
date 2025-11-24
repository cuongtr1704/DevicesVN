<?php
/**
 * App Class - Main Application Router with Custom Routes Support
 */

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];
    protected $routes = [];

    public function __construct() {
        // Load custom routes
        $this->loadRoutes();
        
        $url = $this->parseUrl();
        $requestUri = implode('/', $url);

        // Check custom routes first
        $matched = $this->matchRoute($requestUri);
        
        if ($matched) {
            // Custom route matched
            list($this->controller, $this->method, $this->params) = $matched;
        } else {
            // Default routing (Controller/Method/Params)
            $this->defaultRouting($url);
        }

        // Require the controller
        $controllerPath = ROOT_PATH . '/app/controllers/' . $this->controller . '.php';
        if (!file_exists($controllerPath)) {
            $this->show404();
            return;
        }
        
        require_once $controllerPath;
        $this->controller = new $this->controller;

        // Check if method exists
        if (!method_exists($this->controller, $this->method)) {
            $this->show404();
            return;
        }

        // Call the controller method with params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Load custom routes from config/routes.php
     */
    protected function loadRoutes() {
        $routeFile = ROOT_PATH . '/config/routes.php';
        if (file_exists($routeFile)) {
            $this->routes = require $routeFile;
        }
    }

    /**
     * Match request URI against custom routes
     * 
     * @param string $requestUri
     * @return array|false [controller, method, params] or false
     */
    protected function matchRoute($requestUri) {
        foreach ($this->routes as $pattern => $target) {
            $regex = preg_replace('/:\w+/', '([^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $requestUri, $matches)) {
                array_shift($matches);
                if (is_array($target) && isset($target['controller']) && isset($target['action'])) {
                    return [$target['controller'], $target['action'], $matches];
                }
            }
        }

        return false;
    }

    /**
     * Default routing: /controller/method/param1/param2
     * 
     * @param array $url
     */
    protected function defaultRouting($url) {
        // Check if controller exists
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            if (file_exists(ROOT_PATH . '/app/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        // Check if method exists
        if (isset($url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        // Get params
        $this->params = $url ? array_values($url) : [];
    }

    /**
     * Parse URL from query string
     * 
     * @return array
     */
    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }

    /**
     * Show 404 error page
     */
    protected function show404() {
        http_response_code(404);
        require_once ROOT_PATH . '/app/controllers/HomeController.php';
        $controller = new HomeController();
        if (method_exists($controller, 'notFound')) {
            $controller->notFound();
        } else {
            echo '404 - Page Not Found';
        }
        exit;
    }
}

