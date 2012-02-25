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
 * Agent Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Agent
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Agent {
    
    public $userAgent;
    
    public $platforms;
    public $browsers;
    public $mobiles;
    
    public $platform;
    public $browser;
    public $version;
    
    public $is_mobile = false;
    
    public function __construct() {
        $ME = &get_instance();
        $config = $ME->config->load('agent', true);
        $this->initialize($config);
        
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->analize($this->userAgent);
    }
    
    /**
     * Initialize custom configuration.
     * 
     * @param array $config 
     */
    public function initialize($config = array()){
        if(!empty($config)){
            foreach ($config as $key => $val) {
                if(!empty($val)){
                    $this->$key = $val;
                }
            }
        } 
    }
    
    /**
     * Analyse the user agent string if its provided.
     * 
     * @param string $user_agent 
     */
    public function analize($user_agent){
        $user_agent = strtolower($user_agent);
        // Reset values on each call.
        $this->platform = 'Unknown Platform';
        $this->browser = 'Unknown Browser';
        $this->version = null;
        $this->is_mobile = false;
        
        // Check for platforms.
        foreach ($this->platforms as $key => $val) {
            if (preg_match("/" . preg_quote($key) . "/i", $user_agent)){
                $this->platform = $val;
                break;
            }
        }
        
        // Check for browsers first.
        foreach ($this->browsers as $key => $val) {
            if (preg_match("/" . preg_quote($key) . ".*?([0-9\.]+)/i", $user_agent, $match)){
                $this->browser = $val;
                $this->version = $match[1];
                break;
            }
        }
        
        // Then check for mobiles.
        foreach ($this->mobiles as $key => $val) {
            if (preg_match("/" . preg_quote($key) . ".*?([0-9\.]+)/i", $user_agent, $match)){
                $this->is_mobile = true;
                $this->browser = $val;
                $this->version = $match[1];
                break;
            }
        }
    }
    
    /**
     * Check if the user is using the provided platform.
     * 
     * @param string $platform
     * @return boolean 
     */
    public function is_platform($platform){
        if(!empty($platform)){
            $platform = strtolower($platform);
            if(in_array($platform, $this->platforms)){
                $platform = $this->platforms[$platform];
            }
            return (bool) preg_match("/" . $platform . "/i", $this->userAgent);
        }
        return false;
    }
    
    /**
     * Return the user platform.
     * 
     * @return string 
     */
    public function platform(){
        return $this->platform;
    }
    
    /**
     * Check if the user is using the provided browser.
     * 
     * @param string $platform
     * @return boolean 
     */
    public function is_browser($browser = null){
        if(is_null($browser)){
            return !$this->is_mobile;
        }
        
        if(!empty($browser)){
            $browser = strtolower($browser);
            if(in_array($browser, $this->browsers)){
                $browser = $this->browsers[$browser];
            }
            return (bool) preg_match("/" . $browser . "/i", $this->userAgent);
        }
        return false;
    }
    
    /**
     * Return the user browser.
     * 
     * @return string 
     */
    public function browser(){
        return $this->browser;
    }
    
    /**
     * Check if the user is using the provided browser.
     * 
     * @param string $platform
     * @return boolean 
     */
    public function is_mobile($browser = null){
        if(is_null($browser)){
            return $this->is_mobile;
        }
        
        if(!empty($browser)){
            $browser = strtolower($browser);
            if(in_array($browser, $this->mobiles)){
                $browser = $this->mobiles[$browser];
            }
            return (bool) preg_match("/" . $browser . "/i", $this->userAgent);
        }
        return false;
    }
    
    /**
     * Return the user browser.
     * 
     * @return string 
     */
    public function mobile(){
        if($this->is_mobile){
           return $this->browser; 
        }
        return 'Unknown Mobile Browser';
    }
    
    /**
     * Return browser version.
     * 
     * @return string 
     */
    public function version(){
        return $this->version;
    }
    
    /**
     * Return if the user was refferd.
     * 
     * @return boolean 
     */
    public function is_referal(){
        if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
            return true;
        }
        return false;
    }
    
    /**
     * Return the referal url.
     * 
     * @return string 
     */
    public function referal(){
        if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
            return $_SERVER['HTTP_REFERER'];
        }
        return;
    }
    
}
// END Agent Class

/* End of file Agent.php */
/* Location: Agent.php */