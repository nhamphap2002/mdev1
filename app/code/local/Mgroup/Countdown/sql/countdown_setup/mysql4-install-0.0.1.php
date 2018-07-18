<?php
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
try {
    $setup->addAttribute('catalog_category', 'mg_countdown', array(
        'group' => 'General Information',
        'input' => 'text',
        'type' => 'text',
        'label' => 'Time countdown',
        'backend' => '',
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    ));
} catch (Exception $e) {
    Mage::logException($e);
}
$installer->endSetup();
?>