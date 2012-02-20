<?php

/**
 * @package		Mikroengine
 * @author		Keovi Dev Team
 * @copyright           Copyright (c) 2011 - 2011, Mikrobytes.
 * @license
 * @link		
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Event Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Event
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------
namespace Mikroengine\Core;

class Event {
    
    protected static $_events = array();
    
    public function __construct(){
        
    }
    
    public static function register($name, $callback, $arguments = array()){
        if(!isset(static::$_events[$name])){
            static::$_events[$name] = array();
        }
        array_push(static::$_events[$name], array('method' => $callback, 'args' => $arguments));
    }
    
    public static function trigger($name, $data){
        if(static::has_listeners($name)){
            foreach (static::$_events[$name] as $callback) {
                if(is_callable($callback['method'])){
                    $arguments = !empty($callback['args']) ? $callback['args'] : array();
                    call_user_func_array($callback['method'], $arguments);
                }
            }
        }
    }
    
    public static function has_listeners($name){
        return (count(static::$_events[$name])>0) ? true : false;
    }
    
}

// END Event Class

/* End of file Event.php */
/* Location: Event.php */