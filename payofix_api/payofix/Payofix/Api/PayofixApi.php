<?php

namespace Payofix\Api;

/**
 * Main API class, does all the work.
 *
 * Class PayofixApi
 * @package Api
 */
class PayofixApi
{

    /*
     * Payofix Username
     */
    protected $username;

    /*
     * Payofix API Key
     */
    protected $apiKey;

    /*
     * API URL
     */
    protected $url = null;


    /*
     * Endpoint
     */
    protected $endpoint = "/api";

    /**
     * Constructor
     * @param username
     * @param string Username
     * @param string API Key
     * @param string API url override
     */
    public function __construct($username, $apiKey, $url = null)
    {
        $this->username = $username;
        $this->apiKey = $apiKey;

        if($url === null){
            $url = PayofixConfig::getInstance()->get("url");
        } 
        $this->url = rtrim($url, "/");
    }



    /**
     * Make a payment
     * @param array data
     */
    public function pay($data)
    {
        $type = $this->getPaymentType();
        $this->validateData($data, $type);        
        $data["username"] = $this->username;
        $data = $this->signData($data, $type);
        return $this->executeRequest($data);
    }

    /**
     * Refund
     * @param string order number
     * @return array
     */
    public function refund($orderNumber){
        $data = array(
            "order_number" => $orderNumber
        );
        $data["username"] = $this->username;
        $data = $this->signData($data, "refund");
        return $this->executeRequest($data, "/refund");
    }



    /**
     * Get Payment type
     * @return string
     */
    protected function getPaymentType()
    {
        return "CC";
    }



    /**
     * Validate data
     * @param array data
     * @param string validation group
     */
    protected function validateData($data, $type)
    {
        if(!is_array($data)){
            throw new \Exception("Invalid data: data must be array");
        }
        
        $requiredKeys = PayofixConfig::getInstance()->get("required_keys.".$type);
        $errors = array();
        foreach($requiredKeys as $rk){
            if(!array_key_exists($rk, $data)){
                $errors[] = $rk;
            }
        }

        if(!empty($errors)){
            throw new \Exception("Missed fields: ".implode(", ", $errors));
        }
    }


    /**
     * Sign data with the signature
     * @param array data
     * @param string Signature group
     * @return array
     */
    protected function signData($data, $type)
    {
        $signKeys = PayofixConfig::getInstance()->get("signature_keys.".$type);
        if(!$signKeys){
            return $data;
        }
        $signStr = $this->username;
        foreach ($signKeys as $k) {
            $signStr .= $data[$k];
        }
        $signStr .= $this->apiKey;

        $data["signature"] = hash('sha256', $signStr);

        return $data;
    }


    /**
     * Executes the request via curl
     * and returns the api response.
     *
     * @param $data
     * @return json
     */
    protected function executeRequest($data, $actionUrl = "")
    {   
        $data = http_build_query($data);

        $url = $this->url."/".trim($this->endpoint, "/")."/".ltrim($actionUrl, "/");

        $website = $_SERVER['REQUEST_URI'];

        $curl = curl_init($url);        
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_REFERER, $website);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  

        $response = curl_exec($curl);
        curl_close($curl);      
        //var_dump($response);
        if($response === false){
            throw new \Exception("Error connecting Payofix endpoint");
        }
        $arr = @json_decode($response, true);
        if(!$arr){
            throw new \Exception("Invalid response from Payofix");
        }

        return $arr;
    }



    /**
     * Verify callback
     * @param array data
     */
    public function verifyCallback($data)
    {
        $this->validateData($data, "callback");
        $signature = $this->getCallbackSignature($data);
        if($signature != $data["signature"]){            
            throw new \Exception("Invalid signature");
        }
    }


    /**
     * Create callback signature
     *
     * @param $data
     * @return string hashed
     */
    public function getCallbackSignature($data)
    {
        $signStr = $data['result'];
        $signStr .= $data['order_number'];
        $signStr .= $data['currency'];
        $signStr .= $data['amount'];        
        $signStr .= $this->apiKey;
        $signInfo = hash('sha256', $signStr);

        return $signInfo;
    }


    /**
     * Check CC Payment
     * @param array data
     * @return array
     */
    public function check($data){
        $type = "transaction";
        $this->validateData($data, $type);
        $data["username"] = $this->username;
        $data = $this->signData($data, $type);


        $url = $this->url."/".ltrim($this->endpoint, "/");
        $url .= "/transaction/".$data["id"]."/".$data["signature"];

        $curl = curl_init($url);        
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, false);                
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  

        $response = curl_exec($curl);
        curl_close($curl);

        
        if($response === false){
            throw new \Exception("Error connecting Payofix endpoint");
        }

        $arr = @json_decode($response, true);
        if(!$arr){
            throw new \Exception("Invalid response from Payofix");
        }

        return $arr;   
    }






}
