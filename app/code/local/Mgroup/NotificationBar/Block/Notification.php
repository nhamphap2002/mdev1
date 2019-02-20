<?php
class Mgroup_NotificationBar_Block_Notification extends Mage_Core_Block_Template
{
    const BASE_CONFIG_PATH = "notification_bar/";

    public function getSettings()
    {
        return Mage::getStoreConfig(self::BASE_CONFIG_PATH."general", Mage::app()->getStore());
    }

    public function getContent($content)
    {
        $processor = Mage::helper('cms')->getBlockTemplateProcessor();
        $message = $processor->filter($content);
        return $message;
    }
}
