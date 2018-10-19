<?php
/**
 * Cartin24
 * @package    Cartin24_MultiWishlist
 * @copyright  Copyright (c) 2015-2016 Cartin24. (http://www.cartin24.com)
 * @license    http://opensource.org/licenses/osl-3.0.php   Open Software License (OSL 3.0)
 */
class Cartin24_Multiwishlist_Block_Share_Wishlist extends Mage_Wishlist_Block_Share_Wishlist
{
  /**
     * Create wishlist item collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createWishlistItemCollection()
    {
       $collection = $this->_getWishlist()->getItemCollection();
       if($this->getRequest()->getParam('mid')){
			$id = $this->getMid();
		    if($this->getMid() == 'default')
				$id = 0;
		   $collection ->addFieldToFilter('multi_wishlist_id', $id);
		}
        return  $collection;
    }
}
