<?PHP

class Factory {

    function &DB() {
        static $instance;
        $option = array(
            "dbHost" => DB_HOST,
            "dbUser" => DB_USER,
            "dbPassword" => DB_PASS,
            "dbDatabaseName" => DB_NAME
        );
        $instance = Mysql::getInstance($option);

        return $instance;
    }

    function &USER($whUr = "member") {
        static $instance;

        $instance = & userObj::getInstance($whUr);

        return $instance;
    }

//function
}

//end class
?>