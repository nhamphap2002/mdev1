<?php

namespace Payofix\Api\PayofixApi;

use Payofix\Api\PayofixApi;

/**
 * Credit card payment class
 *
 * Class CCPayment
 * @package Api
 */
class CCPayment extends PayofixApi
{
	
    /*
     * Endpoint
     */
    protected $endpoint = "/api";

	/**
     * Get Payment type
     * @return string
     */
    protected function getPaymentType()
    {
        return "CC";
    }


    
}