<?php

/*
 * ------------------------------------------------------
 *  Configuration file for the Payofix API
 * ------------------------------------------------------
 *
 * DO NOT edit the system reserved parameters.
 *
 */


/*
 * ------------------------------------------------------
 *  System reserved parameters
 * ------------------------------------------------------
 */

$config['url'] = 'https://secure.payofix.com';


$config['required_keys'] = array(
    "CC" => array(
        'amount',
        'currency',
        'card_number',
        'card_code',
        'card_month',
        'card_year',
        'csid',
        'description',
        'email',
        'firstname',
        'lastname',
        'ip_addr',
        'order_number',
        'telephone',
        'country',
        'state',
        'city',
        'zip',
        'address_1',
        'address_2',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_zip',
        'shipping_address_1',
        'shipping_address_2'
    ),

    "eCheck" => array(
        'amount',
        'currency',        
        'description',
        'email',
        'firstname',
        'lastname',
        'ip_addr',
        'order_number',
        'telephone',
        'country',
        'state',
        'city',
        'zip',
        'address_1',
        'address_2',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_zip',
        'shipping_address_1',
        'shipping_address_2',
        'account_number',
        'routing_number'
    ),

    "callback" => array(
        'transaction_id',
        'order_number',
        'amount',
        'currency',
        'result',        
        'signature'
    ),

    "voucher" => array(
        'amount',
        'currency',        
        'description',
        'email',
        'firstname',
        'lastname',
        'ip_addr',
        'order_number',
        'telephone',
        'country',
        'state',
        'city',
        'zip',
        'address_1',
        'address_2',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_zip',
        'shipping_address_1',
        'shipping_address_2',
        'redirect_url'
    ),
    "transaction" => array(
        "id",
        "order_number"
    ),
    "DirectDebit" => array(
        'amount',
        'currency',        
        'description',
        'email',
        'firstname',
        'lastname',
        'ip_addr',
        'order_number',
        'telephone',
        'country',
        'state',
        'city',
        'zip',
        'address_1',
        'address_2',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_zip',
        'shipping_address_1',
        'shipping_address_2',
        'callback_url',
        'success_url',
        'fail_url'
    ),
);

$config['signature_keys'] = array(
    "CC" => array(
        'order_number',
        'currency',
        'amount',
        'firstname',
        'lastname',
        'card_number',
        'card_year',
        'card_month',
        'card_code',
        'email'
    ),

    "eCheck" => array(
        'order_number',
        'currency',
        'amount',
        'firstname',
        'lastname',
        'account_number',
        'routing_number',        
        'email'
    ),
    "voucher" => array(),
    "transaction" => array(
        'id',
        'order_number'
    ),
    "DirectDebit" => array(
        'order_number',
        'currency',
        'amount',        
        'email'
    ),
    "refund" => array(        
        'order_number'
    ),
);
