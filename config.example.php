<?php

return [
    'app' => [
        'url' => '',
        'timezone' => 'America/Argentina/Buenos_Aires',
    ],
    'database' => [
        'username' => '',
        'password' => '',
        'host' => '',
        'port' => 3306,
        'dbname' => '',
        'charset' => 'utf8mb4'
    ],
    'mail' => [
        'host' => '',
        'port' => '',
        'account' => '',
        'password' => '',
    ],
    'maintenance' => [
        'status' => 0,
        'allowed_routes' => [
            '/login',
            '/session'
        ],
    ],
];
