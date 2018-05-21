<?php

/*
 * Created on : May 1, 2018, 4:16:36 PM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 */

class Mgroup_Gcrawler_Helper_Observer extends Mage_Core_Helper_Abstract {

    public function parseCustomVars($observer) {
        $response = $observer->getEvent()->getFront()->getResponse();
        $html = $response->getBody();

        $callback = function($matches) {
            $var = Mage::getModel('core/variable');
            $var->setStoreId(Mage::app()->getStore()->getId());
            return $var->loadByCode($matches[1])->getValue('html');
        };

        if (!$this->isAdmin()) {
            $html = preg_replace_callback(
                    "/{{customVarCode code=(.*?)}}/U", '$callback', $html
            );
        }
        $response->setBody($html);
        Mage::app()->setResponse($response);

        return $this;
    }

    public function isAdmin() {
        if (Mage::app()->getStore()->isAdmin()) {
            return true;
        }

        if (Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }

        return false;
    }

}
