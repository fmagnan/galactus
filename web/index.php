<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../inc/settings.inc.php';

use RestService\Server;
use Galactus\Rest\Server\Frontend;

Server::create('/', new Frontend($connector))
    ->addGetRoute('feeds', 'feeds')
    ->addGetRoute('posts', 'posts')
    ->run();