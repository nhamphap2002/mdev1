<?php
/**
 * Cartin24
 * @package    Cartin24_MultiWishlist
 * @copyright  Copyright (c) 2015-2016 Cartin24. (http://www.cartin24.com)
 * @license    http://opensource.org/licenses/osl-3.0.php   Open Software License (OSL 3.0)
 */
require_once Mage::getModuleDir('controllers', 'Mage_Wishlist') . DS . 'SharedController.php';
class  Cartin24_Multiwishlist_SharedController extends Mage_Wishlist_SharedController
{
    
  /**
     * Add all items from wishlist to shopping cart
     *
     */
    public function allcartAction()
    {
        if ($this->_isCheckFormKey && !$this->_validateFormKey()) {
            $this->_forward('noRoute');
            return;
        }

        $wishlist   = $this->_getWishlist();
        if (!$wishlist) {
            $this->_forward('noRoute');
            return;
        }
        $isOwner    = $wishlist->isOwner(Mage::getSingleton('customer/session')->getCustomerId());

        $messages   = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

        $cart       = Mage::getSingleton('checkout/cart');
        $collection = $wishlist->getItemCollection()
                ->setVisibilityFilter();
        if($this->getRequest()->getParam('mid')){
           $id = $this->getMid();
		   if($this->getMid() == 'default')
				$id = 0;
		   $collection ->addFieldToFilter('multi_wishlist_id', $id);
		 }

        $qtysString = $this->getRequest()->getParam('qty');
        if (isset($qtysString)) {
            $qtys = array_filter(json_decode($qtysString), 'strlen');
        }

        foreach ($collection as $item) {
            /** @var Mage_Wishlist_Model_Item */
            try {
                $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                $item->unsProduct();

                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }
                $item->getProduct()->setDisableAddToCart($disableAddToCart);
                // Add to cart
                if ($item->addToCart($cart, $isOwner)) {
                    $addedItems[] = $item->getProduct();
                }

            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $notSalable[] = $item;
                } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $hasOptions[] = $item;
                } else {
                    $messages[] = $this->__('%s for "%s".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                }

                $cartItem = $cart->getQuote()->getItemByProduct($item->getProduct());
                if ($cartItem) {
                    $cart->getQuote()->deleteItem($cartItem);
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $messages[] = Mage::helper('wishlist')->__('Cannot add the item to shopping cart.');
            }
        }

        if ($isOwner) {
            $indexUrl = Mage::helper('wishlist')->getListUrl($wishlist->getId());
        } else {
            $indexUrl = Mage::getUrl('wishlist/shared', array('code' => $wishlist->getSharingCode()));
        }
        if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
            $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
        } else if ($this->_getRefererUrl()) {
            $redirectUrl = $this->_getRefererUrl();
        } else {
            $redirectUrl = $indexUrl;
        }

        if ($notSalable) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Unable to add the following product(s) to shopping cart: %s.', join(', ', $products));
        }

        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('wishlist')->__('Product(s) %s have required options. Each of them can be added to cart separately only.', join(', ', $products));
        }

        if ($messages) {
            $isMessageSole = (count($messages) == 1);
            if ($isMessageSole && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                if ($isOwner) {
                    $item->delete();
                }
                $redirectUrl = $item->getProductUrl();
            } else {
                $wishlistSession = Mage::getSingleton('wishlist/session');
                foreach ($messages as $message) {
                    $wishlistSession->addError($message);
                }
                $redirectUrl = $indexUrl;
            }
        }

        if ($addedItems) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            }
            catch (Exception $e) {
                Mage::getSingleton('wishlist/session')->addError($this->__('Cannot update wishlist'));
                $redirectUrl = $indexUrl;
            }

            $products = array();
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            Mage::getSingleton('checkout/session')->addSuccess(
                Mage::helper('wishlist')->__('%d product(s) have been added to shopping cart: %s.', count($addedItems), join(', ', $products))
            );

            // save cart and collect totals
            $cart->save()->getQuote()->collectTotals();
        }

        Mage::helper('wishlist')->calculate();

        $this->_redirectUrl($redirectUrl);
    }
}