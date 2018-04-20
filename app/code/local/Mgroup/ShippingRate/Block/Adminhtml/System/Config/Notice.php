<?php

class Mgroup_ShippingRate_Block_Adminhtml_System_Config_Notice extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'shippingrate/system/config/notice.phtml';

    public function render(Varien_Data_Form_Element_Abstract $fieldset)
    {
        return $this->toHtml();
    }

}
