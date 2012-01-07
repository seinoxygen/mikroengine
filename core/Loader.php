<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package		Mikroengine
 * @author		Mikrobytes Dev Team
 * @copyright           Copyright (c) 2011 - 2011, Mikrobytes.
 * @license
 * @link		http://www.mikroengine.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Loader Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Loader
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Loader {

    public function __construct() {
    }

    public function library($file, $name = '', $args = '') {
        if(!include_once("classes/".ucfirst($file).".php")) throw new Exception('Library file not found.');
        $name = (empty($name)) ? strtolower($file) : $name;
        $ME = &get_instance();
        $ME->{$name} = new $file($args);        
    }
    
    /**
     * Load a a model.
     * 
     * @param string $file
     * @param string $name 
     */
    public function model($file, $name = '') {
        if(!include_once("models/".ucfirst($file).".php")) throw new Exception('Model file not found.');
        $name = (empty($name)) ? strtolower($file) : $name;
        $ME = &get_instance();
        $ME->{$name} = new $file();
    }
        
    public function view($file, $vars = array(), $return = false) {
        $ME = &get_instance();
        
        if($return === true){
            return $ME->output->load($file, $vars, true);
        }
        
        $ME->output->load($file, $vars);
    }
  
}