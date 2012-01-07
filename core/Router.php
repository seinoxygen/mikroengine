<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

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
 * Router Class
 *
 * @package		Mikrobytes
 * @subpackage          Libraries
 * @category            Router
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Router {
    
    private $uri;
    private $config;
    private $exception;
    
    private $dir = '';
    private $class = '';
    private $method = '';
    private $args = array();
    
    public function __construct($config = array()){
        $this->uri = &load_class('Uri', 'core');
        $this->config = &load_class('Config', 'core');
        $this->exception = &load_class('Exceptions', 'core');
        $this->config->load('router');
        $this->route();
    }
    
    /*
     * Loads the routes and goes one by one matching with the current uri.
     */
    private function route(){
        $segments = $this->uri->segments();
        
        foreach($this->config->get('router_routes') as $real_route => $virtual_route){
                        
            $preg_virtual_route = str_replace('[any]', '.+', $virtual_route);
            $preg_virtual_route = str_replace('[num]', '[0-9]+', $preg_virtual_route);
            $preg_virtual_route = str_replace('[alpha]', '[a-zA-Z]+', $preg_virtual_route);
            $preg_virtual_route = str_replace('[alphanum]', '[a-zA-Z0-9]+', $preg_virtual_route);

            if(preg_match('#^'.$preg_virtual_route.'$#', $this->uri->path())){
                $i = 1;
                // Better way to do this?
                foreach((array)explode('/', $virtual_route) as $segment){
                    $var = $this->uri->segment($i);
                    switch ($segment) {
                        case '[any]':
                            $real_route = str_replace('[any]', $var, $real_route, $i);
                            break;
                        case '[num]':
                            $real_route = str_replace('[num]', $var, $real_route, $i);
                            break;
                        case '[alpha]':
                            $real_route = str_replace('[alpha]', $var, $real_route, $i);
                            break;
                        case '[alphanum]':
                            $real_route = str_replace('[alphanum]', $var, $real_route, $i);
                            break;
                    }
                    $i++;
                }
                $segments = (array)explode('/',$real_route);
            }
        }

        // Build the directory paths in case of a piece of a uri is a path instead a controller.
        foreach($segments as $segment){
            if(is_dir(BASEPATH.'controllers/'.$segment)){
                $this->dir .= $segment.'/';
            }
        }
        $this->dir = trim($this->dir, '/');
       
        $action = $segments;
        if(!empty($this->dir) && count(explode('/', $this->dir)) > 0){
            $action = array_slice($segments, count(explode('/', $this->dir)));
        }

        // Controller is set in the uri? If not use default controller.
        $this->class = (!empty($action[0])) ? $action[0] : $this->config->get('router_default');     

        // Show 404 if class doesn't exists
        $controller = $this->get_directory().$this->get_class().'.php';

        if(!file_exists($controller)){
            $this->exception->display_error();
        }
        
        // Is method defined? Then use index.
        $this->method = (!empty($action[1])) ? $action[1] : 'index';
        
        // Set the arguments if they exists.
        if(isset($action[2])){
            $this->args = array_slice($action, 2);
        }
    }
    
    /* TODO
     * Re-route current request to another controller.
     * 
     * @param string $route
     */
    public function reroute($route = null){
        if(is_null($route)){
            return;
        }
    }
    
    /**
     * Return the directory where is the controller.
     * 
     * @return string
     */
    public function get_directory(){
        $trailing = (!empty($this->dir)) ? '/' : '';
        return BASEPATH.'controllers/'.$this->dir.$trailing;
    }
    
    /**
     * Return the current controller.
     * 
     * @return string 
     */
    public function get_class(){
        return $this->class;
    }
    
    /**
     * Returh the method.
     * 
     * @return string 
     */
    public function get_method(){
        return $this->method;
    }
    
    /**
     * Return the arguments.
     * 
     * @return array 
     */
    public function get_args(){
        return $this->args;
    }
    
}
// END Router Class

/* End of file Router.php */
/* Location: ./classes/Router.php */