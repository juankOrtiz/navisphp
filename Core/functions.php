<?php

use Core\Response;

function dd($value): void
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

function urlIs($value): bool
{
    return $_SERVER['REQUEST_URI'] === $value;
}

function authorize($condition, $status = Response::FORBIDDEN): void
{
  if(! $condition) {
    abort($status);
  }
}

function base_path($path) {
  return BASE_PATH . $path;
}

function view($path, $attributes = []) {
  extract($attributes, EXTR_OVERWRITE);

  require base_path('views/' . $path);
}

function user()
{
  return $_SESSION['user'] ?? false;
}

function abort($code = 404): void
{
  http_response_code($code);
  require base_path("views/{$code}.php");
  die();
}

function redirect($path) {
    header("location: {$path}");
    exit();
}

function old($key, $default = '') {
    return \Core\Session::get('old')[$key] ?? $default;
}