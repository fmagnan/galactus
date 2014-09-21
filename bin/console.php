#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Galactus\Command\Absorb7Cercle;

$application = new Application();
$application->add(new Absorb7Cercle());
$application->run();