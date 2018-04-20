<?php

class Mgroup_ShippingRate_Adminhtml_Shippingrate_ScriptController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {

      $pageIndex = $this->getRequest()->getParam('page');

      $collection = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect('*')
                    ->setOrder('entity_id', 'asc')
                    ->setPageSize(100)
                    ->setCurPage($pageIndex);

      $dataArray = array();

       foreach ($collection as $product) {
         $shippingRate = $this->calcShippingPriceFromProduct($product);
         if($shippingRate > 0){
           $product->setData("am_shipping_peritem",$shippingRate);
           $product->save();
         }
         //$dataArray[] = "_".$product->getId()." ".$product->getSku()." has been updated with ShippingRate = $". $shippingRate ."<br>";
         $dataArray[] = "_".$product->getId()." SKU: ".$product->getSku()." has been updated <br>";

      }
      $dataArray = array_reverse($dataArray);

      $response = array();
      if(count($dataArray) < 100){
        $response["next"] = 0;
      }else{
        $response["next"] = 1;
      }
      $response["data"] = implode("",$dataArray);

      echo json_encode($response);
  }

    public function calcShippingPriceFromProduct($product){
      $shipping_rate = explode(PHP_EOL,Mage::getStoreConfig('shipping_rate/general/shipping_rate', Mage::app()->getStore()));

      $weight = $product->getWeight();
      foreach ($shipping_rate as $rate) {
        $rateData = explode(":",$rate);
        $weightRange = explode("-",$rateData[0]);

        if($weightRange[0] <= $weight && empty($weightRange[1])){
          return $rateData[1];
          break;
        }
        else if($weightRange[0] <= $weight && $weight <= $weightRange[1]){
          return $rateData[1];
          break;
        }

      }
    }
}
