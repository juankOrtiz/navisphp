<?php

namespace Http\Forms;

use Core\Validator;

class LoginForm extends Form
{
    public function __construct(public array $attributes)
    {
        if(!Validator::email($attributes['email'])) {
            $this->errors['email'] = 'Please enter a valid email';
        }

        if(!Validator::string($attributes['password'])) {
            $this->errors['password'] = 'Please enter a valid password';
        }
    }
}
