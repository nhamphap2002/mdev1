<?php

session_start();
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', '19mg_med4pacific');
define('TIMEZONE', 'Asia/Ho_Chi_Minh');

define('TB_USER', 'payofix_users');
define('TB_ORDERS', 'payofix_orders');
define('TB_ORDERLINKS', 'payofix_orderlinks');
define("NOW", date("Y-m-d H:i:s"));
define("Limit", 10);
define("Currency_Code", 'USD');
define("URLBASE", 'http://www.med4pacific.com/payofix_api/');
define("URLPAY", 'http://www.med4pacific.com/payofix_api/?orderid=');
define("username", 'bolagrpltd.sandb');
define("apiKey", 'b8f6d61362648d402da4a12b3c5d40f1');


include_once 'lib/Factory.class.php';
include_once 'lib/Mysql.class.php';
$Factory = new Factory();
$db = $Factory->DB();

