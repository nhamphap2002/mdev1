<?php

namespace Payofix\Api\PayofixApi;

use Payofix\Api\PayofixApi;

/**
 * Direct Debit payment class
 *
 * Class DirectDebitPayment
 * @package Api
 */
class DirectDebitPayment extends PayofixApi
{
	
    /*
     * Endpoint
     */
    protected $endpoint = "/api/directdebit";

	/**
     * Get Payment type
     * @return string
     */
    protected function getPaymentType()
    {
        return "DirectDebit";
    }

}