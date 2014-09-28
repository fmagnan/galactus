#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../inc/settings.inc.php';

use Symfony\Component\Console\Application;
use Galactus\Command\AbsorbFeed;

$application = new Application();
$application->add(new AbsorbFeed($connector));
$application->run();