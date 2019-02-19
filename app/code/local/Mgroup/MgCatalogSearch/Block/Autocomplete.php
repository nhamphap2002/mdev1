<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Autocomplete queries list
 */
//class Mgroupsearch_CatalogSearch_Block_Autocomplete extends Mage_Core_Block_Abstract
class Mgroup_MgCatalogSearch_Block_Autocomplete extends Mage_Core_Block_Template {

    protected $_suggestData = null;
    protected $_mgData = null;

    protected function _toHtml() {

        $enabled = Mage::app()->getStore()->getConfig('mgroup_mgcatalogsearch/general/enabled');
        if ($enabled != 1) {
            return parent::_toHtml();
        }

        $html = '';
        $_img = '';
        $desc = '';
        $styleli = '';
        $stylespan = '';

        if (!$this->_beforeToHtml()) {
            return $html;
        }
        // Retrieve Suggest options
        //$rowData=Mage::getModel('autocomplete/autocomplete')->load(1);
        // $optVal= explode("-",$rowData->getSuggest_val());
        $products_limit = Mage::app()->getStore()->getConfig('mgroup_mgcatalogsearch/general/products_limit');
        $show_price = Mage::app()->getStore()->getConfig('mgroup_mgcatalogsearch/general/show_price');
        $show_image = Mage::app()->getStore()->getConfig('mgroup_mgcatalogsearch/general/show_image');
        $image_size = Mage::app()->getStore()->getConfig('mgroup_mgcatalogsearch/general/image_size');
        $show_rating = Mage::app()->getStore()->getConfig('mgroup_mgcatalogsearch/general/show_rating');
        $show_add2cart = Mage::app()->getStore()->getConfig('mgroup_mgcatalogsearch/general/show_add2cart');
        $show_short_description = Mage::app()->getStore()->getConfig('mgroup_mgcatalogsearch/general/show_short_description');
        $short_description_len = Mage::app()->getStore()->getConfig('mgroup_mgcatalogsearch/general/short_description_len');
        $headercontent = 'Most relevant searches shown'; //Mage::app()->getStore()->getConfig('mgkautocompletesection/general/headercontent');
        $footercontent = ''; //Mage::app()->getStore()->getConfig('mgkautocompletesection/general/footercontent');

        $query = Mage::helper('catalogSearch')->getQuery();
        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText()) {
            if (Mage::helper('catalogSearch')->isMinQueryLength()) {
                $query->setId(0)
                        ->setIsActive(1)
                        ->setIsProcessed(1);
            } else {

                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity() + 1);
                } else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()) {
                    $query->save();
                    Mage::getResponse()->setRedirect($query->getRedirect());
                    //  return;
                } else {
                    $query->prepare();
                }
            }
        }
        $catid = $this->getRequest()->getParam('cat');
        $suggestData = $this->getSuggestData();
        $pcollect = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        if ($catid != '') {
            $this->mgData = Mage::getSingleton('catalogsearch/layer')->getProductCollection()
                            ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                            ->addAttributeToSelect('*')
                            ->addAttributeToFilter('category_id', $catid)
                            ->addAttributeToSort('relevance', 'DESC')->setPage(1, $products_limit);
        } else {
            $this->mgData = Mage::getSingleton('catalogsearch/layer')->getProductCollection()->addAttributeToSort('relevance', 'DESC')->setPage(1, $products_limit);
        }

        //echo  $this->mgData->getSelect()->__toString();die();  
        $_helper = Mage::helper('catalog/output');

        if (!($count = count($pcollect))) {

            $html = '<ul class="search-autocomplete-inner">';
            $html .= '<div class="magik_head">No Records Found</div>';
            $html .= '</ul>';
            return $html;
        }

        $html = '<ul class="search-autocomplete-inner">';
        if ($headercontent != '')
            $html .= '<div class="gghyd_head">' . $headercontent . '</div>';
        $i = 0;
        foreach ($this->mgData as $index => $item) {
            if ($index == 0) {
                $item['row_class'] .= ' first';
            }
            if ($index == $count) {
                $item['row_class'] .= ' last';
            }
            $html .= '<li title="' . $this->htmlEscape($item->getName()) . '" ' . $styleli . '>';
            $html .= '<a class="gghyd-product-link" href="' . $item->getProductUrl() . ' " >';
            $html .= ' <div class="gghyd_element">';
            $_product = Mage::getModel('catalog/product')->load($item->getId());
            if ($show_image) {
                $html .= ' <div class="gghyd_image">';
                $html .= '<img align="left" src="' . Mage::helper('catalog/image')->init($item, 'small_image')->resize($image_size) . '" alt="' . $this->htmlEscape($item->getImageLabel()) . '" id="image">';
                $html .= ' </div>';
            }
            $html .= ' <div class="gghyd_right">';
            $pname = $item->getResource()->getAttribute('name')->getFrontend()->getValue($item);
            if (strlen($pname) > 35)
                $html .= substr($pname, 0, 35) . "...";
            else
                $html .= $pname;
            if ($show_rating) {
                $_reviewSummary = $this->getReviewSummary($_product->getId());
                if ($_reviewSummary->getReviewsCount() > 0) :
                    $html .= '<div class="ratings">';
                    $html .= '    <div class="rating-box">';
                    $html .= '        <div class="rating" style="width:' . $_reviewSummary->getRatingSummary() . '%"></div>';
                    $html .= '    </div>';
                    $html .= '    <span class="reviews">' . $_reviewSummary->getReviewsCount() . 'Review(s)</span>';
                    $html .= '</div>';
                endif;
            }
            if ($show_short_description) {
                $html .= '<div class="short-description">';
                $html .= Mage::helper('core/string')->truncate(strip_tags($_product->getShortDescription()), $short_description_len);
                $html .= '</div>';
            }
            if ($show_price) {
                $customer_group = 0;
                $callforpriceShow = FALSE;
                if (Mage::helper('customer')->isLoggedIn()) {
                    $customer_group = Mage::helper('customer')->getCustomer()->getGroupId();
                }

                $sym = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                if ($_product->getTypeId() == 'bundle') {
                    $aProductIds = $_product->getTypeInstance()->getChildrenIds($_product->getId());
                    $prices = array();
                    foreach ($aProductIds as $ids) {
                        foreach ($ids as $id) {
                            $aProduct = Mage::getModel('catalog/product')->load($id);
                            $prices[] = $aProduct->getPriceModel()->getPrice($aProduct);
                        }
                    }

                    krsort($prices);
                    $price = array_shift($prices);
                } else {
                    $today = time();
                    $toDate = $_product->getSpecialToDate();

                    if ($_product->getData('special_price') == "" && ($_product->getFinalPrice() == $_product->getPrice())):

                        $tier_qty_one = 0;
                        foreach ($_product->getTierPrice() as $tierPrice) {

                            if ($tierPrice['price_qty'] == 1)
                                $tier_qty_one = $tierPrice['website_price'];
                        }
                        if ($tier_qty_one > 0):
                            if (Mage::app()->getStore()->getWebsite()->getId() != 1):
                                $price = $tier_qty_one;
                            else:
                                $price = $tier_qty_one;
                            endif;

                        else:
                            if (Mage::app()->getStore()->getWebsite()->getId() != 1):
                                $price = $_product->getFinalPrice;
                            else:
                                $price = $_product->getFinalPrice();
                            endif;
                        //echo $_product->getFinalPrice(); 
                        endif;
                    else:
                        if ($toDate != '' && $today > strtotime($toDate)):
                            if (Mage::app()->getStore()->getWebsite()->getId() != 1):
                                $price = $_product->getFinalPrice;
                            else:
                                $price = $_product->getFinalPrice();
                            endif;


                        else:
                            $tier_qty_one = 0;
                            foreach ($_product->getTierPrice() as $tierPrice) {

                                if ($tierPrice['price_qty'] == 1)
                                    $tier_qty_one = $tierPrice['website_price'];
                            }

                            $new_price = $_product->getFinalPrice();
                            $old_price = $_product->getPrice();

                            if ($new_price > $tier_qty_one && $tier_qty_one > 0) {
                                $old_price = $new_price;
                                $new_price = $tier_qty_one;
                            } elseif ($tier_qty_one > 0) {
                                $old_price = $tier_qty_one;
                            }

                            if (Mage::app()->getStore()->getWebsite()->getId() != 1):
                                $price = $new_price;
                            else:
                                $price = $new_price;
                            endif;
                            if (Mage::app()->getStore()->getWebsite()->getId() != 1):
                                $price = $old_price;
                            else:
                                $price = $old_price;
                            endif;
                        endif;
                    endif;
                }
                $html .= $sym . number_format($price, 2);
            }

            if ($show_add2cart) {
                $html .= $this->_getAdd2CartHtml($_product);
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</a>';
            $html .= '</li>';
            $i++;
            if ($i == $products_limit)
                $flag = 1;
            else
                $flag = 0;
        }
        if ($footercontent != '')
            $html .= '<div class="gghyd_head">' . $footercontent . '</div>';
        if ($flag == 1)
            $html .= '<div class="gghyd_link"><a href=' . $this->getUrl('catalogsearch/result') . '?q=' . $query->getQueryText() . '>Click to see more results</a></div>';

        $html .= '</ul>';

        return $html;
    }

    public function getSuggestData() {
        if (!$this->_suggestData) {
            $collection = $this->helper('catalogSearch')->getSuggestCollection();

            $query = $this->helper('catalogSearch')->getQueryText();
            $counter = 0;
            $data = array();
            foreach ($collection as $item) {
                $_data = array(
                    'title' => $item->getQueryText(),
                    'row_class' => ( ++$counter) % 2 ? 'odd' : 'even',
                    'num_of_results' => $item->getNumResults()
                );

                if ($item->getQueryText() == $query) {
                    array_unshift($data, $_data);
                } else {
                    $data[] = $_data;
                }
            }
            $this->_suggestData = $data;
        }
        return $this->_suggestData;
    }

    public function turn_array($m) {
        for ($z = 0; $z < count($m); $z++) {
            for ($x = 0; $x < count($m[$z]); $x++) {
                $rt[$x][$z] = $m[$z][$x];
            }
        }

        return $rt;
    }

    /**
     * Getting average of ratings/reviews.
     *
     * @param int $productId
     *
     * @return Mage_Review_Model_Review_Summary
     */
    public function getReviewSummary($productId) {
        $reviewSummary = Mage::getModel('review/review_summary')
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->load($productId);

        return $reviewSummary;
    }

    protected function _getAdd2CartHtml($_product) {

        $html = '';



        if ($_product->isSaleable() || $_product->getTypeId() == 'downloadable') {
            $url = $_product->getProductUrl() . '?return_url=' . Mage::getUrl('checkout/cart');

            $html = '<div class="add2cart">'
                    . '<button type="button" title="' . __('Add to Cart')
                    . '" class="button btn-cart" onclick="setLocation(\'' . $url . '\'); return false;"><span><span>'
                    . __('Add to Cart')
                    . '</span></span></button></div>';
        } else {
            $html = '<div class="add2cart availability out-of-stock"><span>'
                    . __('Out of stock')
                    . '</span></div>';
        }


        return $html;
    }

}
