<?php

class TV_Subscriber_Model_Observer {

    /**
     * @param Varien_Event_Observer $observer
     */
    public function newsletterSubscriberSave(Varien_Event_Observer $observer) {
        $subscriber = $observer->getSubscriber();
        //$countryCode = Mage::app()->getRequest()->getParam('country_code');
        $signType = Mage::app()->getRequest()->getParam("sign_type");
        $storeType = Mage::app()->getRequest()->getParam("store_type");
        
        //$subscriber->setCountryCode($countryCode);
        $subscriber->setSignType($signType);
        $subscriber->setStoreType($storeType);
        print_r($subscriber);exit();
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addGridColumn(Varien_Event_Observer $observer) {
        $block = $observer->getBlock();
        
        if ($block && $block instanceof Mage_Adminhtml_Block_Newsletter_Subscriber_Grid) {
            /** @var Mage_Adminhtml_Block_Newsletter_Subscriber_Grid $block */
//            $block->addColumnAfter('country_code', array(
//                'header' => 'Country',
//                'type' => 'options',
//                'index' => 'country_code',
//                'options' => Mage::helper('tv_subscriber')->getCountriesOptionHash()
//                    ), 'lastname');
            /** @var Mage_Adminhtml_Block_Newsletter_Subscriber_Grid $block */
            $block->addColumnAfter('sign_type', array(
                'header' => 'Sign Type',
                'index' => 'sign_type',
                    ), 'lastname');
            $block->addColumnAfter('store_type', array(
                'header' => 'Store Type',
                'index' => 'store_type',
                    ), 'sign_type');
        }
    }

}
