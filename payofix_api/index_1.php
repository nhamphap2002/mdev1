<?php

//ini_set("display_errors", 1);
//error_reporting(E_ALL);

require_once ( "../app/Mage.php" );
Mage::app();

if (isset($_GET['createtbl'])) {
    createTable();
    exit();
}

if (!empty($_GET['orderid'])) {
    //$order = Mage::getModel('sales/order')->load($_GET['orderid']);
    $order = Mage::getModel('sales/order')->loadByIncrementId($_GET['orderid']);
    if (count($order->getData()) > 0) {
//echo '<pre>';
//print_r($order->getData()); 
        $grand_total = $order['grand_total'];
        $IncrementId = $order['increment_id'];
        $customer_firstname = $order['customer_firstname'];
        $customer_lastname = $order['customer_lastname'];
        $customer_email = $order['customer_email'];
        $currency_code = $order['order_currency_code'];

        $order_Address = $order->getShippingAddress();
        if ($order_Address) {
//print_r(get_class_methods($order_Address)); 
            $custAddr = $order_Address->getStreetFull();
            $region = $order_Address->getRegion();
            $order_country_code = $order_Address->getCountry();
            $postcode = $order_Address->getPostcode();
            $telephone = $order_Address->getTelephone();
            $city = $order_Address->getCity();
        }

        include_once 'countries.php';

        include_once 'form.php';
    }else{
        echo 'Not found Order!';
        exit();
    }
}else{
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