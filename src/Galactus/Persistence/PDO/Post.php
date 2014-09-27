<?php

namespace Galactus\Persistence\PDO;

class Post extends Table
{

    public function __construct($connector)
    {
        parent::__construct($connector, 'posts', 'id');
    }

    public function findActives($limit = 50, $offset = 0)
    {
        return $this->all($limit, $offset);
    }

    public function findActivesCount()
    {
        return $this->countAll();
    }

}