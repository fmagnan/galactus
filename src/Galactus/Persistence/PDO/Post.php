<?php

namespace Galactus\Persistence\PDO;

class Post extends Table
{

    public function __construct($connector)
    {
        parent::__construct($connector, 'posts', 'id');
    }

    public function findAllForFeed($feed)
    {
        return $this->findBy('feedId', $feed);
    }

}