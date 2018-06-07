<?php

class Mgroup_Editsalesemail_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $postValues = $this->getRequest()->getParams();
        if ($postValues['emailadress'] != '' AND $postValues['order_id'] != "" AND $postValues['form_key'] != "") {
            $order = Mage::getModel('sales/order')->load($postValues['order_id'], 'increment_id');
            $order->setCustomerEmail($postValues['emailadress'])->save();
            echo true;
        } else {
            echo false;
        }
    }
}
