<?php

class Mgroup_Recaptcha_Helper_Data extends Mage_Core_Helper_Abstract {

    public function Validate_Captcha($response) {
        $verifyResponse = $this->getRemoteForm('https://www.google.com/recaptcha/api/siteverify?secret=' . Mage::getStoreConfig("mgroup_recaptcha/general/secretkey") . '&response=' . $response);
        $responseData = json_decode($verifyResponse);
        if ($responseData->success):
            return true;
        else:
            return false;
        endif;
    }

    public function getRemoteForm($url) {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandle, CURLOPT_ENCODING, "");
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_AUTOREFERER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $result;
    }

}
