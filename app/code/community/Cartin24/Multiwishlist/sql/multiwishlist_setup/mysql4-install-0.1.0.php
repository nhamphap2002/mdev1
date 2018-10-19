<?php
/**
 * Cartin24
 * @package    Cartin24_MultiWishlist
 * @copyright  Copyright (c) 2015-2016 Cartin24. (http://www.cartin24.com)
 * @license    http://opensource.org/licenses/osl-3.0.php   Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('cartin24_multiwishlist')};
CREATE TABLE {$this->getTable('cartin24_multiwishlist')} (
  `multi_wishlist_id` int(11) unsigned NOT NULL auto_increment,
   `customer_id` int(11) unsigned NOT NULL default '0',
   `wishlist_name` text NOT NULL default '',

   PRIMARY KEY (`multi_wishlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->getConnection()->addColumn(
        $this->getTable('wishlist/item'), 
        'multi_wishlist_id',     
        'int(11) NOT NULL DEFAULT 0'  
        );

$installer->endSetup(); 
