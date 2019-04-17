<?PHP

class Mysql {

    public $_resource;
    public $_cursor;
    var $_quoted = array();
    var $_hasQuoted = null;
    var $_nameQuote = '`';
    var $_errorNum;

    /**
     * The number of transactions that have been 'started'.
     * This is used to work out whether a 'COMMIT' will really do a commit or just a 'SAVEPOINT'.
     *
     * @see StartTransaction
     * @see CommitTransaction
     * @see RollbackTransaction
     */
    var $_transaction_counter = 0;
    var $_transaction_names = array();

    public static function getInstance($option) {

        static $instances;

        $signature = serialize($option);

        if (empty($instances[$signature])) {
            $instances[$signature] = new self($option);
            //echo "<BR>CALL NEW DB<BR>";
        }
        //$instances[$signature] = new self($option);

        return $instances[$signature];
    }

    function __construct($option) {
        static $instance;
        $dbHost = $option["dbHost"];
        $dbUser = $option["dbUser"];
        $dbPassword = $option["dbPassword"];
        $dbDatabaseName = $option["dbDatabaseName"];
        //echo $dbUser;

        if (!function_exists('mysql_connect')) {
            //Log::error("The MySQL adapter mysql is not available.");
            echo "The MySQL adapter mysql is not available.";
            return;
        }

        // connect to the server
        if (!($this->_resource = @mysql_connect($dbHost, $dbUser, $dbPassword, true))) {
            //Log::error("Could not connect to MySQL.");
            echo "Could not connect to MySQL.";
            return;
        }

        if (!mysql_select_db($dbDatabaseName, $this->_resource)) {
            //Log::error("Could not connect to database.");
            echo "Could not connect to database.";
            return false;
        }

        $instance &= $this->_resource;
        return $instance;
    }

    public function query($query) {

        mysql_query("SET time_zone =  " . TIMEZONE);

        $result = mysql_query($query, $this->_resource);

        $this->_cursor = $result;

        if ($result == false) {
            $this->_errorNum = mysql_errno($this->_resource);
        }
        return $this->_cursor;
    }

    function loadResult() {
        if ($this->getNumRows() > 0) {
            // Get the first row from the result set as an array.
            if ($row = $this->fetchArray()) {
                $ret = $row[0];
            }
            // Free up system resources and return.
            $this->freeResult($this->_cursor);
        } else {
            $ret = 0;
        }

        return $ret;
    }

    public function loadObject($class = 'stdClass') {
        if ($this->getNumRows() > 0) {
            // Get the first row from the result set as an array.
            if ($object = $this->fetchObject()) {
                $ret = $object;
            }
            // Free up system resources and return.
            $this->freeResult($this->_cursor);
        } else {
            $ret = 0;
        }

        return $ret;
    }

    public function loadObjectList($key = '', $class = 'stdClass') {
        if ($this->getNumRows() > 0) {
            // Get all of the rows from the result set as objects of type $class.
            while ($row = $this->fetchObject($this->_cursor, $class)) {
                if ($key) {
                    $array[$row->$key] = $row;
                } else {
                    $array[] = $row;
                }
            }

            // Free up system resources and return.
            $this->freeResult($this->_cursor);
        } else {
            $array[] = 0;
        }

        return $array;
    }

    public function querySql($query, $dcall = '') {
        mysql_query("SET time_zone =  " . TIMEZONE);

        //echo $query."<BR><BR>";
        //Log::debug($query) ;
        $result = mysql_query($query, $this->_resource);

        $this->_cursor = $result;
        if ($result == false) {
            //trigger_error('Uncovered an error in your SQL query script: "' . $this->error() . '"');
            $this->_errorNum = mysql_errno($this->_resource);
            //Log::error(mysql_errno($this->_resource) . ": " . mysql_error($this->_resource) );
            //Log::error($query);
        }

        if (empty($dcall)) {
            return $this->_cursor;
        } else {
            if ($this->getNumRows() > 0) {
                $rows = array();
                while ($r = $this->fetchAssoc())
                    $rows[] = $r;

                return $rows;
            } else
                return false;
        }//end if empty($dcall)
    }

    public function selectSql($fields, $table, $where = false, $orderby = false, $limit = false) {
        mysql_query("SET NAMES utf8");
        if (is_array($fields))
            $fields = "`" . implode($fields, "`, `") . "`";

        $orderby = ($orderby) ? " ORDER BY " . $orderby : '';
        $where = ($where) ? " WHERE " . $where : '';
        $limit = ($limit) ? " LIMIT " . $limit : '';

        $this->querySql("SELECT " . $fields . " FROM " . $table . " " . $where . $orderby . $limit);

        //echo $this;
        //Log::debug("SELECT " . $fields . " FROM " . $table . " " . $where . $orderby . $limit);

        if ($this->getNumRows() > 0) {
            $rows = array();

            while ($r = $this->fetchAssoc())
                $rows[] = $r;
            return $rows;
        } else
            return false;
    }

    public function insertSql(array $fields_values, $table) {
        mysql_query("SET NAMES utf8");
        if (count($fields_values) < 0)
            return false;

        foreach ($fields_values as $field => $val)
            $fields_values[$field] = $this->escapeString($val);

        $fmtsql = 'INSERT INTO ' . $this->nameQuote($table) . ' ( %s ) VALUES ( %s ) ';
        $fields = array();
        foreach ($fields_values as $k => $v) {
            if ($v === NULL) {
                continue;
            }

            $fields[] = $this->nameQuote($k);
            $values[] = $this->isQuoted($k) ? $this->Quote($v) : $v;
        }

        $this->_quoted = array();

        //Log::debug(sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) )  ) ;
        //echo sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) );
        if ($this->querySql(sprintf($fmtsql, implode(",", $fields), implode(",", $values))))
            return true;
        else
            return false;
    }

//end function insertSql

    public function updateSql(array $fields_values, $table, $where = false, $updateNulls = false) {
        mysql_query("SET NAMES utf8");
        if (count($fields_values) < 0)
            return false;

        $fmtsql = 'UPDATE ' . $this->nameQuote($table) . ' SET %s %s';
        $tmp = array();

        //foreach($fields_values as $field => $val)
        //$fields_values[$field] = $this->escapeString($val);

        foreach ($fields_values as $k => $v) {
            if ($v === NULL) {
                if ($updateNulls) {
                    $val = 'NULL';
                } else {
                    continue;
                }
            } else {
                $v = $this->escapeString($v);
                $val = $this->isQuoted($k) ? $this->Quote($v) : $v;
            }

            $tmp[] = $this->nameQuote($k) . '=' . $val;
        }

        $where = ($where) ? " WHERE " . $where : '';
        $this->_quoted = array();

        //Log::debug( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) ) ;
        if ($this->querySql(sprintf($fmtsql, implode(",", $tmp), $where)))
            return true;
        else
            return false;
    }

//end updateSql

    public function deleteSql($table, $where = false, $limit = 1) {
        mysql_query("SET NAMES utf8");
        $where = ($where) ? " WHERE " . $where : '';
        $limit = ($limit) ? " LIMIT " . $limit : '';

        if ($this->querySql("DELETE FROM `" . $table . "`" . $where . $limit))
            return true;
        else
            return false;
    }

//end function deleteSql

    function getNumRows($cur = null) {
        return mysql_num_rows($cur ? $cur : $this->_cursor);
    }

    public function fetchAssoc($query = false) {
        $this->resCalc($query);
        return mysql_num_rows($query);
    }

    public function fetchArray($cursor = null) {
        return mysql_fetch_row($cursor ? $cursor : $this->_cursor);
    }

    public function fetchObject($cursor = null, $class = 'stdClass') {
        return mysql_fetch_object($cursor ? $cursor : $this->_cursor, $class);
    }

    public function freeResult($cursor = null) {
        mysql_free_result($cursor ? $cursor : $this->_cursor);

        if ((!$cursor) || ($cursor === $this->_cursor)) {
            $this->_cursor = null;
        }
    }

    private function resCalc(&$result) {
        if ($result == false)
            $result = $this->_cursor;
        else {
            if (gettype($result) != 'resource')
                $result = $this->querySql($result);
        }

        return;
    }

    function addQuoted($quoted) {
        if (is_string($quoted)) {
            $this->_quoted[] = $quoted;
        } else {
            $this->_quoted = array_merge($this->_quoted, (array) $quoted);
        }
        $this->_hasQuoted = true;
    }

    public function escapeString($str) {
        return mysql_real_escape_string($str, $this->_resource);
    }

    function nameQuote($s) {
        // Only quote if the name is not using dot-notation
        if (strpos($s, '.') === false) {
            $q = $this->_nameQuote;
            if (strlen($q) == 1) {
                return $q . $s . $q;
            } else {
                return $q{0} . $s . $q{1};
            }
        } else {
            return $s;
        }
    }

    function isQuoted($fieldName) {
        if ($this->_hasQuoted) {
            return in_array($fieldName, $this->_quoted);
        } else {
            return true;
        }
    }

    function Quote($text, $escaped = true) {
        return '\'' . ($escaped ? $this->getEscaped($text) : $text) . '\'';
    }

    function getEscaped($text, $extra = false) {
        return $text;
    }

    function getAffectedRows() {
        return mysql_affected_rows($this->_resource);
    }

    function getLastId() {
        return mysql_insert_id($this->_resource);
    }

    function ping() {
        return mysql_ping($this->_resource);
    }

    /**
     * Start a transaction
     *
     * Method will start a transaction
     * If a transaction is already in progress, then it will issue a "SAVEPOINT" command.
     *
     * @access public
     * @return bool TRUE if the transaction was successfully created, FALSE otherwise
     */
    function StartTransaction() {
        /**
         * If there are no transactions open, start one up.
         */
        if ($this->_transaction_counter == 0) {
            $this->_transaction_counter++;
            return (bool) $this->querySql("BEGIN");
        }

        /**
         * If there is a transaction open, work out a new "name" and issue a "SAVEPOINT" command.
         */
        $name = $this->_generate_transaction_name();
        $this->_transaction_counter++;
        return (bool) $this->querySql("SAVEPOINT " . $name);
    }

    /**
     * Commit all open transactions and save points
     *
     * This will commit all save points and open transactions until everything is done.
     * By issuing a "COMMIT" statement, the db will automatically commit everything it has done.
     * We need to clear the transaction counter & transaction names.
     *
     * @return Boolean Returns whether the "COMMIT" call was successful or not.
     */
    function CommitAllTransactions() {
        $this->_transaction_counter = 0;
        $this->_transaction_names = array();
        return (bool) $this->querySql("COMMIT");
    }

    /**
     * Commit a transaction
     *
     * Method will commit a transaction if it's the last transaction in progress.
     * If more than one transaction is in progress, then it will simply "ignore" the commit (there is no way to "commit" a savepoint or sub-transaction).
     * This will just decrement the number of transactions we have open and remove the last transaction name from the queue.
     *
     * @access public
     * @return bool TRUE if the transaction was successfully commited, FALSE otherwise
     */
    function CommitTransaction() {
        /**
         * If there are no transactions open, return false.
         */
        if ($this->_transaction_counter < 1) {
            return false;
        }

        if ($this->_transaction_counter == 1) {
            $this->_transaction_counter--;
            return (bool) $this->querySql("COMMIT");
        }

        /**
         * If we're in a transaction, all we need to do is get rid of the last 'savepoint' name
         * We can't actually "commit" a savepoint.
         */
        $name = array_pop($this->_transaction_names);
        $this->_transaction_counter--;
        return true;
    }

    /**
     * Rollback a transaction
     *
     * Method will completely rollback a transaction if it's the last one in progress.
     * If more than one transaction is in progress, then it will rollback to the last savepoint.
     *
     * @access public
     * @return bool TRUE if the transaction was successfully rolled back, FALSE otherwise
     */
    function RollbackTransaction() {
        /**
         * If there are no transactions open, return false.
         */
        if ($this->_transaction_counter < 1) {
            return false;
        }

        if ($this->_transaction_counter == 1) {
            $this->_transaction_counter--;
            return (bool) $this->querySql("ROLLBACK");
        }

        $this->_transaction_counter--;
        $name = array_pop($this->_transaction_names);
        return (bool) $this->querySql("ROLLBACK TO SAVEPOINT " . $name);
    }

    /**
     * Rollback all open transactions and save points
     *
     * This will rollback all save points and open transactions until everything is done.
     * By issuing a "ROLLBACK" statement, the db will automatically clear everything it has done.
     * We need to clear the transaction counter & transaction names.
     *
     * @return Boolean Returns whether the "ROLLBACK" call was successful or not.
     */
    function RollbackAllTransactions() {
        $this->_transaction_counter = 0;
        $this->_transaction_names = array();
        return (bool) $this->querySql("ROLLBACK");
    }

    /**
     * _generate_transaction_name
     * Generates a random transaction name for use with "SAVEPOINT" calls.
     * This saves having to name your transactions, it's all handled automatically.
     * The name is kept in the _transaction_names array so we can check it is unique and also be able to go back to a previous savepoint if necessary.
     *
     * @see _transaction_names
     * @see StartTransaction
     * @see CommitTransaction
     * @see RollbackTransaction
     *
     * @return String Returns a random transaction name starting with ''.
     */
    function _generate_transaction_name() {
        while (true) {
            $name = uniqid('');
            if (!in_array($name, $this->_transaction_names)) {
                $this->_transaction_names[] = $name;
                return $name;
            }
        }
    }

}

?>