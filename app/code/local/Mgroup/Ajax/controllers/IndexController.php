<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Mgroup_Ajax_IndexController extends Mage_Checkout_CartController {

    public function addAction() {
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Unable to find Product ID');
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();
            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$cart->getQuote()->getHasError()) {
                $msg = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                Mage::getSingleton('core/session')->addNotice($msg);
                $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart/clerk', array('_secure' => true)));
            }
        } catch (Mage_Core_Exception $e) {
            $msg = "";
            if ($this->_getSession()->getUseNotice(true)) {
                $msg = $e->getMessage();
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $msg .= $message . '<br/>';
                }
            }
            Mage::getSingleton('core/session')->addNotice($msg);
            $this->getResponse()->setRedirect($product->getProductUrl());
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addNotice('Cannot add the item to shopping cart..');
            $this->getResponse()->setRedirect($product->getProductUrl());
        }
        return;
    }

}
