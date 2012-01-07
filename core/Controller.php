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
 * Controller Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Controller
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Controller{

    private static $instance;
    
    public function __construct(){
	self::$instance = & $this;

        foreach (is_loaded() as $var => $class) {
            $this->$var = &load_class($class);
        }
        
        $this->load =& load_class('Loader', 'core');
    }

    public static function &get_instance(){
        return self::$instance;
    }
    
}
// END Controller Class

/* End of file Controller.php */
/* Location: ./core/Controller.php */