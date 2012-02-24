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
 * Cookie Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Cookie
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Cookie {
        
    /**
     * Get cookie value.
     * 
     * @param string $name
     * @return string 
     */
    public function get($name){
        return $_COOKIE[$name];
    }
    
    /**
     * Set cookie.
     * 
     * @param string $name
     * @param string $value
     * @param integer $expire
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $http
     * @return boolean 
     */
    public function set($name, $value, $expire = null, $path = null, $domain = null, $secure = null, $http = null){
        $expire = $expire > 0 ? $expire + time() : 0;

        return setcookie($name, $value, $expire, $path, $domain, $secure, $http);
    }
    
    /**
     * Destroy cookie.
     * 
     * @param type $name 
     */
    public function delete($name){
        unset($_COOKIE[$name]);  
    }
    
}

// END Cookie Class

/* End of file Cookie.php */
/* Location: Cookie.php */