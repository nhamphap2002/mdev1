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
    $orderid = $_POST['orderid'];

    $data = array(
        //Filled by your cart implementation
        'order_number' => $_POST['IncrementId'], //Unique order number
        'description' => 'Order #' . $_POST['IncrementId'], //Order description
        'amount' => $_POST['grand_total'], //Amount (2 decimal points allowed)
        'currency' => $_POST['currency_code'], //ISO currency code
        'ip_addr' => $_SERVER['REMOTE_ADDR'],
        //If callback is supported on your account
        'callback_url' => URLBASE . "callback.php?orderid=" . $orderid . "&signature=" . $signInfo . "message=error" . "&amount=" . $_POST['grand_total'],
        'fail_url' => URLBASE . "fail.php?orderid=" . $orderid . "&signature=" . $signInfo . "message=error" . "&amount=" . $_POST['grand_total'],
        'success_url' => URLBASE . "success.php?orderid=" . $orderid . "&signature=" . $signInfo . "&grand_total=" . $_POST['grand_total'],
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

    //echo '<pre>';print_r($data);exit();

    try {
        $api = new PayofixAdapter(username, apiKey);
        $message = '';
        $res = $api->makeCCPayment($data);
        //print_r($res);exit();
        if (isset($res["transaction_id"])) {
            //$message .= "Transaction ID: " . $res["transaction_id"] . " ";
            saveTransactions($_GET['orderid'], $res["transaction_id"]);
            $col["status"] = 1;
            $table = TB_ORDERLINKS;
            $where = ' id= ' . $orderid;
            $db->updateSql($col, $table, $where);
            $data_email = array();
            $data_email['invoice_number'] = $res["transaction_id"];
            $data_email['customer_email'] = $data['email'];
            $data_email['order_number'] = $_POST['IncrementId'];
            sendEmailAdmin($data_email);
        }
        if ($res["success"] === false) {
            $message .= "Transaction ID: " . $res["transaction_id"] . " ";
            $message .= "
<p>Your payment is " . $res["message"] . ".</p>
<p>For any questions regarding the transaction, please do not hesitate to contact us.</p";
            $col["status"] = 2;
            $table = TB_ORDERLINKS;
            $where = ' id= ' . $orderid;
            $db->updateSql($col, $table, $where);
        } else {
            if ($res["result"] == 1) {
                $message .= "<p>Your payment is successful.</p>
<p>The transaction was charged by <strong>Payment Descriptor</strong> on your credit card statement.</p>
<p>** The payment may be processed in a different currency, may not be USD. 
The final amount charged on your card statement will be closed to the original total of your order USD " . $_POST['grand_total'] . " (get this from payment amount).
Additional charges may be added by your bank for foreign currency processing. 
(Please note: Pharmaceutical products is a sensitive subject. 
Please keep your order details confidential and DO NOT MENTION THE ORDERED PRODUCTS OR WEBSITE NAME to your bank or credit card company.
Thanks!)
 </p>
<p>
For any questions or disputes regarding the transaction, please do not hesitate to contact us.
<p>
";
            } elseif ($res["result"] == 0) {

                if (isset($res["redirect"])) {
                    header("Location:" . $res["redirect"]);
                    exit();
                } else {
                    $message .= "Pending. Awaiting callback";
                    $col["status"] = 3;
                    $table = TB_ORDERLINKS;
                    $where = ' id= ' . $orderid;
                    $db->updateSql($col, $table, $where);
                }
            } elseif ($res["result"] == 2) {
                $col["status"] = 4;
                $table = TB_ORDERLINKS;
                $where = ' id= ' . $orderid;
                $db->updateSql($col, $table, $where);
                $message .= "
<p>Your payment is " . $res["message"] . ".</p>
<p>For any questions regarding the transaction, please do not hesitate to contact us.</p";
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
