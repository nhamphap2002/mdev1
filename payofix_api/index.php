<?php

//ini_set("display_errors", 1);
//error_reporting(E_ALL);

require_once ( "../app/Mage.php" );
Mage::app();

if (isset($_GET['createtbl'])) {
    createTable();
    exit();
}
include_once 'config.php';
$table = TB_ORDERLINKS;
if (!empty($_GET['orderid'])) {
    $orderid = $_GET['orderid'];
    $where = "WHERE id = " . $orderid . ' AND status = 0';
    $sql = "SELECT * FROM " . $table . " " . $where;
    $db->query($sql);
    $order = $db->loadObject();
    if ($order) {
        //print_r($order);
        include_once 'countries.php';

        include_once 'form.php';
    } else {
        echo 'Not found Order!';
        exit();
    }
} else {
    echo 'Please provide Order number';
    exit();
}

function createTable() {
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $query = "CREATE TABLE `payofix_transactions` ( `id` INT NOT NULL AUTO_INCREMENT , `order_id` INT NOT NULL , `transaction_id` VARCHAR(100) NOT NULL , `created_date` DATETIME NOT NULL , PRIMARY KEY (`id`))";

    $writeConnection->query($query);
}

?>