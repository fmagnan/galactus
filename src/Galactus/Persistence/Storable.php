<?php

namespace Galactus\Persistence;

interface Storable
{
    public function add(array $data, $ignore = false);

    public function update(array $data, $whereClause);
}