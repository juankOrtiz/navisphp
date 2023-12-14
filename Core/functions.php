<?php

use Core\Response;

function dd($value): void
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

function urlIs(string $value): bool
{
    return $_SERVER['REQUEST_URI'] === $value;
}

function authorize($condition, int $status = Response::FORBIDDEN): void
{
  if(! $condition) {
    abort($status);
  }
}

function base_path(string $path): string {
  return BASE_PATH . $path;
}

function view(string $path, array $attributes = []): void {
  extract($attributes, EXTR_OVERWRITE);

  require base_path('views/' . $path);
}

function user(): array|false
{
  return $_SESSION['user'] ?? false;
}

function abort(int $code = 404): void
{
  http_response_code($code);
  require base_path("views/{$code}.php");
  die();
}

function redirect(string $path): void {
    header("location: {$path}");
    exit();
}

function old(string $key, string $default = ''): string {
    return \Core\Session::get('old')[$key] ?? $default;
}