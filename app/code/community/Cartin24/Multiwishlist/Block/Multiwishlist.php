<?php
/**
 * Cartin24
 * @package    Cartin24_MultiWishlist
 * @copyright  Copyright (c) 2015-2016 Cartin24. (http://www.cartin24.com)
 * @license    http://opensource.org/licenses/osl-3.0.php   Open Software License (OSL 3.0)
 */
 
class Cartin24_Multiwishlist_Block_Multiwishlist extends Mage_Core_Block_Template{

	public function getMyWishlist(){
		
		$model = Mage::getModel('multiwishlist/wishlistlabels');
		$result = $model->getWishlistLabels();
		return $result;
	
	
	}
}
	 
