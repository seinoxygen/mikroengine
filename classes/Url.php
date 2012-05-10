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
 * Url Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Url
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Url {
        
    private $uri;
    
    public function __construct() {
        $ME = &get_instance();
        $this->uri = $ME->uri;
    }


    /**
     * Return the base url.
     * 
     * @return string 
     */
    public function base($segments = ''){
        if (isset($_SERVER['HTTP_HOST'])) {
            $base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $base_url .= '://' . $_SERVER['HTTP_HOST'];
            $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        }
        
        if(!empty($segments)){
            $base_url = trim($this->base(), '/');
            $base_url = $base_url.'/'.ltrim($segments, '/');
        }
        
        return $base_url;
    }
    
    /**
     * Return current url.
     * 
     * @return type 
     */
    public function current(){
        return $this->site($this->uri->path());
    }
        
}
// END Url Class

/* End of file Url.php */
/* Location: Url.php */