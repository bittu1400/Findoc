<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $notFoundCallback;

    public function get($uri, $callback)
    {
        $this->addRoute('GET', $uri, $callback);
    }

    public function post($uri, $callback)
    {
        $this->addRoute('POST', $uri, $callback);
    }

    private function addRoute($method, $uri, $callback)
    {
        $uri = trim($uri, '/');
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'callback' => $callback
        ];
    }

    public function dispatch()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->convertToRegex($route['uri']);
            
            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove full match
                return $this->executeCallback($route['callback'], $matches);
            }
        }

        // No route matched - 404
        if ($this->notFoundCallback) {
            return call_user_func($this->notFoundCallback);
        }
        
        http_response_code(404);
        echo "404 - Page Not Found";
    }

    private function convertToRegex($uri)
    {
        // Convert {id} to capture group, escape other characters
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    private function executeCallback($callback, $params)
    {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        if (is_string($callback)) {
            // Format: "ControllerName@methodName"
            list($controller, $method) = explode('@', $callback);
            
            $controllerClass = "App\\Controllers\\{$controller}";
            
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }

            $controllerInstance = new $controllerClass();
            
            if (!method_exists($controllerInstance, $method)) {
                throw new \Exception("Method {$method} not found in {$controllerClass}");
            }

            return call_user_func_array([$controllerInstance, $method], $params);
        }
    }

    public function setNotFound($callback)
    {
        $this->notFoundCallback = $callback;
    }
}

?>