<?php

/*
 * Created on : Mar 29, 2018, 9:05:26 AM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 */

class TV_Cmspagelogin_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid {

    public function setCollection($collection) {
        // fetching fax from billing address
        $collection->joinAttribute('billing_company', 'customer_address/company', 'default_billing', null, 'left');
        parent::setCollection($collection);
    }

    protected function _prepareColumns() {
        // adding fax column to the customer grid
        $this->addColumn('billing_company', array(
            'header' => Mage::helper('customer')->__('Company'),
            'width' => '150px',
            'index' => 'billing_company',
            'type' => 'text',
        ));

        // show the fax column after ZIP column
        $this->addColumnsOrder('billing_company', 'name');

        return parent::_prepareColumns();
    }

}

?>