<?php
class Cartin24_Common_Model_Feed extends Mage_AdminNotification_Model_Feed {

	public function getFeedUrl() {
        $this->_feedUrl = 'http://www.cartin24.com/feeds/feed.rss';
        return $this->_feedUrl;
    }
    
    public function observe() {
       $model  = Mage::getModel('common/feed');
       $model->checkUpdate();
    }
	
}
