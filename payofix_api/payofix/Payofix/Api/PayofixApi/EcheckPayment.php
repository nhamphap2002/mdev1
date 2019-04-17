<?php

namespace Payofix\Api\PayofixApi;

use Payofix\Api\PayofixApi;

/**
 * Echeck payment class
 *
 * Class EcheckPayment
 * @package Api
 */
class EcheckPayment extends PayofixApi
{
	
    /*
     * Endpoint
     */
    protected $endpoint = "/api/echeck";

	/**
     * Get Payment type
     * @return string
     */
    protected function getPaymentType()
    {
        return "eCheck";
    }

}