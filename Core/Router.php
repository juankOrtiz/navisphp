<?php

namespace Core;

use Core\Middleware\Auth;
use Core\Middleware\Guest;
use Core\Middleware\Middleware;

class Router {
  protected array $routes = [];

  public function add(string $method, string $uri, string $controller): self {
    $this->routes[] = [
      'uri' => $uri,
      'controller' => $controller,
      'method' => $method,
      'middleware' => ''
    ];

    return $this;
  }

  public function get(string $uri, string $controller): self {
    return $this->add('GET', $uri, $controller);
  }

  public function post(string $uri, string $controller): self {
    return $this->add('POST', $uri, $controller);
  }

  public function delete(string $uri, string $controller): self {
    return $this->add('DELETE', $uri, $controller);
  }

  public function patch(string $uri, string $controller): self {
    return $this->add('PATCH', $uri, $controller);
  }

  public function put(string $uri, string $controller): self {
    return $this->add('PUT', $uri, $controller);
  }

  public function only(string $key): self {
    $this->routes[array_key_last($this->routes)]['middleware'] = $key;

    return $this;
  }

  public function route(string $uri, string $method) {
    foreach ($this->routes as $route) {
      if($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
        Middleware::resolve($route['middleware']);

        return require base_path('Http/controllers' .$route['controller']);
      }
    }

    $this->abort();
  }

  public function previousUrl(): string {
      return $_SERVER['HTTP_REFERER'];
  }

  protected function abort(int $code = 404): void
  {
      http_response_code($code);
      require base_path("views/{$code}.php");
      die();
  }
}