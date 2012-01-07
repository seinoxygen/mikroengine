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
 * Cookie Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Cookie
 * @author		Mikrobytes Dev Team
 */
// ------------------------------------------------------------------------

class Cookie {
    
    public function __construct(){

    }
    
    public function get($name){
        return $_COOKIE[$name];
    }
    
    public function set($name, $value, $expire = null, $path = null, $domain = null, $secure = null, $http = null){
        
        $expire = $expire > 0 ? $expire + time() : 0;
        
        return setcookie($name, $value, $expire, $path, $domain, $secure, $http);
    }
    
    public function delete($name){
        unset($_COOKIE[$name]);
        
    }
    
}

// END Cookie Class

/* End of file Cookie.php */
/* Location: Cookie.php */