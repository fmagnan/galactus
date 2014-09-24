#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../inc/settings.inc.php';

use Symfony\Component\Console\Application;
use Galactus\Persistence\PDO\Connector;
use Galactus\Command\AbsorbFeed;

$dbConnector = new Connector($settings['database']);

$application = new Application();
$application->add(new AbsorbFeed($dbConnector));
$application->run();