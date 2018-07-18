<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "cate_countdown",  array(
    "type"     => "datetime",
    "backend"  => "eav/entity_attribute_backend_datetime",
    "frontend" => "",
    "label"    => "Count Down",
    "input"    => "date",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

	));
$installer->endSetup();
	 