<?php

class Pektsekye_PartFinder_ProductController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        // a brute-force protection here would be nice

       /* parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }*/
        $identifier = trim($this->getRequest()->getPathInfo(), '/');
        $fullpath = trim($this->getRequest()->getRequestUri());
        if (strpos($identifier, 'partfinder') !== false && strpos($fullpath, 'cat=') === false) {
            $params = $this->getRequest()->getParams();

            $table = Mage::getSingleton('core/resource')->getTableName('partfinder/db');
            $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
            $sql = $conn->select()
                ->from(array('cp' => $table))
                ->where('cp.pf_year = ?', $params['year'])
                ->where('cp.pf_make = ?', $params['make'])
                ->where('cp.pf_model = ?', $params['model'])
                ->where('cp.pf_submodel = ?', $params['submodel']);
            $rowData = $conn->fetchRow($sql);


            if (!$rowData) {
                return false;
            }

            $isSecure = Mage::app()->getStore()->isCurrentlySecure();
            $this->getResponse()->setRedirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, $isSecure). $rowData['identifier']);
        }
        return;
    }
    public function listAction()
    {
        $selectedValues = $this->getSelectedValues();

        if (is_null($selectedValues)){
            $this->getResponse()->setRedirect(Mage::getBaseUrl());
            return;
        }


        $rowId = Mage::getModel('partfinder/selector')->fetchRowId($selectedValues);

        if (is_null($rowId)){
            $this->getResponse()->setRedirect(Mage::getBaseUrl());
            return;
        }


        $pIds  = Mage::getModel('partfinder/restriction')->getProductIds($rowId);
        $pIds2 = Mage::getModel('partfinder/attribute')->getProductIds($rowId);

        $pIds = array_unique(array_merge($pIds, $pIds2));

        if (count($pIds) == 0){
            $this->getResponse()->setRedirect(Mage::getBaseUrl());
            return;
        }


        if ($this->categorySearchEnabled() && $this->isCategorySelected())
            Mage::register('partfinder_applied_category', $this->getCategory());


        if ($this->wordSearchEnabled() && $this->isSearchQuery())
            Mage::helper('partfinder')->saveQuery();


        Mage::register('partfinder_selected_values', $selectedValues);
        Mage::register('partfinder_product_ids', $pIds);
        Mage::register('partfinder_layer', $this->getLayer());

        $this->loadLayout();
        $year = $this->getRequest()->getParam('year');
        $make = $this->getRequest()->getParam('make');
        $model = $this->getRequest()->getParam('model');
        $head = $this->getLayout()->getBlock('head');
        $head->setTitle($year.' '.$make.' '.$model);
        $head->setKeywords($year.' '.$make.' '.$model);
        $head->setDescription($year.' '.$make.' '.$model);
        $this->renderLayout();
    }
    public function listdetailAction()
    {
      $selectedValues = $this->getSelectedValues();
      
      if (is_null($selectedValues)){
		    $this->getResponse()->setRedirect(Mage::getBaseUrl());      
        return;
      }

      
      $rowId = Mage::getModel('partfinder/selector')->fetchRowId($selectedValues);  

      if (is_null($rowId)){
		    $this->getResponse()->setRedirect(Mage::getBaseUrl());      
        return;
      }



      $pIds  = Mage::getModel('partfinder/restriction')->getProductIds($rowId);
      $pIds2 = Mage::getModel('partfinder/attribute')->getProductIds($rowId);

      $pIds = array_unique(array_merge($pIds, $pIds2));
      
      if (count($pIds) == 0){
		    $this->getResponse()->setRedirect(Mage::getBaseUrl());          
        return;
      }

        $cat = $this->getRequest()->getParam('cat');
        if($cat){
            $productCollection = Mage::getResourceModel('catalog/product_collection')
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'inner')
                ->addAttributeToFilter('category_id', array('in' => $cat))
                ->addAttributeToFilter('entity_id', array('in' => $pIds))
                ->addAttributeToSelect('*');
            $newpIds = array();
            foreach($productCollection as $product){
                $newpIds[] = $product->getEntityId();
            }
            $pIds = $newpIds;
        }
//        Zend_Debug::dump($pIds);

      if ($this->categorySearchEnabled() && $this->isCategorySelected())              
        Mage::register('partfinder_applied_category', $this->getCategory());                    
               
     
      if ($this->wordSearchEnabled() && $this->isSearchQuery())
        Mage::helper('partfinder')->saveQuery();


      Mage::register('partfinder_selected_values', $selectedValues);
      Mage::register('partfinder_product_ids', $pIds);
      Mage::register('partfinder_layer', $this->getLayer());

        $this->loadLayout();
      $this->renderLayout();
      
    }





    public function getSelectedValues()
    {
    
      $values = array();
      $collection = Mage::getModel('partfinder/selector_level')->getCollection();
      foreach($collection as $level){
        $value = $this->getRequest()->getParam($level->getUrlParameter());
        if (!is_null($value)){
          $values[] = $value;          
        } else {         
          return null;
        }
      }
      
      return $values;
    }  




    public function getCategory()
    {
      $categoryId = $this->getRequest()->getParam(Mage::getModel('catalog/layer_filter_category')->getRequestVar());
      if (!is_null($categoryId)){      
        $category = Mage::getModel('catalog/category')->load((int) $categoryId);
        if (!is_null($category->getId()))
          return $category;
      }      
       return null;     
    }
    


    public function getLayer()
    {  	    	    
      if ($this->wordsearchEnabled() && $this->isSearchQuery()){            
        $layerName = 'partfinder/searchlayer';                         
      } else {
        $layerName = 'partfinder/categorylayer';      
      }

      return Mage::getSingleton($layerName);
    }    
    
    
    
    public function categorySearchEnabled()
    {
      return Mage::getStoreConfig('partfinder/frontendsettings/categorysearch') == 1;
    }      
    
    public function wordSearchEnabled()
    {
      return Mage::getStoreConfig('partfinder/frontendsettings/wordsearch') == 1;
    }      
    
    public function isSearchQuery()
    {
      return !is_null($this->getSearchQuery());
    }  
    
    public function getSearchQuery()
    {
      return $this->getRequest()->getParam(Mage::helper('catalogsearch')->getQueryParamName());
    }  
    
    public function isCategorySelected()
    {
      return !is_null($this->getCategory());
    }  
    
    
  
}
