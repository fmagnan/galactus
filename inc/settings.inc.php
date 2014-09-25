<?php

$settings = [
    'database' => [
        'host' => 'localhost',
        'user' => 'galactus',
        'pass' => 'password',
        'dbName' => 'galactus',
        'charset' => 'utf8'
    ]
];

if (file_exists(__DIR__.'/env.inc.php')) {
    require __DIR__.'/env.inc.php';
}