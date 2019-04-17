<?php

namespace Payofix\Api;

/**
 * Adapter for the PayofixApi class.
 * User application will interact with this class only,
 * via the Payofixable interface.
 *
 * Class PayofixAdapter
 * @package Api
 *
 */
class PayofixAdapter
{
    /*
     * Payofix Username
     */
    private $username;

    /*
     * Payofix API Key
     */
    private $apiKey;

    /*
     * Endpoint URL
     */
    private $url = null;


    /**
     * Get the Config singleton instance
     * and initialize the main Api class
     * @param string Username
     * @param string API Key
     * @param string endpoint URL to override default
     */
    public function __construct($username, $apiKey, $url = null)
    {
        $this->username = $username;
        $this->apiKey = $apiKey;

        if($url !== null){
            $this->url = $url;
        }
    }



    /**
     * Make Credit Card payment
     * @param array data
     * @return array
     */
    public function makeCCPayment($data){
        $api = new PayofixApi\CCPayment($this->username, $this->apiKey, $this->url);
        return $api->pay($data);
    }


    /**
     * Make eCheck Payment
     * @param array data
     * @return array
     */
    public function makeEcheckPayment($data){
        $api = new PayofixApi\EcheckPayment($this->username, $this->apiKey, $this->url);
        return $api->pay($data);
    }

    /**
     * Make Voucher Payment
     * @param array data
     * @return array
     */
    public function makeVoucherPayment($data){
        $api = new PayofixApi\VoucherPayment($this->username, $this->apiKey, $this->url);
        return $api->pay($data);
    }


    /**
     * Make Direct Debit Payment
     * @param array data
     * @return array
     */
    public function makeDirectDebitPayment($data){
        $api = new PayofixApi\DirectDebitPayment($this->username, $this->apiKey, $this->url);
        return $api->pay($data);
    }



    


    /**
     * Verify callback data
     * @param array $_GET data
     * @return boolean
     */
    public function verifyCallback($data){
        
        $api = new PayofixApi($this->username, $this->apiKey, $this->url);
        return $api->verifyCallback($data);        
    }


    /**
     * Check Credit Card payment status
     * @param array data
     * @return array
     */
    public function checkTransaction($data){
        $api = new PayofixApi($this->username, $this->apiKey, $this->url);
        return $api->check($data);
    }


    /**
     * Refund
     * @param string order number
     * @return array
     */
    public function refund($orderNumber){
        $api = new PayofixApi($this->username, $this->apiKey, $this->url);
        return $api->refund($orderNumber);
    }

}