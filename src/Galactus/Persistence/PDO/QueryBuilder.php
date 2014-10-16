<?php

namespace Galactus\Persistence\PDO;

class QueryBuilder
{
    protected $connector;
    protected $tableName;
    protected $primaryKey;

    public function __construct(\PDO $connector, $tableName, $primaryKey)
    {
        $this->connector = $connector;
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
    }

    public function add(array $data, $ignore = false)
    {
        $mask = 'INSERT %s INTO `%s` (%s) VALUES (%s)';
        $fields = $values = [];
        foreach ($data as $field => $value) {
            $fields[] = $field;
            $values[] = ':' . $field;
        }
        $ignoreClause = $ignore ? 'IGNORE' : '';
        $query = sprintf($mask, $ignoreClause, $this->tableName, implode(',', $fields), implode(',', $values));
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $result = $statement->execute($data);
        $this->connector->commit();

        return $result;
    }

    public function updateByPk(array $data, $pk)
    {
        $mask = 'UPDATE `%s` SET %s WHERE `%s`=%d';
        $dataClause = [];
        foreach ($data as $field => $value) {
            $dataClause[] = sprintf('%s=:%s', $field, $field);
        }
        $query = sprintf($mask, $this->tableName, implode(',', $dataClause), $this->primaryKey, $pk);
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $result = $statement->execute($data);
        $this->connector->commit();

        return $result;
    }

    public function disableFeed($id)
    {
        $mask = 'UPDATE `%s` SET `isEnabled`=0 WHERE `%s`=%d';
        $query = sprintf($mask, $this->tableName, $this->primaryKey, $id);
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $result = $statement->execute();
        $this->connector->commit();

        return $result;
    }

    public function findActiveFeeds()
    {
        $query = 'SELECT `f`.*, GROUP_CONCAT(DISTINCT `t`.`name`) AS `tags`
                FROM `feeds` `f`
                LEFT JOIN `feed_x_tag` `ft` ON `f`.`id`=`ft`.`feedId`
                LEFT JOIN `tags` `t` ON `ft`.`tagId`=`t`.`id`
                WHERE `f`.`isEnabled` = 1
                GROUP BY `f`.`id`
                ORDER BY `f`.`name`';
        $this->connector->beginTransaction();
        $statement = $this->connector->query($query);
        $feeds = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->connector->commit();

        return $feeds;
    }

    public function findActivePosts(array $conditions = [], $limit, $offset)
    {
        $mask = 'SELECT `p`.`id`, `p`.`feedId`, `p`.`title`, `p`.`url`,
                DATE_FORMAT(`p`.`creationDate`, "%%Y-%%m-%%d") AS `creationDate`,
                `p`.`content`, `f`.`name` AS `feedName`
                FROM `posts` `p`
                JOIN `feeds` `f` ON `p`.`feedId`=`f`.`id`
                WHERE 1 %s
                ORDER BY `p`.`creationDate` DESC
                LIMIT %d OFFSET %d';
        $where = '';
        foreach ($conditions as $key => $value) {
            $where .= sprintf('AND `%s`=:%s', $key, $key);
        }
        $query = sprintf($mask, $where, $limit, $offset);
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $statement->execute($conditions);
        $posts = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->connector->commit();

        return $posts;
    }

    public function findByPk($value)
    {
        $mask = 'SELECT * FROM `%s` WHERE `%s`=:primary_key LIMIT 1';
        $query = sprintf($mask, $this->tableName, $this->primaryKey);
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $statement->execute(['primary_key' => $value]);
        $row = $statement->fetch();
        $this->connector->commit();

        return $row;
    }

    public function truncate()
    {
        $query = 'TRUNCATE TABLE ' . $this->tableName;
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $result = $statement->execute();
        $this->connector->commit();

        return $result;
    }

    public function last()
    {
        $mask = 'SELECT * FROM `%s` ORDER BY `%s` DESC LIMIT %d';
        $query = sprintf($mask, $this->tableName, 'creationDate', 2);
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $statement->execute();
        $posts = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->connector->commit();

        return $posts;
    }
}