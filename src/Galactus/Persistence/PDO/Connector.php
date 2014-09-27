<?php

namespace Galactus\Persistence\PDO;

class Connector
{
    const INSERT_IGNORE = 1;
    const INSERT_UPDATE = 2;
    const INSERT_REPLACE = 3;
    const CHARSET_UTF8 = 'UTF8';

    protected $pdo;
    protected $lastPdoStatement;

    /**
     * Create a PDOMySQL instance
     */
    public function __construct(array $params)
    {
        $dsn = sprintf('mysql:dbname=%s;host=%s;', $params['dbName'], $params['host']);
        $options = [\PDO::MYSQL_ATTR_INIT_COMMAND => sprintf('SET NAMES "%s"', $params['charset'])];
        $this->pdo = new \PDO($dsn, $params['user'], $params['pass'], $options);
    }

    /**
     * Returns the number of rows affected by the last SQL statement
     */
    public function rowCount(\PDOStatement $mRes)
    {
        return $mRes->rowCount();
    }

    /**
     * Return first primary key field name for the specified table.
     * [WARNING] Use only on Backoffice
     */
    public function getPrimaryKeyName($table)
    {
        $rv = '';
        $q1 = "DESCRIBE `$table`";
        $sta = $this->execute($q1);
        while ($t1 = $this->fetch_assoc($sta)) {
            if ($t1['Key'] == 'PRI') {
                $rv = $t1['Field'];
                break;
            }
        }
        $this->closeCursor($sta);
        return $rv;
    }

    /**
     * Retrocompatibility for MySQL object
     * Fetches multiple rows
     */
    public function getRowsById($table, $field, $value, $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = false)
    {
        $mask = 'SELECT * FROM `%s` WHERE `%s`=?';
        $query = sprintf($mask, $table, $field);
        $statement = $this->execute($query, [$value]);
        if ($fetchArgument) {
            return $statement->fetchAll($fetchStyle, $fetchArgument);
        }
        return $statement->fetchAll($fetchStyle);
    }

    /**
     * Retrocompatibility for MySQL object
     * Create a insert query
     * @param string table name
     * @param array field to insert
     * @param boolean lock or not
     * @param boolean insert ignore or insert
     */
    public function insert($table, array $datas, $lock = false, $ignore = false)
    {
        $iSpecial = $ignore ? self::INSERT_IGNORE : false;
        return $this->insertData($table, $datas, $iSpecial, true, $lock);
    }

    /**
     * Check if the specified table exists
     */
    public function tableExists($tableName)
    {
        $res = $this->execute("SHOW TABLES LIKE '$tableName'");
        return ($this->num_rows($res) !== 0);
    }

    /**
     * Prepare and execute a query
     *
     * @param string query string with prepare syntax (or not)
     * @param mixed params to use into the prepared query
     *
     * @return \PDOStatement
     */
    public function execute($sQuery, $aParams = array())
    {
        if (!is_array($aParams)) {
            $aParams = array($aParams);
        }
        /**
         * @var \PDOStatement $mRes
         */
        $mRes = $this->pdo->prepare($sQuery);
        if ($mRes === false) {
            self::onError($this->pdo);
        }
        $tmp = $mRes->execute($aParams);
        if ($tmp === false) {
            self::onError($mRes);
        }
        $this->lastPdoStatement = $mRes;
        return $mRes;
    }

    /**
     * Returns an array containing all of the result of the query
     * @param $sQuery query string with prepare syntax (or not)
     * @param array $aParams params to use into the prepared query
     * @param int $mFetch integer constant of PDO, default is \PDO::FETCH_ASSOC
     *
     * @return array
     */
    public function fetchAll($sQuery, array $aParams = array(), $mFetch = \PDO::FETCH_ASSOC)
    {
        $oStmt = $this->execute($sQuery, $aParams);
        $aRows = $oStmt->fetchAll($mFetch);
        $this->closeCursor($oStmt);
        return $aRows;
    }

    /**
     * Fetches the first row from a query
     * @param string $sQuery query string with prepare syntax (or not)
     * @param array $aParams params to use into the prepared query
     * @param int $mFetch constant of PDO, default is \PDO::FETCH_ASSOC
     *
     * @return mixed array if result, else false
     */
    public function fetchOne($sQuery, array $aParams = array(), $mFetch = \PDO::FETCH_ASSOC)
    {
        $oStmt = $this->execute($sQuery, $aParams);
        $aRow = $oStmt->fetch($mFetch);
        $this->closeCursor($oStmt);
        return $aRow;
    }

    /**
     * Fetches the next row from a result set
     *
     * @param \PDOStatement $oStmt
     * @param int $mFetch Controls how the next row will be returned to the caller
     *
     * @return array
     */
    public function fetch(\PDOStatement $oStmt, $mFetch = \PDO::FETCH_ASSOC)
    {
        $aRow = $oStmt->fetch($mFetch);
        return $aRow;
    }

    /**
     * Closes the cursor, enabling the statement to be executed again.
     * @param $oStmt \PDOStatement object
     */
    public function closeCursor(\PDOStatement $oStmt)
    {
        $oStmt->closeCursor();
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Quotes a string for use in a query.
     * @param $mVal The string to be quoted
     *
     * @return string
     */
    public function escape($mVal)
    {
        return $this->pdo->quote($mVal);
    }

    /**
     * @param $sTable table name
     * @param array $aFieldsValues field to insert
     * @param bool $iSpecial object constant depending of query type (ex : PDOMySQL::INSERT_IGNORE)
     * @param bool $bGetId do return last insert id or boolean
     * @param bool $lock lock table or not
     *
     * @return bool|string
     */
    public function insertData($sTable, array $aFieldsValues, $iSpecial = false, $bGetId = true, $lock = false)
    {
        // Get fields to update, if none then exit
        $aFields = array_keys($aFieldsValues);
        if (empty($aFields) || is_numeric($aFields[0])) {
            return false;
        }
        // Get the values as named parameters
        $aVals = array();
        foreach ($aFieldsValues as $sKey => $mVal) {
            $aVals[':' . $sKey] = $mVal;
        }

        // Build the query
        $sReq = ($iSpecial == self::INSERT_REPLACE ? 'REPLACE' : 'INSERT') . ' ' . ($iSpecial == self::INSERT_IGNORE ? 'IGNORE' : '') . ' INTO `' . $sTable . '` (`';
        $sReq .= implode('`, `', $aFields);
        $sReq .= '`) VALUES (' . implode(', ', array_keys($aVals)) . ')';
        if ($iSpecial == self::INSERT_UPDATE) {
            $sReq .= ' ON DUPLICATE KEY UPDATE ';
            $bFirst = true;
            foreach ($aFields as $sField) {
                if (!$bFirst) {
                    $sReq .= ', ';
                }
                $sReq .= '`' . $sField . '` = :' . $sField;
                $bFirst = false;
            }
        }
        if ($lock) {
            $this->lock($sTable);
        }
        $this->execute($sReq, $aVals)->closeCursor();
        if ($lock) {
            $this->unlock();
        }

        return ($bGetId ? $this->lastInsertId() : true);
    }

    /**
     * Create an update query
     *
     * @param $sTable table name
     * @param array $aFieldsValues field to insert
     * @param array $aCond SQL where condition
     * @param int $limit SQL update limit
     * @param bool $lock lock table or not
     *
     * @return bool
     */
    public function updateDataArray($sTable, array $aFieldsValues, array $aCond = array())
    {
        $sCond = '';
        if (is_array($aCond)) {
            $bFirst = true;
            foreach ($aCond as $sKey => $mValue) {
                if (!$bFirst) {
                    $sCond .= ' AND ';
                }
                $sCond .= '`' . $sKey . '` = ' . $this->escape($mValue);
                $bFirst = false;
            }
        }
        return $this->updateData($sTable, $aFieldsValues, $sCond);
    }

    /**
     * @param $sTable table name
     * @param array $aFieldsValues field to insert
     * @param $sCond SQL where condition
     * @param int $limit SQL update limit
     * @param bool $lock lock table or not
     *
     * @return bool
     */
    public function updateData($sTable, array $aFieldsValues, $sCond)
    {
        // Get fields to update, if none then exit
        $aFields = array_keys($aFieldsValues);
        if (empty($aFields) || is_numeric($aFields[0])) {
            return false;
        }
        // Get the values as named parameters
        $aVals = array();
        foreach ($aFieldsValues as $sKey => $mVal) {
            $aVals[':' . $sKey] = $mVal;
        }

        // Build the query
        $sReq = 'UPDATE `' . $sTable . '` SET ';
        $bFirst = true;
        foreach ($aFields as $sField) {
            if (!$bFirst) {
                $sReq .= ', ';
            }
            $sReq .= '`' . $sField . '` = :' . $sField;
            $bFirst = false;
        }
        if (!empty($sCond)) {
            $sReq .= ' WHERE ' . $sCond;
        }
        $this->execute($sReq, $aVals)->closeCursor();

        return true;
    }

    /**
     * Create a delete query
     *
     * @param $sTable
     * @param array $aWhereValues
     * @param int $limit
     * @param bool $lock
     *
     * @return bool
     */
    public function deleteData($sTable, array $aWhereValues = array(), $limit = 0, $lock = false)
    {
        // Build the query
        $aVals = array();
        $sReq = 'DELETE FROM `' . $sTable . '` ';
        if (is_array($aWhereValues) && count($aWhereValues) > 0) {
            $sReq .= 'WHERE ';
            // Get the values as named parameters
            $bFirst = true;
            foreach ($aWhereValues as $sKey => $mVal) {
                if (!$bFirst) {
                    $sReq .= ' AND ';
                }
                $sReq .= '`' . $sKey . '` = :' . $sKey;
                $aVals[':' . $sKey] = $mVal;
                $bFirst = false;
            }
        }
        $sReq .= (is_int($limit) && $limit > 0) ? sprintf(' LIMIT %s', $limit) : '';
        if ($lock) {
            $this->lock($sTable);
        }
        $this->execute($sReq, $aVals)->closeCursor();
        if ($lock) {
            $this->unlock();
        }
        return true;
    }

    /**
     * Create a select * simple query
     * @param string table name
     * @param array SQL where condition
     * @param mixed string or array of values to select
     * @param integer SQL select limit
     *
     * @return array
     */
    public function selectData($sTable, array $aWhereValues = array(), $mSelectValues = '*', $limit = 0)
    {
        // Build the query
        $aVals = array();
        $sReq = 'SELECT ';
        // build select string
        if (is_array($mSelectValues) && count($mSelectValues) > 0) {
            foreach ($mSelectValues as $sKey => $mVal) {
                $mSelectValues[$sKey] = '`' . $mVal . '`';
            }
            $sReq .= implode(',', $mSelectValues);
        } else {
            $sReq .= $mSelectValues;
        }
        $sReq .= ' FROM `' . $sTable . '` ';
        // build where clause
        if (is_array($aWhereValues) && count($aWhereValues) > 0) {
            $sReq .= 'WHERE ';
            // Get the values as named parameters
            $bFirst = true;
            foreach ($aWhereValues as $sKey => $mVal) {
                if (!$bFirst) {
                    $sReq .= ' AND ';
                }
                $sReq .= '`' . $sKey . '` = :' . $sKey;
                $aVals[':' . $sKey] = $mVal;
                $bFirst = false;
            }
        }
        $sReq .= (is_int($limit) && $limit > 0) ? sprintf(' LIMIT 0,%s', $limit) : '';
        return $this->fetchAll($sReq, $aVals);
    }

    /**
     * Get the database tables
     */
    public function getTables()
    {
        $aRows = $this->fetchAll('SHOW TABLES');
        $aRes = array();
        foreach ($aRows as $aRow) {
            $aRes[] = current($aRow);
        }
        return $aRes;
    }

    /**
     * Get the fields of the table
     * @param $sTable The table to describe.
     *
     * @return array
     */
    public function getFields($sTable)
    {
        $aRows = $this->fetchAll('DESC ' . $sTable);
        $aRes = array();
        foreach ($aRows as $aRow) {
            $aRes[] = $aRow['Field'];
        }
        return $aRes;
    }

    /**
     * Methode interne de gestion d'erreur
     */
    private static function onError($oObj)
    {
        $aError = $oObj->errorInfo();
        trigger_error($aError[2]);
        throw new \Exception('SQL Error : ' . $aError[2]);
        exit;
    }

    protected function getLastPdoStatement()
    {
        return $this->lastPdoStatement;
    }

    /**
     * Return SQL formated string
     *
     * @param    string    update or insert
     * @param    string    table name
     * @param    array        conf for query set
     * @param    array        conf for query where
     */
    public function getQuery($type, $tableName, $querySet = array(), $queryWhere = array())
    {
        $rv = '';
        $type = strtoupper($type);
        if (($type == 'INSERT' || $type == 'REPLACE' || $type == 'UPDATE' || $type == 'DELETE' || $type == 'SELECT') && $tableName != '') {
            $set = $where = $limit = '';
            if ($type == 'SELECT') {
                $type .= ' * FROM';
                $where = $this->getQuerySyntax($querySet, 'WHERE');
            } elseif ($type == 'DELETE') {
                $where = $this->getQuerySyntax($querySet, 'WHERE');
                $type = 'DELETE FROM';
                $limit = 'LIMIT 1';
            } else {
                $set = $this->getQuerySyntax($querySet, 'SET');
                $where = $this->getQuerySyntax($queryWhere, 'WHERE');
                if ($type == 'INSERT' || $type == 'REPLACE') { //TODO: Format INSERT queries properly - INSERT INTO `table "fields" VALUES("values")...
                    $type .= ' INTO';
                } elseif ($type == 'UPDATE') {
                    $limit = 'LIMIT 1';
                }
            }
            $rv = sprintf("%s `%s` %s %s %s", $type, $tableName, $set, $where, $limit);
            return $rv;
        }
        return $rv;
    }

    /**
     * Return SQL syntax
     *
     * @param    array        conf (field=>value)
     * @param    string    type (SET || WHERE)
     */
    private function getQuerySyntax($conf, $type)
    {
        $type = strtoupper($type);
        if (is_array($conf) && count($conf) > 0) {
            $queryConf = array();
            foreach ($conf as $field => $value) {
                $queryConf[] = sprintf("`%s`='%s'", $field, $value);
            }
            if ($type == 'SET') {
                $queryConf = $type . ' ' . implode(', ', $queryConf);
            } elseif ($type == 'WHERE') {
                $queryConf = $type . ' ' . implode(' AND ', $queryConf);
            }
            return $queryConf;
        } else {
            return false;
        }
    }

    public function isConnected()
    {
        return !is_null($this->pdo) && false !== $this->pdo;
    }

    public function query($query)
    {
        return $this->pdo->query($query);
    }
}