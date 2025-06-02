<?php

namespace src\Router;
use src\Logger\RequestLogger; // Add this line
require_once __DIR__ . '/../Logger/RequestLogger.php'; // Add this line

class Router {
    private $routes = [];
    // Stores all registered routes as an array

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    public function delete($path, $callback) {
        $this->addRoute('DELETE', $path, $callback);
    }

    public function put($path, $callback) {
        $this->addRoute('PUT', $path, $callback);
    }

    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
        ];
    }
    // Adds a route to the $routes array

    public function run() {
        RequestLogger::log(); // Add this line

        $method = $_SERVER['REQUEST_METHOD'];
        // Gets the HTTP method (GET, POST, etc.)

        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Gets the path from the request URI

        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        // Gets the base path of the script

        $path = substr($requestUri, strlen($basePath));
        // Removes the base path from the request URI

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            // Skip if HTTP method does not match

            $routeParts = explode('/', trim($route['path'], '/'));
            $pathParts = explode('/', trim($path, '/'));
            // Split both route and request path into parts

            if (count($routeParts) !== count($pathParts)) {
                continue;
            }
            // Skip if the number of parts does not match

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
            // For each part, check if it's a parameter or an exact match

            if ($matched) {
                // If the route matches

                // Special handling for POST and PUT with JSON input
                if ($method === 'POST' || $method === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    // Read JSON input from the request body
                    // Append input array as last argument
                    return call_user_func_array($route['callback'], array_merge($params, [$input]));
                }

                return call_user_func_array($route['callback'], $params);
                // Call the callback with parameters
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        // If no route matches, return 404 error
    }

    private function isRouteParam(string $part): bool {
        return strlen($part) > 2 && $part[0] === '{' && $part[strlen($part) - 1] === '}';
    }
    // Checks if a route part is a parameter (e.g., {id})

    private function castParam(string $val) {
        return is_numeric($val) ? (int)$val : $val;
    }
    // Converts numeric parameters to integers, otherwise returns as string
}
