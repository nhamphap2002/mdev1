<?php
class Cartin24_Multiwishlist_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
     * Get the jquery status 
	*/
	public function enablejQuery(){
        $config = Mage::getStoreConfig('multiwishlist/general/enablejquery');
	 	if($config == "1")
			return true;
		return false;
   }

}
