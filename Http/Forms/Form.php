<?php

namespace Http\Forms;

use Core\ValidationException;

class Form
{
    protected array $errors = [];

    public function __construct(public array $attributes)
    {
    }

    public static function validate(array $attributes): static
    {
        $instance = new static($attributes);

        return $instance->failed() ? $instance->throw() : $instance;
    }

    public function throw()
    {
        return ValidationException::throw($this->errors(), $this->attributes);
    }

    public function failed(): int
    {
        return count($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function error(string $field, string $message): static
    {
        $this->errors[$field] = $message;

        return $this;
    }
}
