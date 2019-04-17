<html>
    <head>
        <title>Payofix</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>-->
        <!--<script src="assets/js/bootstrap.js" type="text/javascript"></script>-->
        <script src="assets/js/prototype.js" type="text/javascript"></script>
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>-->
        <script src="assets/js/ccard.js" type="text/javascript"></script>
        <script src="assets/js/validation.js" type="text/javascript"></script>        
    </head>
    <body>
        <div class="container">
            <div class="form-group clearfix">&nbsp;</div>
            <div class="form-group clearfix">&nbsp;</div>
            <form class="" method="post" action="checkout.php" id="co-payment-form">
                <div class="row">
                    <div class="col-sm-8">
                        <div style="border: 1px solid #ccc; border-top: 3px solid #3B6691; padding: 15px">
                            <label>Payment Amount: <span style="color: #FF4C4C"> <?php echo $grand_total . " " . $currency_code; ?></span></label>
                        </div>
                        <p>
                            We process payments for goods and services provided by<br/>
                            Your payment details will be encrypted securely by Payofix<br/>
                            The field marked with * must be filled
                        </p>
                        <h2>Order Information</h2>
                        <form action="" method="post" class="" >
                            <div class="input-box">
                                <select id="ccsave_cc_type" name="payment[cc_type]" title="Credit Card Type" class="required-entry validate-cc-type-select" autocomplete="off" style="">
                                    <!--<option value="">--Please Select--</option>-->
                                    <option value="MC">MasterCard</option>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Card Number:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control input-text validate-cc-number validate-cc-type" name="card_number" value="" id="ccsave_cc_number"/>
                                </div>
                            </div>

                            <div class="form-group row input-box">
                                <label class="col-sm-3 col-form-label">Card Month/Year:</label>
                                <div class="col-sm-3">
                                    <select id="ccsave_expiration" name="card_month" class="form-control month required-entry validate-cc-exp">
                                        <option value="">Month</option>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?php echo $i ?>"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
                                        <?php endfor; ?>
                                    </select>  
                                </div>
                                <div class="col-sm-3">
                                    <select id="ccsave_expiration_yr" name="card_year" class="form-control year required-entry">
                                        <option value="">Year</option>
                                        <?php for ($i = date("Y"); $i < date("Y") + 5; $i++): ?>
                                            <option value="<?php echo $i ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Card Code:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control  required-entry" name="card_code" value="111"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" >Firstname:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control  required-entry" name="firstname" value="<?php echo $customer_firstname ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Lastname:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control required-entry" name="lastname" value="<?php echo $customer_lastname ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Email:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control required-entry validate-email" name="email" value="<?php echo $customer_email ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Country:</label>
                                <div class="col-sm-9 input-box">
                                    <select name="country" class="form-control  required-entry">
                                        <option value="">---</option>
                                        <?php foreach ($countries as $key => $value) { ?>
                                            <option <?php if ($order_country_code == $key) echo 'selected' ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">State:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control " name="state" value="<?php echo $region ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">City:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control" name="city" value="<?php echo $city ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Zip:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control" name="zip" value="<?php echo $postcode ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Address 1:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control  required-entry" name="address_1" value="<?php echo $custAddr ?>"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Address 2:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control" name="address_2"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Telephone:</label>
                                <div class="col-sm-9 input-box">
                                    <input type="text" class="form-control" name="telephone" value="<?php echo $telephone; ?>"/>
                                </div>
                            </div>



                            <input type="hidden" name="csid" id="csid">

                            <input type="submit" class="btn btn-primary" name="Pay" value="Make Payment"/>

                        </form>
                    </div>

                    <div class="col-sm-4">
                        <div style="border: 1px solid #ccc; border-top: 3px solid #3B6691; padding: 0">
                            <div class="col-12">
                                <h2>Order Information</h2>
                                <p>Order No: <?php echo $IncrementId ?></p>
                                <p>
                                    Please do not refresh this page while your payment is being processed.<br/>
                                    Refresh the page can result in your card being charged twice!<br/>
                                    Once your payment has been completed successfully you will receive a congfirmation email.<br/>
                                    Your payment detail will be securely transmitted. Due to exchange rates, the amount billed can vary<br/>
                                    slightly. Thanh you very much for using our payment gateway.<br/>
                                </p>
                                <div class="form-group"></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="IncrementId" value="<?php echo $IncrementId ?>" />
                    <input type="hidden" name="grand_total" value="<?php echo $grand_total ?>" />
                    <input type="hidden" name="currency_code" value="<?php echo $currency_code ?>" />
                </div>
            </form>
            <script>
//                jQuery(document).ready(function ($) {
                //var validator = new Validation($('co-payment-form'));
                var validator = new Validation('co-payment-form');

//                })
            </script>
            <script type="text/javascript" src="https://secure.payofix.com/assets/global/scripts/csid2.js"></script>
        </div>
    </body>
</html>

