<?php

class Mgroup_SubscribeLink_Block_Adminhtml_Newsletter_Queue_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {

  public function render(Varien_Object $row)
  {
    $actions = array();

    if($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
      if(!$row->getQueueStartAt() && $row->getSubscribersTotal()) {
        $actions[] = array(
          'url' => $this->getUrl('*/*/start', array('id'=>$row->getId())),
          'caption'	=> Mage::helper('newsletter')->__('Start')
        );
      }
    } else if ($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_SENDING) {
      $actions[] = array(
        'url' => $this->getUrl('*/*/pause', array('id'=>$row->getId())),
        'caption'	=>	Mage::helper('newsletter')->__('Pause')
      );

//            $actions[] = array(
//                'url'		=>	$this->getUrl('*/*/cancel', array('id'=>$row->getId())),
//                'confirm'	=>	Mage::helper('newsletter')->__('Do you really want to cancel the queue?'),
//                'caption'	=>	Mage::helper('newsletter')->__('Cancel')
//            );


    } else if ($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_PAUSE) {

      $actions[] = array(
        'url' => $this->getUrl('*/*/resume', array('id'=>$row->getId())),
        'caption'	=>	Mage::helper('newsletter')->__('Resume')
      );

    }

    $actions[] = array(
      'url'       =>  $this->getUrl('*/newsletter_queue/preview',array('id'=>$row->getId())),
      'caption'   =>  Mage::helper('newsletter')->__('Preview'),
      'popup'     =>  true
    );

    $actions[] = array(
      'url'		=>	$this->getUrl('*/*/cancel', array('id'=>$row->getId())),
      'confirm'	=>	Mage::helper('newsletter')->__('Do you really want to cancel the queue?'),
      'caption'	=>	Mage::helper('newsletter')->__('Cancel')
    );

    $this->getColumn()->setActions($actions);
    return parent::render($row);
  }
}