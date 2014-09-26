<?php

namespace Galactus\Persistence\PDO;

class Feed extends Table
{

    public function __construct($connector)
    {
        parent::__construct($connector, 'feeds', 'id');
    }

    public function findActives()
    {
        return $this->findBy('isEnabled', 1);
    }

}