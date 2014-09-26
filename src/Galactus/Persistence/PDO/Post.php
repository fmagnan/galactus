<?php

namespace Galactus\Persistence\PDO;

class Post extends Table
{

    public function __construct($connector)
    {
        parent::__construct($connector, 'posts', 'id');
    }

    public function findActives()
    {
        return $this->all();
    }

}