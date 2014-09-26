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
        $mask = 'SELECT `f`.*, GROUP_CONCAT(DISTINCT `t`.`name`) AS `tags`
                FROM `%s` `f`
                LEFT JOIN `feed_x_tag` `ft` ON `f`.`id`=`ft`.`feedId`
                LEFT JOIN `tags` `t` ON `ft`.`tagId`=`t`.`id`
                WHERE `f`.`isEnabled` = 1
                GROUP BY `f`.`id`';
        $query = sprintf($mask, $this->tableName);
        $statement = $this->dbConnector->execute($query);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

}