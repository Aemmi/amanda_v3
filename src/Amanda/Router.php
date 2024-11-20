<?php

namespace Src\Amanda;

class Router extends DB
{
    protected $full_path;
    protected $uri;
    protected $request_method;
    protected $indexedParams = [];
    protected $_getList = [];
    protected $_postList = [];
    protected $params = [];
    protected $basePath = ''; // Base path for the application
    protected $routes = [];  // Store the routes for each HTTP method
    protected $namedRoutes = []; // Named routes
    protected $middlewares = []; // Middlewares to be executed
    protected $errorHandlers = []; // Custom error handlers
    protected $rateLimits = []; // Rate limits storage
    protected $routeGroupPrefix = ''; // Prefix for grouped routes

    public function __construct($basePath = '')
    {
        parent::__construct();
        $this->basePath = $basePath ?: '/'; // Default base path
        $this->full_path = $_SERVER['REQUEST_URI'] ?? '';
        $this->uri = parse_url($this->full_path, PHP_URL_PATH);
        $this->request_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    // Normalize the route path
    private function normalizePath($path)
    {
        return trim($path, '/');
    }

    // Add group prefix to routes
    private function addGroupPrefix($path)
    {
        return $this->routeGroupPrefix ? $this->normalizePath($this->routeGroupPrefix . '/' . $path) : $path;
    }

    // Register routes
    public function get($path, $callback, $name = null)
    {
        $this->addRoute('GET', $path, $callback, $name);
    }

    public function post($path, $callback, $name = null)
    {
        $this->addRoute('POST', $path, $callback, $name);
    }

    public function put($path, $callback, $name = null)
    {
        $this->addRoute('PUT', $path, $callback, $name);
    }

    public function patch($path, $callback, $name = null)
    {
        $this->addRoute('PATCH', $path, $callback, $name);
    }

    public function delete($path, $callback, $name = null)
    {
        $this->addRoute('DELETE', $path, $callback, $name);
    }

    private function addRoute($method, $path, $callback, $name = null)
    {
        // Normalize and apply the group prefix to the path
        $fullPath = $this->normalizePath($this->routeGroupPrefix) . '/' . $this->normalizePath($path);

        // Store the route under the corresponding HTTP method
        $this->routes[$method][$this->normalizePath($fullPath)] = $callback;

        // Optionally store the named route
        if ($name) {
            $this->namedRoutes[$name] = $this->normalizePath($fullPath);
        }
    }

    // Match the current request to a route
    public function dispatch()
    {
        $uri = $this->uri; // Current request URI
        $method = $this->request_method; // Current HTTP request method (GET, POST, etc.)
        
        // Prepend basePath to the route if it's set
        $basePath = rtrim($this->basePath, '/');
        
        // Iterate over the routes for the current request method
        foreach ($this->routes[$method] as $route => $callback) {
            // Ensure the basePath is considered when matching the route
            $fullRoute = $basePath . '/' . $route;

            // Match the full route with the current URI
            if ($this->matchRoute($fullRoute, $uri, $params)) {
                // Create request and response objects (or associative arrays) if needed
                $request = [
                    'uri' => $uri,
                    'method' => $method,
                    'params' => $params
                ];
                $response = []; // Simple response array, can be expanded as needed

                // Create a next function for middleware chaining
                $next = function () use ($request, $response) {
                    // Proceed to the next middleware or route handler
                };

                // Trigger middleware
                $this->executeMiddlewares($request, $response, $next);

                // Call the matched callback with the route parameters
                return call_user_func($callback, $params);
            }
        }
        
        // If no route is found, trigger a 404 error
        $this->triggerErrorHandler(404);
    }

    // Match route with dynamic parameters
    private function matchRoute($route, $path, &$params)
    {
        $routeRegex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
        $routeRegex = '/^' . str_replace('/', '\/', $routeRegex) . '$/';

        if (preg_match($routeRegex, $path, $matches)) {
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }

    // Named route generation
    public function route($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route '{$name}' not found.");
        }
        $route = $this->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $route = str_replace("{{$key}}", $value, $route);
        }
        return '/' . $route;
    }

    // Middleware handling
    public function useMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    // Execute middlewares with the correct arguments
    private function executeMiddlewares($request, $response)
    {
        foreach ($this->middlewares as $middleware) {
            if (is_callable($middleware)) {
                // Call the middleware with $request, $response, and $next arguments
                $next = function () use ($request, $response) {
                    // Continue to the next middleware or route handler
                };
                $middleware($request, $response, $next);
            } elseif (is_object($middleware) && method_exists($middleware, 'handle')) {
                $middleware->handle($request, $response, $next);
            }
        }
    }

    // Route grouping
    public function group($prefix, $callback)
    {
        // Backup the current routeGroupPrefix
        $previousGroupPrefix = $this->routeGroupPrefix;

        // Set the new group prefix
        $this->routeGroupPrefix = $this->normalizePath($previousGroupPrefix . '/' . $prefix);

        // Execute the callback with the group prefix
        $callback($this);

        // Restore the previous group prefix
        $this->routeGroupPrefix = $previousGroupPrefix;
    }

    public function debugRoutes()
    {
        echo '<pre>' . print_r($this->routes, true) . '</pre>';
    }

    // Error handling
    public function setErrorHandler($code, $callback)
    {
        $this->errorHandlers[$code] = $callback;
    }

    private function triggerErrorHandler($code)
    {
        if (isset($this->errorHandlers[$code])) {
            call_user_func($this->errorHandlers[$code]);
        } else {
            http_response_code($code);
            echo "{$code} Error";
        }
    }

    // Rate limiting
    public function rateLimit($key, $maxRequests, $duration)
    {
        $time = time();
        $this->rateLimits[$key] = $this->rateLimits[$key] ?? [];

        // Remove expired requests
        $this->rateLimits[$key] = array_filter($this->rateLimits[$key], function ($timestamp) use ($time, $duration) {
            return $timestamp > ($time - $duration);
        });

        // Add the current request
        $this->rateLimits[$key][] = $time;

        if (count($this->rateLimits[$key]) > $maxRequests) {
            http_response_code(429);
            echo "Too Many Requests. Please try again later.";
            exit;
        }
    }

    // Route generation method
    public function generate($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route '{$name}' not found.");
        }
        $route = $this->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $route = str_replace("{{$key}}", $value, $route);
        }
        return '/' . $route;
    }

    // Rendering views
    public static function render($file, $vars = [])
    {
        extract($vars);
        require('temp/' . $file . '.temp.php');
    }

    // JSON response
    public static function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Redirect
    public static function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    // CORS support
    public function enableCORS($allowedOrigins = '*')
    {
        header("Access-Control-Allow-Origin: $allowedOrigins");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }

    public static function use($file)
    {
        require($file . '.route.php');
    }

    // Handle a request by calling the dispatcher method
    public function run()
    {
        $this->dispatch();
    }
}