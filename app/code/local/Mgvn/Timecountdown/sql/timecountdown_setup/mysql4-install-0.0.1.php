<?php

$installer = $this;
$installer->startSetup();
$attribute = array(
    'type' => 'datetime',
    'label' => 'Time countdown',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => "",
    'group' => "General Information"
);
$installer->addAttribute('catalog_category', 'mgvn_countdown', $attribute);
$installer->endSetup();
?>