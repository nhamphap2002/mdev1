<?php
class Mgroup_Recaptcha_Model_Observer
{
	public function Validate_Captcha(Varien_Event_Observer $observer){
		$g_response=Mage::app()->getRequest()->getParam('g-recaptcha-response');
        if(isset($g_response) && !empty($g_response)):
            if (!(Mage::helper('recaptcha')->Validate_Captcha($g_response))):
                Mage::getSingleton('core/session')->addError('Please click on the reCAPTCHA box.');
                $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
                Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                Mage::app()->getResponse()->sendResponse();
                exit;
            endif;
        else:
            $observer->getEvent()->setData(null);
            Mage::getSingleton('core/session')->addError('Please click on the reCAPTCHA box.');
            $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            Mage::app()->getResponse()->sendResponse();
            exit;
        endif;
	}
}
