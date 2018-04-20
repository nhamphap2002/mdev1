<?php
class Mgroup_ShippingRate_Block_Adminhtml_System_Config_Buttonrun extends Mage_Adminhtml_Block_System_Config_Form_Field{

    protected function _construct()
    {

        $this->setTemplate('shippingrate/system/config/buttonrun.phtml');
        return parent::_construct();
    }
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());
        return $this->_toHtml();
    }


    public function getAjaxUpdateUrl(){
       return Mage::helper('adminhtml')->getUrl('adminhtml/shippingrate_script/index');
   }

}
