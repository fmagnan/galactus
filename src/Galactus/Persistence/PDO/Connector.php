<?php

namespace Galactus\Persistence\PDO;

use Galactus\Persistence\Storable;

class Connector
{
    const INSERT_IGNORE = 1;
    const INSERT_UPDATE = 2;
    const INSERT_REPLACE = 3;
    const CHARSET_UTF8 = 'UTF8';

    protected $pdo;
    protected $host;
    protected $user;
    protected $pass;
    protected $database;
    protected $lastPdoStatement;

    /**
     * Create a PDOMySQL instance
     */
    public function __construct($host, $user, $pass, $dbName, $charset = 'utf8')
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $dbName;
        // dynamic port detection
        $port = '';
        if (strpos($host, ':') !== false) {
            list($host, $port) = explode(':', $host);
        }
        $options = [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "' . $charset . '"'];
        // connexion to database with PDO
        $dsnMask = 'mysql:dbname=%s;host=%s;%s';
        $dsn = sprintf($dsnMask, $dbName, $host, empty($port) ? '' : ';port=' . $port);
        $this->pdo = new \PDO($dsn, $user, $pass, $options);
    }

    /**
     * Returns the number of rows affected by the last SQL statement
     */
    public function rowCount(\PDOStatement $mRes)
    {
        return $mRes->rowCount();
    }

    /**
     * [Deprecated] Retrocompatibility for MySQL object
     * Create a delete query string and execute it
     * @param $table name of table
     * @param $where string clause where to limit the query
     * @param $lock
     * @return boolean if query was ok
     */
    public function delete($table, $where = null, $lock = false)
    {
        $rqt = false;
        if (!empty($table)) {
            if ($lock) {
                $this->lock($table);
            }
            $query = sprintf('DELETE FROM `%s` WHERE %s', $table, $where);
            $rqt = $this->execute($query)->closeCursor();
            if ($lock) {
                $this->unlock();
            }
        }
        return $rqt;
    }

    /**
     * Retrocompatibility for MySQL object
     */
    public function errno()
    {
        return $this->pdo->errorCode();
    }

    /**
     * Retrocompatibility for MySQL object
     */
    public function error()
    {
        return $this->pdo->errorInfo();
    }

    /**
     * Retrocompatibility for MySQL object
     * Fetches the next row from a result set
     * returns an array indexed by column name as returned in your result set
     */
    public function fetch_assoc(\PDOStatement $mRes)
    {
        return $this->fetch($mRes, \PDO::FETCH_ASSOC);
    }

    /**
     * Retrocompatibility for MySQL object
     * Fetches the next row from a result set
     * returns an array indexed by column number as returned in your result set, starting at column 0
     */
    public function fetch_row(\PDOStatement $mRes)
    {
        return $this->fetch($mRes, \PDO::FETCH_NUM);
    }

    /**
     * Experimental, retrocompatibility for MySQL object
     * Returns metadata for a column in a result set
     */
    public function field_name($index, \PDOStatement $mRes)
    {
        $meta = $mRes->getColumnMeta($index);
        $name = $meta['name'];

        return $name;
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
     * [Deprecated] retrocompatibility for MySQL object
     * Fetches the first row from a query
     */
    public function getRow($query, $fetchAssoc = true)
    {
        $style = $fetchAssoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM;
        return $this->fetchOne($query, array(), $style);
    }

    /**
     * Retrocompatibility for MySQL object
     * Fetches a row
     */
    public function getRowById($table, $field, $value, $fetchAssoc = true)
    {
        $style = $fetchAssoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM;
        $query = 'SELECT * FROM `' . $table . '` WHERE `' . $field . '`=?';
        $sta = $this->execute($query, $value);
        return $sta->fetch($style);
    }

    /**
     * Retrocompatibility for MySQL object
     * Fetches multiple rows
     */
    public function getRowsById($table, $field, $value, $fetchAssoc = true)
    {
        $style = $fetchAssoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM;
        $query = 'SELECT * FROM `' . $table . '` WHERE `' . $field . '`=?';
        $sta = $this->execute($query, array($value));
        return $sta->fetchAll($style);
    }

    /**
     * [Deprecated] retrocompatibility for MySQL object
     * Fetches multiple rows from a query
     */
    public function getRows($query, $fetchAssoc = true)
    {
        $style = ($fetchAssoc) ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM;
        return $this->fetchAll($query, array(), $style);
    }

    /**
     * [Deprecated] Retrocompatibility for MySQL object
     */
    private function getDBList($req, $uniqueCol)
    {
        $result = array();
        $sta = $this->execute($req);
        if ($uniqueCol) {
            while ($r = $this->fetch_row($sta)) {
                $result[] = $r[0];
            }
        } else {
            while ($r = $this->fetch_assoc($sta)) {
                $result[] = $r;
            }
        }
        return $result;
    }

    /**
     * @deprecated
     * [Deprecated] Retrocompatibility for MySQL object
     * Prends un tableau qu'il retourne sous forme de string pr�fix� de WHERE / AND
     * Dans le cas d'un OR, le tableau doit contenir une entr�e de type :
     * ( nom = 'toto' OR prenom = 'tata' )
     *
     * @param array $where
     * @return string
     */
    public function getWhere($where = array())
    {
        if (!is_array($where)) {
            return '';
        }
        if (count($where) == 0) {
            return '';
        }
        return 'WHERE ' . implode(' AND ', $where);
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
     * Retrocompatibility for MySQL object
     * Return an array with tables which have the same parent prefix
     */
    public function getTableFamily($parent)
    {
        return $this->getDBList("SHOW TABLES LIKE '$parent%'", true);
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
        $iSpecial = ($ignore) ? self::INSERT_IGNORE : false;
        return $this->insertData($table, $datas, $iSpecial, true, $lock);
    }

    /**
     * Retrocompatibility for MySQL object
     * Returns the ID of the last inserted row or sequence value
     */
    public function insert_id()
    {
        return $this->lastInsertId();
    }

    /**
     * @deprecated
     * Retrocompatibility for MySQL object
     * Create a query string and execute it
     */
    public function insertupdate($type, $table, array $datas, $where = null, $lock = false)
    {
        if ($type == 'INSERT') {
            return $this->insertData($table, $datas, false, true, $lock);
        } elseif ($type == 'UPDATE') {
            return $this->updateData($table, $datas, $where, 0, $lock);
        }
    }

    /**
     * Lock specified table
     */
    public function lock($table)
    {
        $query = sprintf('LOCK TABLES %s WRITE', $table);
        return ($this->execute($query)->closeCursor());
    }

    /**
     * Log into file the string in param
     */
    public function log($rqt, $params = array())
    {
        if (preg_match('/(insert|update)/i', $rqt)) {
            $logMysql = fopen(PATH . '/www/' . $this->user . ".sql", "a+");
            $rqt = str_replace("\t", '', $rqt);
            fputs($logMysql, "############\n");
            fputs($logMysql, $rqt . ';');
            if (count($params > 0)) {
                fputs($logMysql, "\n" . print_r($params, true));
            }
            fputs($logMysql, "\n");
            fclose($logMysql);
        }
    }

    /**
     * [Deprecated] Not compatible with MySQL object
     */
    public function num_fields($r = false)
    {
        $count = $r->columnCount();

        return $count;
    }

    /**
     * Retrocompatibility for MySQL object
     * Return number of rows for the current result set
     */
    public function num_rows(\PDOStatement $rqt)
    {
        return $this->rowCount($rqt);
    }

    /**
     * Retrocompatibility for MySQL object
     * Make a simple SELECT to check if database is up
     */
    public function ping()
    {
        try {
            $this->pdo->query('SELECT 1');
        } catch (\PDOException $e) {
            self::onError($this->pdo);
        }
        return true;
    }

    /**
     * Retrocompatibility for MySQL object
     * This method is an alias of PDOMySQL::execute
     *
     * @param string $query
     *
     * @return \PDOStatement
     */
    public function query($query)
    {
        return $this->execute($query);
    }

    /**
     * [Deprecated] Retrocompatibility for MySQL object
     * Return infos in array for specified query string
     */
    public function select_hash($table, $where = '')
    {
        $query = sprintf('SELECT * FROM `%s` WHERE %s', $table, ($where ? $where : 1));
        return $this->fetchAll($query);
    }

    /**
     * Retrocompatibility for MySQL object
     * Truncate the specified table
     */
    public function truncate($table)
    {
        return $this->execute("TRUNCATE TABLE `$table`");
    }

    /**
     * Retrocompatibility for MySQL object
     * Execute an unbuffered query
     */
    public function unbuffered_query($query)
    {
        $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $r = $this->execute($query);
        $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        return $r;
    }

    /**
     * Unlock specified table
     */
    public function unlock()
    {
        return ($this->execute('UNLOCK TABLES')->closeCursor());
    }

    /**
     * Retrocompatibility for MySQL object
     * Create an update query string and execute it
     */
    public function update($table, $datas, $where = '', $lock = false)
    {
        return $this->updateData($table, $datas, $where, 0, $lock);
    }

    /**
     * Retrocompatibility for MySQL object
     * Create an update query string on a key/value and execute it
     */
    public function updateRowById($table, $datas, $key, $value, $limit = 0, $lock = false)
    {
        $where = sprintf('`%s`=%s', $key, $this->escape($value));
        return $this->updateData($table, $datas, $where, $limit, $lock);
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

    protected function getDatabase()
    {
        return $this->database;
    }

    protected function getHost()
    {
        return $this->host;
    }

    protected function getPass()
    {
        return $this->pass;
    }

    protected function getPdo()
    {
        return $this->pdo;
    }

    protected function getUser()
    {
        return $this->user;
    }

    /**
     * Get login information.
     */
    public function getInfo($param)
    {
        $info = array(
            'host' => $this->host,
            'user' => $this->user,
            'base' => $this->database
        );
        if (!isset($info[$param])) {
            return '';
        }
        return $info[$param];
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
}