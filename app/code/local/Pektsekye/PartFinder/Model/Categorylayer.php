<?php


class Pektsekye_PartFinder_Model_Categorylayer extends Mage_Catalog_Model_Layer
{

    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = Mage::getResourceModel('catalog/product_collection');
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
        return $collection;
    }


    public function prepareProductCollection($collection)
    {
        parent::prepareProductCollection($collection);
    
    		$collection->addIdFilter(Mage::registry('partfinder_product_ids'));
 	
        return $this;
    }



    public function checkSubCategories($category = null)
    { 

        if (is_null($category))  $category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId());

        $categories = $category->getChildrenCategories();

        $this->getProductCollection()->addCountToCategories($categories);

        $categoryOptions = array();
        foreach ($categories as $category) {
            if ($category->getIsActive() && $category->getProductCount()) {
                $categoryOptions[] = array(
                'id'    => (int) $category->getId(),
                'title' => $category->getName()
                );
            }
        }

        return $categoryOptions;

    }
  
  
    
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableInSearchFilter()
            ->addVisibleFilter();
        return $collection;
    }
    
}
