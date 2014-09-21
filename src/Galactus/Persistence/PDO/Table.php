<?php

namespace Galactus\Persistence\PDO;

use Galactus\Persistence\Storable;

abstract class Table implements Storable
{
    const PDO_PARAM_RAW = 0;

    protected $dbConnector;
    protected $tableName;
    protected $primaryKey;

    public function __construct(Connector $dbConnector, $tableName, $primaryKey)
    {
        $this->dbConnector = $dbConnector;
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
    }

    public function all($fetchStyle = \PDO::FETCH_CLASS)
    {
        $query = sprintf('SELECT * FROM `%s`', $this->tableName);
        $statement = $this->dbConnector->execute($query);
        return $statement->fetchAll($fetchStyle);
    }

    public function update(array $data, $whereClause)
    {
        // @todo
    }

    public function updateDataArray(array $aFieldsValues, array $aCond = array(), $limit = 0, $lock = false)
    {
        return $this->dbConnector->updateDataArray($this->tableName, $aFieldsValues, $aCond, $limit, $lock);
    }

    public function findOneBy($field, $value, array $fieldsToRetrieve = [])
    {
        $mask = 'SELECT %s FROM `%s` WHERE `%s`=:value LIMIT 1';
        $fields = empty($fieldsToRetrieve) ? '*' : implode(',', $fieldsToRetrieve);

        $query = sprintf($mask, $fields, $this->tableName, $field);
        $statement = $this->dbConnector->execute($query, ['value' => $value]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }

    public function findBy($field, $value)
    {
        $rows = $this->dbConnector->getRowsById($this->tableName, $field, $value);

        return $rows;
    }

    public function add(array $data, $ignore = false)
    {
        return $this->dbConnector->insert($this->tableName, $data, false, $ignore);
    }

}