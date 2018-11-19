<?php

class Mgroup_SubscribeLink_IndexController extends Mage_Core_Controller_Front_Action {

    public function subscribeAction() {
//      http://mdev1.tv.net/resubscribe/index/subscribe/email/thang123@gmail.com
        $email = base64_decode($this->getRequest()->getParam('email'));
        $session = Mage::getSingleton('core/session');
        $customerSession = Mage::getSingleton('customer/session');
        try {
            $ownerId = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email)
                    ->getId();
            if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                Mage::throwException($this->__('This email address is already assigned to another user.'));
            }

//      $status = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
            $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
            if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                $session->addSuccess($this->__('Confirmation request has been sent.'));
            } else {
                $session->addSuccess($this->__('Thank you for your subscription.'));
            }
        } catch (Mage_Core_Exception $e) {
            Mage::log($e, null, 're-subsriber.log', true);
        }
        $this->_redirect('/');
    }

}
