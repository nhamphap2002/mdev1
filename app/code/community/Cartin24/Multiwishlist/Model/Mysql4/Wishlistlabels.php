<?php
/**
 * Cartin24
 * @package    Cartin24_MultiWishlist
 * @copyright  Copyright (c) 2015-2016 Cartin24. (http://www.cartin24.com)
 * @license    http://opensource.org/licenses/osl-3.0.php   Open Software License (OSL 3.0)
 */

class Cartin24_Multiwishlist_Model_Mysql4_Wishlistlabels extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('multiwishlist/wishlistlabels', 'multi_wishlist_id');
    }
}
