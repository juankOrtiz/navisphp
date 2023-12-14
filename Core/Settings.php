<?php

namespace Core;

class Settings
{
    private array $config;

    public function __construct($config) {
        $file = require base_path('config.php');
        $this->config = $file[$config];
    }

    public function get($key) {
        return $this->config[$key];
    }
}