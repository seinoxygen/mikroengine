<?php

/**
 * @package		Mikroengine
 * @author		Mikrobytes Dev Team
 * @copyright           Copyright (c) 2011 - 2011, Mikrobytes.
 * @license
 * @link
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Config Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Config
 * @author		Mikrobytes Dev Team
 */
// ------------------------------------------------------------------------

class Config {

    private $confs = array();

    public function __construct(){

    }

    /**
     * Load configurations from a file.
     *
     * @param string $file
     * @return type
     */
    public function load($file){
        $file = BASEPATH.'config/'.$file.'.php';
        if(!file_exists($file)) return;
        include_once $file;
        
        if(!isset($config)) return;
        
        if(is_array($config)){
            $this->confs = array_merge($this->confs, $config);
        }
    }

    /**
     * Return a value from configuration options.
     *
     * @param type $key
     * @return type
     */
    public function get($key){
        return (isset($this->confs[$key])) ? $this->confs[$key] : false;
    }

    /**
     * Set a temporal value in configuration array.
     *
     * @param type $key
     * @param type $val
     */
    public function set($key, $val){
        $this->confs[$key] = $val;
    }

    /**
     * Return all items.
     *
     * @return array
     */
    public function items(){
        return $this->confs;
    }

}

// END Config Class

/* End of file Config.php */
/* Location: Config.php */