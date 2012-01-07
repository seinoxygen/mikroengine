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
 * Uri Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Uri
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Uri{

    private $config;
    private $base_url;
    private $url;
    private $segments;

    public function __construct(){
        $this->config = &load_class('Config', 'core');
        $this->config->load('uri');
        $this->initialize();
    }

    /**
     * Sets the current url and segments.
     * 
     * @return string
     */
    public function initialize(){       
        $protocol = 'http';
        $host = 'localhost';
        $path = '';
        $url = '';
        
        if (isset($_SERVER['HTTP_HOST'])) {
            $protocol = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $path = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            $url = $protocol.'://'.$host.$path;
        }
        
        $url = trim($url, '/');

        $request = $_SERVER["REQUEST_URI"];
        $request = trim($request, '/');    
        $request = explode('?', $request);      
        
        // Remove the folder segments from the uri if the application is warking in a subfolder.
        $parts = explode('/', $url);
        $this->segments = explode('/', $request[0]);
        foreach($this->segments as $key => $segment){
            if(in_array($segment, $parts)){
                unset($this->segments[$key]);
            }
            else{
                $this->segments[$key] = $this->sanitize($segment);
            }
        }
        // Re-index the segments
        $this->segments = array_values($this->segments);
        
        $this->url = $protocol.'://'.$host.'/'.$_SERVER["REQUEST_URI"];
        
        return $this->url;
    }

    /**
     * Return the current path.
     * 
     * @return string
     */
    public function path(){
        if(!empty($this->segments)){
            return implode("/", $this->segments);
        }
        return '';
    }

    /**
     * Return the specified segment.
     * 
     * @param integer $index
     * @return string
     */
    public function segment($index){
        $index = $index - 1;
        if(isset($this->segments[$index])){
            return $this->segments[$index];
        }
        return false;
    }
    
    /*
     * Return the segment array.
     */
    public function segments(){
        return $this->segments;
    }
    
    /**
     * Sanitize the uri segments with the allowed characters.
     * 
     * @param string $segment
     * @return string 
     */
    private function sanitize($segment){
        $chars = $this->config->get('uri_chars');
        $segment = preg_replace("/[^".$chars."]/", "", $segment);
        return $segment;
    }
}
// END Uri Class

/* End of file Uri.php */
/* Location: ./classes/Uri.php */