<?php

/*
 * Created on : Mar 26, 2019, 8:09:13 AM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 */
//ini_set("display_errors", 1);
//error_reporting(E_ALL);
include_once 'config.php';
require_once ( "../app/Mage.php" );
Mage::app();

//Load API library
require_once Mage::getBaseDir('base') . '/payofix_api/payofix/autoload.php';

//require_once Mage::getBaseDir('base') . '/payofix_api/payofix/Payofix/Api/PayofixAdapter.php';

use Payofix\Api\PayofixAdapter;

if (!empty($_POST)) {

    $signStr = $data['result'];
    $signStr .= $_POST['IncrementId'];
    $signStr .= $_POST['currency_code'];
    $signStr .= $_POST['grand_total'];
    $signStr .= apiKey;
    $signInfo = hash('sha256', $signStr);
    $orderid = $_REQUEST['orderid'];

    $data = array(
        //Filled by your cart implementation
        'order_number' => $_POST['IncrementId'], //Unique order number
        'description' => 'Order #' . $_POST['IncrementId'], //Order description
        'amount' => $_POST['grand_total'], //Amount (2 decimal points allowed)
        'currency' => $_POST['currency_code'], //ISO currency code
        'ip_addr' => $_SERVER['REMOTE_ADDR'],
        //If callback is supported on your account
        'callback_url' => "http://mdev1.tv.net/payofix_api/callback.php?orderid=" . $orderid . "&signature=" . $signInfo . "message=error",
        'success_url' => "http://mdev1.tv.net/payofix_api/success.php?orderid=" . $orderid . "&signature=" . $signInfo,
        //Data submitted
        'firstname' => $_POST['firstname'],
        'lastname' => $_POST['lastname'],
        'email' => $_POST['email'],
        'telephone' => $_POST['telephone'],
        'country' => $_POST['country'],
        'state' => $_POST['state'],
        'city' => $_POST['city'],
        'zip' => $_POST['zip'],
        'address_1' => $_POST['address_1'],
        'address_2' => $_POST['address_2'],
        'shipping_country' => $_POST['country'],
        'shipping_state' => $_POST['state'],
        'shipping_city' => $_POST['city'],
        'shipping_zip' => $_POST['zip'],
        'shipping_address_1' => $_POST['address_1'],
        'shipping_address_2' => $_POST['address_2'],
        'card_number' => $_POST['card_number'],
        'card_code' => $_POST['card_code'],
        'card_month' => $_POST['card_month'],
        'card_year' => $_POST['card_year'],
        'csid' => $_POST['csid']
    );

    $table = payofix_orders;
    $cols["linkid"] = $orderid;
    $cols["firstname"] = $_REQUEST['firstname'];
    $cols["lastname"] = $_REQUEST['lastname'];
    $cols["email"] = $_REQUEST['email'];
    $cols["country"] = $_REQUEST['country'];
    $cols["state"] = $_REQUEST['state'];
    $cols["city"] = $_REQUEST['city'];
    $cols["created"] = NOW;
    $cols["status"] = 1;
    $db->insertSql($cols, $table);

    //echo '<pre>';print_r($data);exit();

    try {
        $api = new PayofixAdapter(username, apiKey);
        $message = '';
        $res = $api->makeCCPayment($data);
        //print_r($res);exit();
        if (isset($res["transaction_id"])) {
            $message .= "Transaction ID: " . $res["transaction_id"] . " ";
            //saveTransactions($_GET['orderid'], $res["transaction_id"]);
            $col["status"] = 1;
            $where = ' linkid= ' . $orderid;
            $db->updateSql($cols, $table, $where);

            $data_email = array();
            $data_email['invoice_number'] = $res["transaction_id"];
            $data_email['customer_email'] = $data['email'];
            $data_email['order_number'] = $_POST['IncrementId'];
            sendEmailAdmin($data_email);
        }
        if ($res["success"] === false) {
            $message .= $res["message"];
        } else {
            if ($res["result"] == 1) {
                $message .= "Success";
            } elseif ($res["result"] == 0) {

                if (isset($res["redirect"])) {
                    header("Location:" . $res["redirect"]);
                    exit();
                } else {
                    $message .= "Pending. Awaiting callback";
                }
            } elseif ($res["result"] == 2) {
                $message .= "Failed. Cause: " . $res["message"];
            }
        }
        $message .= '';
        include_once 'message.php';
        ?>

        <?php

    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

function saveTransactions($order_id, $transaction_id) {
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $date_now = date("Y-m-d H:i:s");
    $query = "INSERT INTO `payofix_transactions` (`id`, `order_id`, `transaction_id`, `created_date`) VALUES (NULL, '$order_id', '$transaction_id', '$date_now');";

    $writeConnection->query($query);
}

function sendEmailAdmin($data = array()) {
    $admin_name = Mage::getStoreConfig('trans_email/ident_general/name');
    $admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
    ob_start();
    include_once 'body_email.php';
    $body = ob_get_contents();
    ob_end_clean();
    $mail = Mage::getModel('core/email')
            ->setToName($admin_name)
            ->setToEmail($admin_email)
            ->setBody($body)
            ->setSubject("New Invoice #" . $data['invoice_number'] . " for Order #" . $data['order_number'])
            ->setFromEmail($admin_email)
            ->setFromName($admin_name)
            ->setType('html');
    try {
        $mail->send();
    } catch (Exception $error) {
        Mage::getSingleton('core/session')->addError($error->getMessage());
        return false;
    }
}
