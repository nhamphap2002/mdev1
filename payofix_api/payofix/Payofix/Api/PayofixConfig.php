<?php

namespace Payofix\Api;

/**
 * Class PayofixConfig
 * @package Api
 *
 * Config singleton, handles all config variables
 */
class PayofixConfig
{

    private static $instance;
    private static $config;

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the 'new' operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Publicly available config item getter.
     *
     * @param $item
     * @return string or bool
     */
    public static function get($item)
    {
        self::$config = self::getConfig();

        $arr = explode(".", $item);

        $c = self::$config;
        foreach($arr as $k){
            if(!$k){
                throw new \Exception("Payofix config file does not appear to be formatted correctly.");
            }
            if(!isset($c[$k])){
                throw new \Exception("Payofix config file does not appear to be formatted correctly.");
            }
            $c = $c[$k];
        }        

        return $c;
    }

    /**
     * Reads the config file and prepares all variables.
     *
     * @return array
     */
    private static function getConfig()
    {
        static $_config;

        if (isset($_config)) {
            return $_config;
        }
        $path = __DIR__."/config.php";
        if(!file_exists($path)){
            throw new \Exception("Missing Payofix Config file ".$path);
        }

        require $path;

        if (!isset($config) || !is_array($config)) {
            throw new \Exception("Payofix config file does not appear to be formatted correctly.");
        }

        $_config =& $config;

        return $_config;
    }

}