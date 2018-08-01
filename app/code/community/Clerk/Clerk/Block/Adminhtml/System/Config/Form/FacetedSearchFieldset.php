<?php

class Clerk_Clerk_Block_Adminhtml_System_Config_Form_FacetedSearchFieldset extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $html = $this->_getHeaderHtml($element);

        if (! $this->isConfigured()) {
            $html .= Mage::helper('clerk')->__('Public and private key must be set in order to enable faceted search');
        } else {
            if (! $this->keysValid()) {
                $html .= Mage::helper('clerk')->__('Public or private key invalid');
            } else {
                foreach ($element->getSortedElements() as $field) {
                    $html.= $field->toHtml();
                }
            }
        }

        $html .= $this->_getFooterHtml($element);

        return $html;

        return parent::render($element);
    }

    /**
     * Determine if public & private keys are set and valid
     *
     * @return bool
     */
    protected function isConfigured()
    {
        return (bool) (Mage::getStoreConfig(Clerk_Clerk_Model_Config::XML_PATH_PUBLIC_KEY, $this->getStore()) && Mage::getStoreConfig(Clerk_Clerk_Model_Config::XML_PATH_PRIVATE_KEY, $this->getStore()));
    }

    /**
     * Determine if supplied keys are valid
     *
     * @return bool
     */
    protected function keysValid()
    {
        $publicKey = Mage::getStoreConfig(Clerk_Clerk_Model_Config::XML_PATH_PUBLIC_KEY, $this->getStore());
        $privateKey = Mage::getStoreConfig(Clerk_Clerk_Model_Config::XML_PATH_PRIVATE_KEY, $this->getStore());

        $keysValid = json_decode(Mage::getModel('clerk/communicator')->keysValid($publicKey, $privateKey)->getBody());

        if ($keysValid->status === 'error') {
            return false;
        }

        return true;
    }

    /**
     * Get store being configured
     *
     * @return mixed
     */
    protected function getStore()
    {
        return Mage::getSingleton('adminhtml/config_data')->getStore();
    }
}