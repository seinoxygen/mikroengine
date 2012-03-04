<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package		Mikroengine
 * @author		Keovi Dev Team
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
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Loader {

    /**
     * Load a class.
     *
     * @param string $file
     * @param string $name
     * @param array $args 
     */
    public function library($file, $name = '', $args = '') {
        if(!include_once("classes/".ucfirst($file).".php")) throw new Exception('Library file not found.');
        $name = (empty($name)) ? strtolower($file) : $name;
        $ME = &get_instance();
        $ME->{$name} = new $file($args);        
    }
    
    /**
     * Load plugin class and asign it to a listener.
     * 
     * @param string $file
     * @param string $name
     * @param array $args
     */
    public function plugin($file, $name = '', $args = ''){
        if(!include_once("plugins/".ucfirst($file).".php")) throw new Exception('Plugin file not found.');
        $name = (empty($name)) ? strtolower($file) : $name;
        $ME = &get_instance();
        $ME->{$name} = new Listener(new $file($args));
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
        
    /**
     * Load a view.
     * 
     * @param string $file
     * @param array $vars
     * @param boolean $return
     * @return string 
     */
    public function view($file, $vars = array(), $return = false) {
        $ME = &get_instance();
        if($return === true){
            return $ME->output->load($file, $vars, true);
        }
        $ME->output->load($file, $vars);
    }
}

// END Loader Class

/* End of file Loader.php */
/* Location: Loader.php */