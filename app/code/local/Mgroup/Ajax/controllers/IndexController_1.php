<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Mgroup_Ajax_IndexController extends Mage_Checkout_CartController {

    public function addAction() {
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();

        $product = $this->_initProduct();
        echo $product->getId();
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
            echo 'Thang cong';
        }
        exit();
    }

}
