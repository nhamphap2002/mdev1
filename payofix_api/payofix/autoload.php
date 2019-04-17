<?php
//require_once ( dirname(dirname(__DIR__))."/app/Mage.php" );
//Mage::app();

class PayofixAutoloader {
    static public function load($className) {
    	$arr = explode("\\", $className);    	
    	if($arr[0] != "Payofix"){
    		return FALSE;
    	}

        $filename = __DIR__."/".str_replace('\\', '/', $className) . ".php";
        //$filename = Mage::getBaseDir('base') . "/payofix_api/payofix/".str_replace('\\', '/', $className) . ".php";
        //die($filename);
        if (file_exists($filename)) {        
            include($filename);
            if (class_exists($className)) {
                return TRUE;
            }
        }
        return FALSE;
    }
}

spl_autoload_register('PayofixAutoloader::load');