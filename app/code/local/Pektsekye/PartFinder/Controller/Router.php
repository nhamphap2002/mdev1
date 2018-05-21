<?php

class Pektsekye_PartFinder_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initPartfinderControllerRouters($observer)
    {
//        $observer->getEvent()->getFront()
//            ->addRouter('partfinder', new Pektsekye_PartFinder_Controller_Router());

        $front = $observer->getEvent()->getFront();

        $front->addRouter('partfinder', $this);

    }

    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $identifier = trim($request->getPathInfo(), '/');
        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
        $identifier = $condition->getIdentifier();
//echo $identifier; die;
        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue()) {
            return false;
        }
        $searchKey = $identifier;
        $catId = '';
        if(strpos($identifier, '/') !== false){
            $identifier = str_replace('.html', '', $identifier);
            $temp = explode('/', $identifier);
            if(count($temp) == 3){
                $searchKey = $temp[0].'.html';
                $catId = strval(intval($temp[1]));
            }
        }

        $table = Mage::getSingleton('core/resource')->getTableName('partfinder/db');
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "SELECT * FROM ".$table. " WHERE identifier = :identifier";
        $binds = array(
            'identifier'   => $searchKey,
        );

        $rowData = $conn->fetchRow($sql, $binds);

        if (!$rowData) {
            return false;
        }

        if($catId > 0){
//Zend_Debug::dump($rowData); die;
            $request->setModuleName('partfinder')
                ->setControllerName('product')
                ->setActionName('listdetail')
                ->setParam('year', $rowData['pf_year'])
                ->setParam('make', $rowData['pf_make'])
                ->setParam('model', $rowData['pf_model'])
                ->setParam('submodel', $rowData['pf_submodel'])
                ->setParam('cat', $catId);
        }else{
            $request->setModuleName('partfinder')
                ->setControllerName('product')
                ->setActionName('list')
                ->setParam('year', $rowData['pf_year'])
                ->setParam('make', $rowData['pf_make'])
                ->setParam('model', $rowData['pf_model'])
                ->setParam('submodel', $rowData['pf_submodel']);
        }
        $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $identifier
        );

        return true;
    }
}