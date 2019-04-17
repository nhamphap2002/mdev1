<?php

namespace Payofix\Api\PayofixApi;

use Payofix\Api\PayofixApi;

/**
 * Credit card payment class
 *
 * Class CCPayment
 * @package Api
 */
class VoucherPayment extends PayofixApi
{
	
    /*
     * Endpoint
     */
    protected $endpoint = "/voucher/api";

	/**
     * Get Payment type
     * @return string
     */
    protected function getPaymentType()
    {
        return "voucher";
    }

}