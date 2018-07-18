<?php

$installer = $this;
$installer->startSetup();
$attribute = array(
    'type' => 'datetime',
    'label' => 'Count Down',
    'input' => 'text',
    'backend' => 'TV_Countdown/entity_attribute_backend_datetime',
//    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => "",
    'group' => "General Information"
);
$installer->addAttribute('catalog_category', 'count_down', $attribute);
$installer->endSetup();
?>