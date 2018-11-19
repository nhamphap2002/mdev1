<?php

/**
 * Cartin24
 * @package    Cartin24_MultiWishlist
 * @copyright  Copyright (c) 2015-2016 Cartin24. (http://www.cartin24.com)
 * @license    http://opensource.org/licenses/osl-3.0.php   Open Software License (OSL 3.0)
 */
require_once Mage::getModuleDir('controllers', 'Mage_Wishlist') . DS . 'IndexController.php';

class Cartin24_Multiwishlist_IndexController extends Mage_Wishlist_IndexController {

    public function createAction() {

        if (!$this->_validateFormKey()) {
            return $this->_redirect('wishlist');
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->_redirect('login');
        }
        $post = $this->getRequest()->getPost();
        $name = $post['wlname'];
        $model = Mage::getModel('multiwishlist/wishlistlabels');
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $collection = $model->getCollection()->addFieldToFilter('wishlist_name', $name)
                ->addFieldToFilter('customer_id', $customerData->getId());
        $result = $collection->getFirstItem();

        if ($result->getId() || strtolower($name) == 'main')
            Mage::getSingleton('core/session')->addError('Already exist a Wishlist. Please choose a different name.');
        else {

            $model->setCustomerId($customerData->getId());
            $model->setWishlistName($name);
            $model->save();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('multiwishlist')->__('Successfully saved the wishlist.'));
        }
        return $this->_redirect('wishlist');
    }

    public function moveToWishlistAction() {

        try {
            $itemId = $this->getRequest()->getParam('itemid');
            $wishlist = $this->getRequest()->getParam('wishlist');
            $model = Mage::getModel('wishlist/item')->load($itemId)->setMultiWishlistId($wishlist)->save();

            $this->loadLayout();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        echo 1;
    }

    public function optionsAction() {

        $this->loadLayout();
        $this->renderLayout();
    }

    public function productoptionsAction() {

        $this->loadLayout();
        $this->renderLayout();
    }

    public function deleteAction() {

        $wlId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('multiwishlist/wishlistlabels');
        $items = $model->getWishlistItemsCollection($wlId);
        $model->load($wlId)->delete();
        foreach ($items as $item) {
            Mage::getModel('wishlist/item')->load($item->getId())->delete();
        }
        return $this->_redirect('wishlist');
    }

    public function assignWishlistAction() {

        $requestParams = array();
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        $name = $this->getRequest()->getParam('wlname');
        $model = Mage::getModel('multiwishlist/wishlistlabels');
        $collection = $model->getCollection()->addFieldToFilter('wishlist_name', $name)
                ->addFieldToFilter('customer_id', $customerData->getId());
        $result = $collection->getFirstItem();
        if ($wishlistId == 'new') {
            if ($result->getId() || strtolower($name) == 'main') {
                $var["result"] = "error";
                $var["message"] = '<ul class="messages"><li class="error-msg"><ul><li><span>Already exist a Wishlist. Please choose a different name.</span></li></ul></li></ul>';
                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($var));
            } else {

                $model->setCustomerId($customerData->getId());
                $model->setWishlistName($name);
                $res = $model->save();
                $wishlistId = $res['multi_wishlist_id'];
            }
        }
        $model = $model->load($wishlistId);
        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerData->getId(), true);
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('productId'));
        $buyRequest = new Varien_Object($requestParams);
        $result = $wishlist->addNewItem($product, $buyRequest);
        $wishlist->save();
        $wishlistItemId = $result['wishlist_item_id'];
        $wishlistItem = Mage::getModel('wishlist/item')->load($wishlistItemId)->setMultiWishlistId($wishlistId)->save();
        $var["result"] = "success";
        $var["message"] = "<ul class='messages'><li class='success-msg'><ul><li><span>'" . $product->getName() . "' has been added to your wishlist '" . $model->getWishlistName() . "'.</span></li></ul></li></ul>";

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($var));
    }

    public function assignWlFromViewAction() {

        $params = $this->getRequest()->getParam('buyReq');
        $paramsArray = explode('&', $params);
        $searchword = 'options';
        $searchword1 = 'super_attribute';
        $searchword2 = 'form_key';
        $searchword3 = 'qty';
        $searchword4 = 'links';
        $attr = array();
        $options = array();
        $links = array();
        $formkey = '';
        $qty = '';
        foreach ($paramsArray as $k => $v) {
            if (preg_match("/\b$searchword\b/i", $v)) {
                $array = explode(']=', substr($v, 8));
                if (is_numeric($array[0]))
                    $options[$array[0]] = $array[1];
                else {
                    $arr = explode('][', $array[0]);
                    $options[$arr[0]][] = $array[1];
                }
            } else if (preg_match("/\b$searchword1\b/i", $v)) {
                $array = explode(']=', substr($v, 16));
                $attr[$array[0]] = $array[1];
            } else if (preg_match("/\b$searchword2\b/i", $v)) {
                $formkey = substr($v, 9);
            } else if (preg_match("/\b$searchword3\b/i", $v)) {
                $qty = substr($v, 4);
            } else if (preg_match("/\b$searchword4\b/i", $v)) {

                $links[] = substr($v, 8);
            }
        }
        $requestParams = array();
        $requestParams['product'] = $this->getRequest()->getParam('productId');
        $requestParams['form_key'] = $formkey;
        $requestParams['super_attribute'] = $attr;
        $requestParams['options'] = $options;
        $requestParams['qty'] = $qty;
        $requestParams['links'] = $links;
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        $name = $this->getRequest()->getParam('wlname');
        $model = Mage::getModel('multiwishlist/wishlistlabels');
        $collection = $model->getCollection()->addFieldToFilter('wishlist_name', $name)
                ->addFieldToFilter('customer_id', $customerData->getId());
        $result = $collection->getFirstItem();
        if ($wishlistId == 'new') {
            if ($result->getId() || strtolower($name) == 'main') {
                $var["result"] = "error";
                $var["message"] = '<ul class="messages"><li class="error-msg"><ul><li><span>Already exist a Wishlist. Please choose a different name.</span></li></ul></li></ul>';
                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($var));
            } else {

                $model->setCustomerId($customerData->getId());
                $model->setWishlistName($name);
                $res = $model->save();
                $wishlistId = $res['multi_wishlist_id'];
            }
        }
        $model = $model->load($wishlistId);
        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerData->getId(), true);
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('productId'));
        $buyRequest = new Varien_Object($requestParams);
        $result = $wishlist->addNewItem($product, $buyRequest);
        $wishlist->save();
        $wishlistItemId = $result['wishlist_item_id'];
        $wishlistItem = Mage::getModel('wishlist/item')->load($wishlistItemId)->setMultiWishlistId($wishlistId)->save();
        $var["result"] = "success";
        $var["message"] = "<ul class='messages'><li class='success-msg'><ul><li><span>'" . $product->getName() . "' has been added to your wishlist '" . $model->getWishlistName() . "'.</span></li></ul></li></ul>";

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($var));
    }

    public function moveFromCartAction() {

        $itemId = $this->getRequest()->getParam('item_id');
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        /* @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getSingleton('checkout/cart');

        try {
            $item = $cart->getQuote()->getItemById($itemId);

            if (!$item) {
                $var["result"] = "error";
                $var["message"] = "<ul class='messages'><li class='error-msg'><ul><li><span>Requested cart item doesn't exist</span></li></ul></li></ul>";

                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($var));
            }
            $buyRequest = array();
            $productId = $item->getProductId();
            $buyRequest = $item->getBuyRequest();
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $name = $this->getRequest()->getParam('wlname');
            $model = Mage::getModel('multiwishlist/wishlistlabels');
            $collection = $model->getCollection()->addFieldToFilter('wishlist_name', $name)
                    ->addFieldToFilter('customer_id', $customerData->getId());
            $result = $collection->getFirstItem();

            if ($wishlistId == 'new') {
                if ($result->getId() || strtolower($name) == 'main') {
                    $var["result"] = "error";
                    $var["message"] = '<ul class="messages"><li class="error-msg"><ul><li><span>Already exist a Wishlist. Please choose a different name.</span></li></ul></li></ul>';
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($var));
                } else {

                    $model->setCustomerId($customerData->getId());
                    $model->setWishlistName($name);
                    $res = $model->save();
                    $wishlistId = $res['multi_wishlist_id'];
                }
            }
            $model = $model->load($wishlistId);
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerData->getId(), true);
            $result = $wishlist->addNewItem($productId, $buyRequest);
            $wishlist->save();
            $cart->getQuote()->removeItem($itemId);
            $cart->save();
            $wishlistItemId = $result['wishlist_item_id'];
            $productName = Mage::helper('core')->escapeHtml($item->getProduct()->getName());
            $wishlistItem = Mage::getModel('wishlist/item')->load($wishlistItemId)->setMultiWishlistId($wishlistId)->save();
            $var["result"] = "success";
            $var["message"] = "<ul class='messages'><li class='success-msg'><ul><li><span>'" . $productName . "' has been moved to wishlist '" . $model->getWishlistName() . "'.</span></li></ul></li></ul>";

            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($var));
        } catch (Mage_Core_Exception $e) {

            $var["result"] = "error";
            $var["message"] = "<ul class='messages'><li class='error-msg'><ul><li><span>" . $e->getMessage() . "</span></li></ul></li></ul>";
        } catch (Exception $e) {
            $var["result"] = "error";
            $var["message"] = "<ul class='messages'><li class='error-msg'><ul><li><span>Cannot move item to wishlist</span></li></ul></li></ul>";
        }

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($var));
    }

    public function cartAction() {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*');
        }
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var $item Mage_Wishlist_Model_Item */
        $item = Mage::getModel('wishlist/item')->load($itemId);
        if (!$item->getId()) {
            return $this->_redirect('*/*');
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->_redirect('*/*');
        }

        // Set qty
        $qty = $this->getRequest()->getParam('qty');
        if (is_array($qty)) {
            if (isset($qty[$itemId])) {
                $qty = $qty[$itemId];
            } else {
                $qty = 1;
            }
        }
        $qty = $this->_processLocalizedQty($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        /* @var $session Mage_Wishlist_Model_Session */
        $session = Mage::getSingleton('wishlist/session');
        $cart = Mage::getSingleton('checkout/cart');

        $redirectUrl = Mage::getUrl('*/*');

        try {
            $options = Mage::getModel('wishlist/item_option')->getCollection()
                    ->addItemFilter(array($itemId));
            $item->setOptions($options->getOptionsByItem($itemId));

            $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest(
                    $this->getRequest()->getParams(), array('current_config' => $item->getBuyRequest())
            );

            $item->mergeBuyRequest($buyRequest);
            if ($item->addToCart($cart, false)) {
                $cart->save()->getQuote()->collectTotals();
            }

            $wishlist->save();
            Mage::helper('wishlist')->calculate();

            if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
                $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
            }
            Mage::helper('wishlist')->calculate();

            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($item->getProductId());
            $productName = Mage::helper('core')->escapeHtml($product->getName());
            $message = $this->__('%s was added to your shopping cart.', $productName);
            Mage::getSingleton('catalog/session')->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError($this->__('This product(s) is currently out of stock'));
            } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
                $redirectUrl = Mage::getUrl('*/*/configure/', array('id' => $item->getId()));
            } else {
                Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
                $redirectUrl = Mage::getUrl('*/*/configure/', array('id' => $item->getId()));
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $session->addException($e, $this->__('Cannot add item to shopping cart'));
        }

        Mage::helper('wishlist')->calculate();

        return $this->_redirectUrl($redirectUrl);
    }

    public function addtoCartAction() {
        $wlId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('multiwishlist/wishlistlabels');
        $items = $model->getWishlistItemsCollection($wlId);
        $products = array();
        $error = array();
        $outofStockError = array();
        $optionError = array();
        $cart = Mage::getSingleton('checkout/cart');
        foreach ($items as $wl_item) {
            $itemId = (int) $wl_item->getId();
            /* @var $item Mage_Wishlist_Model_Item */
            $item = Mage::getModel('wishlist/item')->load($itemId);
            if (!$item->getId()) {
                return $this->_redirect('*/*');
            }
            $wishlist = Mage::getModel('wishlist/wishlist')->load($item->getWishlistId());
            if (!$wishlist) {
                return $this->_redirect('*/*');
            }
            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($item->getProductId());
            $productName = Mage::helper('core')->escapeHtml($product->getName());
            try {
                $options = Mage::getModel('wishlist/item_option')->getCollection()
                        ->addItemFilter(array($itemId));
                $item->setOptions($options->getOptionsByItem($itemId));
                $cart->init();
                $cart->getQuote()->setTotalsCollectedFlag(false);
                if ($item->addToCart($cart, true)) {
                    $cart->save()->getQuote()->collectTotals();
                }
                $wishlist->save();
                Mage::helper('wishlist')->calculate();

                $products[] = '"' . $productName . '"';
            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $outofStockError[] = '"' . $productName . '"';
                } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $optionError[] = '"' . $productName . '"';
                } else {
                    $error[] = rtrim($e->getMessage(), '.') . ' for ' . '"' . $productName . '"';
                }
            }
        }
        $session = Mage::getSingleton('wishlist/session');
        if ($products)
            $session->addSuccess($this->__('%s product(s) have been added to shopping cart: %s', count($products), implode(',', $products)));
        if ($outofStockError)
            $session->addError($this->__('Following product(s) are currently out of stock: %s', implode(',', $outofStockError)));
        if ($optionError)
            $session->addError($this->__('Please specify the product required option(s) for the following: %s', implode(',', $optionError)));
        if ($error) {
            for ($i = 0; $i < count($error); $i++)
                $session->addError($error[$i]);
        }
        return $this->_redirect('wishlist');
    }

    /**
     * Share wishlist
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    public function sendAction() {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }

        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $emails = explode(',', $this->getRequest()->getPost('emails'));
        $message = nl2br(htmlspecialchars((string) $this->getRequest()->getPost('message')));
        $error = false;
        if (empty($emails)) {
            $error = $this->__('Email address can\'t be empty.');
        } else {
            foreach ($emails as $index => $email) {
                $email = trim($email);
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    $error = $this->__('Please input a valid email address.');
                    break;
                }
                $emails[$index] = $email;
            }
        }
        if ($error) {
            Mage::getSingleton('wishlist/session')->addError($error);
            Mage::getSingleton('wishlist/session')->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
            return;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        try {
            $customer = Mage::getSingleton('customer/session')->getCustomer();

            /* if share rss added rss feed to email template */
            if ($this->getRequest()->getParam('rss_url')) {
                $rss_url = $this->getLayout()
                        ->createBlock('wishlist/share_email_rss')
                        ->setWishlistId($wishlist->getId())
                        ->toHtml();
                $message .= $rss_url;
            }
            $mid = $this->getRequest()->getPost('multi_wishlist_id');
            $wishlistBlock = $this->getLayout()->createBlock('wishlist/share_email_items')
                            ->setMid($mid)->toHtml();

            $emails = array_unique($emails);
            /* @var $emailModel Mage_Core_Model_Email_Template */
            $emailModel = Mage::getModel('core/email_template');

            $sharingCode = $wishlist->getSharingCode();
            foreach ($emails as $email) {
                $emailModel->sendTransactional(
                        Mage::getStoreConfig('wishlist/email/email_template'), Mage::getStoreConfig('wishlist/email/email_identity'), $email, null, array(
                    'customer' => $customer,
                    'salable' => $wishlist->isSalable() ? 'yes' : '',
                    'items' => $wishlistBlock,
                    'addAllLink' => Mage::getUrl('*/shared/allcart', array('code' => $sharingCode, 'mid' => $mid)),
                    'viewOnSiteLink' => Mage::getUrl('*/shared/index', array('code' => $sharingCode, 'mid' => $mid)),
                    'message' => $message
                        )
                );
            }

            $wishlist->setShared(1);
            $wishlist->save();

            $translate->setTranslateInline(true);

            Mage::dispatchEvent('wishlist_share', array('wishlist' => $wishlist));
            Mage::getSingleton('customer/session')->addSuccess(
                    $this->__('Your Wishlist has been shared.')
            );
            $this->_redirect('*/*', array('wishlist_id' => $wishlist->getId()));
        } catch (Exception $e) {
            $translate->setTranslateInline(true);

            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            Mage::getSingleton('wishlist/session')->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
        }
    }

}
