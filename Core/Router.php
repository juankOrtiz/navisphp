<?php

namespace Core;

use Core\Middleware\Middleware;

class Router
{
    protected array $routes = [];

    public function add(string $method, string $uri, string $controller): self
    {
        $this->routes[] = [
          'uri' => $uri,
          'controller' => $controller,
          'method' => $method,
          'middleware' => ''
        ];

        return $this;
    }

    // Función que permite responder al llamado de las funciones que tengan el nombre de los métodos HTTP soportados.
    public function __call(string $name, array $args): self
    {
        $httpMethods = ['get', 'post', 'delete', 'patch', 'put'];

        if (in_array($name, $httpMethods)) {
            throw new \BadMethodCallException("Method {$name} not supported.");
        }

        return $this->add(strtoupper($name), ...$args);
    }

    public function only(string $key): self
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;

        return $this;
    }

    public function route(string $uri, string $method)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                Middleware::resolve($route['middleware']);

                return require base_path('Http/controllers/' . $route['controller']);
            }
        }

        $this->abort();
    }

    public function previousUrl(): string
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function abort(int $code = 404): void
    {
        http_response_code($code);
        require base_path("views/{$code}.php");
        die();
    }
}