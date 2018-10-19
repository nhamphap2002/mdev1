<?php
/**
 * Cartin24
 * @package    Cartin24_MultiWishlist
 * @copyright  Copyright (c) 2015-2016 Cartin24. (http://www.cartin24.com)
 * @license    http://opensource.org/licenses/osl-3.0.php   Open Software License (OSL 3.0)
 */
 
class Cartin24_Multiwishlist_Model_Wishlistlabels extends Mage_Core_Model_Abstract {

	public function _construct()
    {
        parent::_construct();
        $this->_init('multiwishlist/wishlistlabels');
    }
    public function getWishlistLabels(){
		$customerData = Mage::getSingleton('customer/session')->getCustomer();
		$collection = Mage::getModel('multiwishlist/wishlistlabels')->getCollection()
 				     ->addFieldToFilter('customer_id', $customerData->getId());
		$this->setCollection($collection);
		return $collection;
	}
	
	public function getLabelIds(){
		
		 $wishlist = $this->getWishlistLabels();
		 $multiWishlist = array();
		 $multiWishlist[0] = 0;
		 foreach ($wishlist as $item)
		 {
			
			if(! in_array($item->getId(), $multiWishlist))
				$multiWishlist[] = $item->getId();
				
		 } 
		 return $multiWishlist;
	}
	public function getWishlistItemsCollection($id){
		
	    $customer = Mage::getSingleton('customer/session')->getCustomer();
		if($customer->getId())
		{
		 $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
		 return $wishlist->getItemCollection()->addFieldToFilter('multi_wishlist_id', $id);
		 
		}
		return 0;
	}
}
	 
