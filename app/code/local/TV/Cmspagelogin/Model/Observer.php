<?php

/*
 * Created on : Mar 23, 2018, 10:51:54 AM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 */

class TV_Cmspagelogin_Model_Observer {

    public function cmsField($observer) {

        //get CMS model with data
        $model = Mage::registry('cms_page');

        $helper = Mage::helper('cms');

        //get form instance
        $form = $observer->getForm();
        //create new custom fieldset 'tv_content_fieldset'
        $fieldset = $form->addFieldset('tv_content_fieldset', array('legend' => Mage::helper('cms')->__('Have To Login?'), 'class' => 'fieldset-wide'));
        //add new field
        $fieldset->addField('is_showpage', 'select', array(
            'name' => 'is_showpage',
            'label' => Mage::helper('cms')->__('Select an Option'),
            'title' => Mage::helper('cms')->__('Select an Option'),
            'disabled' => false,
            //set field value
            'value' => $model->getIsShowpage(),
            'values' => array(
                array(
                    'value' => 0,
                    'label' => $helper->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => $helper->__('Yes'),
                )
            )
        ));
    }

    public function cmsPageSaveAfter($observer) {
        $request = $observer->getRequest()->getPost();
//        var_dump($request);die();
        $model = $observer->getPage();
        $model->setIsShowpage($request["is_showpage"]);
    }

}
