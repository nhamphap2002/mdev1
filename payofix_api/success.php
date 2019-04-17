<html>
    <head>
        <title>Thank you</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="assets/js/bootstrap.js" type="text/javascript"></script>
<!--        <script src="assets/js/prototype.js" type="text/javascript"></script>
        <script src="assets/js/validation.js" type="text/javascript"></script>-->
    </head>
    <body>
        <div class="container">
            <div class="form-group clearfix">&nbsp;</div>
            <div class="form-group clearfix">&nbsp;</div>
            <div class="alert alert-success" role="alert">
                <?php
                include_once 'config.php';
                $orderid = $_GET['orderid'];
                $col["status"] = 1;
                $table = TB_ORDERLINKS;
                $where = ' id= ' . $orderid;
                //$db->updateSql($col, $table, $where);
                $message = "Success";
                echo $message;
                ?>
                <p>Your payment is successful.</p>
                <p>The transaction was charged by <strong>Payment Descriptor</strong> on your credit card statement.</p>
                <p>** The payment may be processed in a different currency, may not be USD. 
                    The final amount charged on your card statement will be closed to the original total of your order USD <?php echo $_POST['grand_total'] ?> (get this from payment amount).
                    Additional charges may be added by your bank for foreign currency processing. 
                    (Please note: Pharmaceutical products is a sensitive subject. 
                    Please keep your order details confidential and DO NOT MENTION THE ORDERED PRODUCTS OR WEBSITE NAME to your bank or credit card company.
                    Thanks!)
                </p>
                <p>
                    For any questions or disputes regarding the transaction, please do not hesitate to contact us.
                <p>
            </div>

        </div>
    </body>
</html>

