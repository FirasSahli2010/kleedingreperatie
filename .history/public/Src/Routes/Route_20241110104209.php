<?php

namespace App\Public\Src\Routes;

class Router {
    private $routes = [];

    public function addRoute($method, $path, $action) {
        $this->routes[] = compact('method', 'path', 'action');
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($method === $route['method'] && preg_match($this->compilePath($route['path']), $uri, $params)) {
                array_shift($params);
                return is_callable($route['action'])
                    ? call_user_func_array($route['action'], $params)
                    : call_user_func_array([new $route['action'][0], $route['action'][1]], $params);
            }
        }
        
        // 404 response if no route is found
        http_response_code(404);
        include __DIR__ . '/../../public/404.php';
    }

    private function compilePath($path) {
        return '#^' . preg_replace('/\{([a-z]+)\}/', '([^/]+)', $path) . '$#i';
    }
}