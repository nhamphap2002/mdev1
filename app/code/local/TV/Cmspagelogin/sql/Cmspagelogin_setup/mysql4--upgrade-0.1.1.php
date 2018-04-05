<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
$table = $installer->getTable('cms_page');

$conn->addColumn(
        $table, 'is_showpage', Varien_Db_Ddl_Table::TYPE_TINYINT, '', array(
    'nullable' => false,
    'default' => '0',
        ), 'On/OFF Login function'
);

$installer->endSetup();
