<?php

$settings = [
    'database' => [
        'host' => 'localhost',
        'user' => 'galactus',
        'password' => 'password',
        'dbname' => 'galactus'
    ]
];

if (file_exists(__DIR__.'/env.inc.php')) {
    require __DIR__.'/env.inc.php';
}

$options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'];
$dsnMask = 'mysql:host=%s;dbname=%s';
$dsn = sprintf($dsnMask, $settings['database']['host'], $settings['database']['dbname']);
$connector = new \PDO($dsn, $settings['database']['user'], $settings['database']['password'], $options);
$connector->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);