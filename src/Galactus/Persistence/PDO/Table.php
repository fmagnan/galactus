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

    public function add(array $data, $ignore = false)
    {
        return $this->dbConnector->insert($this->tableName, $data, false, $ignore);
    }

    public function all($limit, $offset, $fetchStyle = \PDO::FETCH_ASSOC)
    {
        $query = sprintf('SELECT * FROM `%s` LIMIT %d OFFSET %d', $this->tableName, $limit, $offset);
        $statement = $this->dbConnector->execute($query);
        return $statement->fetchAll($fetchStyle);
    }

    public function countAll()
    {
        $query = sprintf('SELECT COUNT(`%s`) FROM `%s`', $this->primaryKey, $this->tableName);
        $statement = $this->dbConnector->query($query);
        return $statement->fetchColumn();
    }

    public function findBy($field, $value, $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = false)
    {
        $rows = $this->dbConnector->getRowsById($this->tableName, $field, $value, $fetchStyle, $fetchArgument);

        return $rows;
    }

    public function findByPk($value, array $fieldsToRetrieve = [])
    {
        return $this->findOneBy($this->primaryKey, $value, $fieldsToRetrieve);
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

    public function truncate()
    {
        $query = sprintf('TRUNCATE TABLE `%s`', $this->tableName);

        return $this->dbConnector->execute($query);
    }

    public function update(array $data, array $conditions)
    {
        return $this->dbConnector->updateDataArray($this->tableName, $conditions, $aCond, $limit, $lock);
    }

}