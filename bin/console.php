#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Galactus\Persistence\PDO\Connector;
use Galactus\Command\AbsorbFeed;

$dbConnector = new Connector('localhost', 'galactus', 'password', 'galactus');

$application = new Application();
$application->add(new AbsorbFeed($dbConnector));
$application->run();