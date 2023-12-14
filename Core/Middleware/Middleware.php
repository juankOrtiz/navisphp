<?php

namespace Core\Middleware;

class Middleware
{
    public const MAP = [
        'guest' => Guest::class,
        'auth' => Auth::class,
    ];

    public static function resolve($key)
    {
        if(!$key) {
            return;
        }

        $middleware = static::MAP[$key] ?? false;

        if(!$middleware) {
            throw new \Exception("There is no middleware with the name '{$key}'.");
        }

        (new $middleware)->handle();
    }
}
