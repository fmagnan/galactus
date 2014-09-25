<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../inc/settings.inc.php';

use RestService\Server;
use Galactus\Persistence\PDO\Connector;
use Galactus\Rest\Server\Frontend;

$dbConnector = new Connector($settings['database']);

Server::create('/', new Frontend($dbConnector))
    ->addGetRoute('feeds', 'feeds')
    ->addGetRoute('posts', 'posts')
    ->run();