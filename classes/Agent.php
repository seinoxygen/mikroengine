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
    
    var $platforms;
    var $browsers;
    var $mobile;
    
    public function __construct() {
        $ME = &get_instance();
        $config = $ME->config->load('agent', true);
        $this->initialize($config);
        
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
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
     * Check if the user is using the provided platform.
     * 
     * @param string $platform
     * @return boolean 
     */
    public function is_platform($platform){
        if(!empty($platform)){
            if(in_array($platform, $this->platforms)){
                $platform = $this->platforms[strtolower($platform)];
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
        foreach ($this->platforms as $key => $val) {
            if (preg_match("/" . preg_quote($key) . "/i", $this->userAgent)){
                return $val;
            }
        }
        return 'Unknown Platform';
    }
    
    /**
     * Check if the user is using the provided browser.
     * 
     * @param string $platform
     * @return boolean 
     */
    public function is_browser($browser){
        if(!empty($browser)){
            if(in_array($browser, $this->browsers)){
                $browser = $this->browsers[strtolower($browser)];
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
        foreach ($this->browsers as $key => $val) {
            if (preg_match("/" . preg_quote($key) . "/i", $this->userAgent)){
                return $val;
            }
        }
        return 'Unknown Browser';
    }
    
    /**
     * Check if the user is using the provided browser.
     * 
     * @param string $platform
     * @return boolean 
     */
    public function is_mobile($browser){
        if(!empty($browser)){
            if(in_array($browser, $this->mobile)){
                $browser = $this->mobile[strtolower($browser)];
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
        foreach ($this->mobile as $key => $val) {
            if (preg_match("/" . preg_quote($key) . "/i", $this->userAgent)){
                return $val;
            }
        }
        return 'Unknown Mobile Browser';
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