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

class Event {
   
    private $object;
    private $name = '';
    private $ME;

    public function __construct($object = null) {
        if(is_null($object)){
            return;
        }
        $this->name = get_class($object);
        $this->object = $object;
        $this->ME = &get_instance();
    }
    
    public function __call($method, $args) {
        
        // Call event function before the real call.
        $before = 'onBefore' .  ucfirst($method) . 'Event';
        if (method_exists($this->ME, $before)) {
            call_user_func_array(array($this->ME, $before), $args);
        }
        
        // Call the method in the plugin class.
        if (method_exists($this->object, $method)) {
            call_user_func_array(array($this->object, $method), $args);
        }
        
        // Method was called so trigger the event.
        $on = 'on' .  ucfirst($method) . 'Event';
        if (method_exists($this->ME, $on)) {
            call_user_func_array(array($this->ME, $on), $args);
        }
        
        // Finally call the last event if it exists.
        $after = 'onAfter' . ucfirst($method) . 'Event';
        if (method_exists($this->ME, $after)) {
            call_user_func_array(array($this->ME, $after), $args);
        }
    }
} 

// END Event Class

class Listener extends Event {
    
}

// END Listener Class

/* End of file Event.php */
/* Location: Event.php */