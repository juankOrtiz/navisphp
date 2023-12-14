<?php

use Core\Response;

function dd($value): void
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

function report(): void
{
    error_reporting(E_ALL);
    ini_set('display_errors', True);
}

function urlIs(string $value): bool
{
    return $_SERVER['REQUEST_URI'] === $value;
}

function urlStartsWith(string $fragment): bool
{
    return str_starts_with($_SERVER['REQUEST_URI'], $fragment);
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

function user_type(): int
{
    return (int)($_SESSION['user']['type']);
}

function isSuperAdmin(): bool
{
    return user_type() === 1;
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

function resourceRoute(string $path): string
{
    $slashes = substr_count($_SERVER['REQUEST_URI'], '/');
    return str_repeat('../', $slashes);
}

function css(string $path): string
{
    return resourceRoute($path) . 'css' . $path;
}

function js(string $path): string
{
    return resourceRoute($path) . 'js' . $path;
}

function img(string $path): string
{
    return resourceRoute($path) . 'img' . $path;
}