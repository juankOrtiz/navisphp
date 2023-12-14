<?php

namespace Core;

class Validator
{
    public static function string($value, $min = 1, $max = INF): bool
    {
        $value = trim($value);

        return strlen($value) >= $min && strlen($value) <= $max;
    }

    public static function optionalString($value, $min = 1, $max = INF): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return self::string($value, $min, $max);
    }

    public static function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public static function number($value, $min = 1, $max = INF): bool
    {
        return is_numeric($value) && (int)$value >= $min && (int)$value <= $max;
    }
}
