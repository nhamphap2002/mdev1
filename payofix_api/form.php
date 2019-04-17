<html>
    <head>
        <title>Payofix</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="assets/js/bootstrap.js" type="text/javascript"></script>
        <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
        <script src="assets/js/script.js" type="text/javascript"></script>
        <style>
            .required,
            .error{
                color: red;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="form-group clearfix">&nbsp;</div>
            <div class="form-group clearfix">&nbsp;</div>
            <form class="" method="post" action="checkout.php" id="co-payment-form">
                <div class="row">
                    <div class="col-sm-8">
                        <div style="border: 1px solid #ccc; border-top: 3px solid #3B6691; padding: 15px">
                            <strong><label>Payment Amount: <span style="color: #FF4C4C"> <?php echo $order->price . " " . Currency_Code; ?></span></label></strong>
                        </div>
                        <p></p>
                        <p>
                            Your payment details will be encrypted securely by <span style="text-decoration: underline">Payofix</span>.<br>
                            The field marked with * must be filled.
                        </p>
                        <h2>Order Information</h2>
                        <form action="" method="post" class="" >
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Card Number:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="card_number" value="" required placeholder="Card Number"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Card Month/Year:<span class="required">*</span></label>
                                <div class="col-sm-3">
                                    <select required name="card_month" class="form-control validate-cc-exp">
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?php echo $i ?>"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
                                        <?php endfor; ?>
                                    </select>  
                                </div>
                                <div class="col-sm-3">
                                    <select required name="card_year" class="form-control" id="ccsave_expiration_yr">
                                        <?php for ($i = date("Y"); $i < date("Y") + 5; $i++): ?>
                                            <option value="<?php echo $i ?>" <?php if ($i == 1) echo "selected" ?>><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">CVV2:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <input required type="text" class="form-control card_code" name="card_code" value="" placeholder="CVV"/>
                                    <img src="assets/img/cvv.png" alt="" style=" position: absolute; top: 2px; right: 17px; ">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" >Firstname:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="firstname" value="<?php echo $customer_firstname ?>" required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Lastname:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="lastname" value="<?php echo $customer_lastname ?>" required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Email:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control email" name="email" value="<?php echo $customer_email ?>" required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Country:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <select name="country" class="form-control" required id="pay_country">
                                        <option value="">---</option>
                                        <?php foreach ($countries as $key => $value) { ?>
                                            <option <?php if ($order_country_code == $key) echo 'selected' ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">State:<span class="required">*</span></label>
                                <div class="col-sm-9 pay_state">
                                    <input type="text" class="form-control" name="state" value="<?php echo $region ?>" required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">City:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="city" value="<?php echo $city ?>" required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Zip:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="zip" value="<?php echo $postcode ?>" required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Address 1:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="address_1" value="<?php echo $custAddr ?>" required/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Address 2:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="address_2"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Telephone:<span class="required">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control required" name="telephone" value="<?php echo $telephone; ?>"/>
                                </div>
                            </div>



                            <input type="hidden" name="csid" id="csid">
                            <input type="hidden" name="orderid" id="orderid" value="<?php echo $orderid; ?>">

                            <input type="submit" class="btn btn-primary" name="Pay" value="Make Payment"/>

                        </form>
                    </div>

                    <div class="col-sm-4">
                        <div style="border: 1px solid #ccc; border-top: 3px solid #3B6691; padding: 0">
                            <div class="col-12">
                                <h2>Order Information</h2>
                                <p><strong>Order No: <?php echo $order->orderid ?></strong></p>
                                <p>
                                    Please do not refresh this page while your payment is being processed.
                                </p>
                                <p>
                                    Refresh the page can result in your card being charged twice!
                                </p>
                                <p>
                                    Once your payment has been completed successfully, you will receive a confirmation email.
                                </p>
                                <p>
                                    Your payment details will be securely transmitted. Due to currency exchange rates, the amount billed can vary
                                </p>
                                <p>
                                    slightly. Thank you very much for using our payment gateway.
                                </p>
                                <div class="form-group"></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="IncrementId" value="<?php echo $order->website . '_' . $order->orderid ?>" />
                    <input type="hidden" name="grand_total" value="<?php echo $order->price ?>" />
                    <input type="hidden" name="currency_code" value="<?php echo Currency_Code ?>" />
                </div>
            </form>
            <script type="text/javascript" src="https://secure.payofix.com/assets/global/scripts/csid2.js"></script>
        </div>
    </body>
</html>

