<?php

namespace src\Router;

class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    public function delete($path, $callback) {
        $this->addRoute('DELETE', $path, $callback);
    }

    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
        ];
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $path = substr($requestUri, strlen($basePath));

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $routeParts = explode('/', trim($route['path'], '/'));
            $pathParts = explode('/', trim($path, '/'));

            if (count($routeParts) !== count($pathParts)) {
                continue;
            }

            $params = [];
            $matched = true;

            for ($i = 0; $i < count($routeParts); $i++) {
                $routePart = $routeParts[$i];
                $pathPart = $pathParts[$i];

                if ($this->isRouteParam($routePart)) {
                    $params[] = $this->castParam($pathPart);
                } elseif ($routePart !== $pathPart) {
                    $matched = false;
                    break;
                }
            }

            if ($matched) {
                // Special handling for POST with JSON input
                if ($method === 'POST' && trim($route['path'], '/') === trim($path, '/')) {
                    $input = json_decode(file_get_contents('php://input'), true);
                    if (!isset($input['name'])) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing name field']);
                        return;
                    }
                    return call_user_func_array($route['callback'], [$input['name']]);
                }

                return call_user_func_array($route['callback'], $params);
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }

    private function isRouteParam(string $part): bool {
        return strlen($part) > 2 && $part[0] === '{' && $part[strlen($part) - 1] === '}';
    }

    private function castParam(string $val) {
        return is_numeric($val) ? (int)$val : $val;
    }
}
