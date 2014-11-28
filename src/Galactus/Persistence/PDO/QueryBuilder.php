<?php

namespace Galactus\Persistence\PDO;

class QueryBuilder
{
    protected $connector;
    protected $tableName;
    protected $primaryKey;

    public function __construct(\PDO $connector, $tableName, $primaryKey = 'id')
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

    public function proposeNewFeed(array $data)
    {
        $query = 'INSERT INTO `feeds` (`planetId`, `url`, `isEnabled`)
                SELECT `id`, :url, 0 FROM `planets` WHERE `code` = :code';
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

    public function findActiveFeeds($code = false)
    {
        $mask = 'SELECT `f`.*
                FROM `feeds` `f`
                JOIN `planets` `p`
                ON `f`.`planetId`=`p`.`id`
                WHERE `f`.`isEnabled` = 1
                %s
                GROUP BY `f`.`id`
                ORDER BY `f`.`name`';
        $codeClause = $code ? 'AND `p`.`code`=:code' : '';
        $query = sprintf($mask, $codeClause);
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $statement->execute(['code' => $code]);
        $feeds = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->connector->commit();

        return $feeds;
    }

    public function findActivePosts(array $conditions = [], $limit, $offset = 0)
    {
        $mask = 'SELECT `p`.`id`, `p`.`feedId`, `p`.`title`, `p`.`url`,
                DATE_FORMAT(`p`.`pubDate`, "%%Y-%%m-%%d") AS `pubDate`,
                `p`.`content`, `p`.`author`, `f`.`name` AS `feedName`
                FROM `posts` `p`
                JOIN `feeds` `f` ON `p`.`feedId`=`f`.`id`
                JOIN `planets` `pl` ON `f`.`planetId` = `pl`.`id`
                WHERE 1 %s
                ORDER BY `p`.`pubDate` DESC
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

    public function allWhichBeginWith($prefix = '')
    {
        $mask = 'SELECT `name`, `value` FROM `%s` WHERE `name` LIKE :prefix';
        $query = sprintf($mask, $this->tableName);
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $statement->execute(['prefix' => $prefix . '%']);
        $posts = $statement->fetchAll(\PDO::FETCH_KEY_PAIR);
        $this->connector->commit();

        return $posts;
    }

    public function flushBuildDate()
    {
        $mask = 'INSERT INTO `%s` (`name`, `value`)
                VALUES ("lastBuildDate", NOW())
                ON DUPLICATE KEY UPDATE `value` = NOW()';
        $query = sprintf($mask, $this->tableName);
        $this->connector->beginTransaction();
        $statement = $this->connector->prepare($query);
        $result = $statement->execute();
        $this->connector->commit();

        return $result;
    }

}