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
 * Url Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Url
 * @author		Mikrobytes Dev Team
 */
// ------------------------------------------------------------------------

class Url {
        
    public function __construct() {
        
    }


    /**
     * Return the base url.
     * 
     * @return string 
     */
    public function base(){
        if (isset($_SERVER['HTTP_HOST'])) {
            $base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $base_url .= '://' . $_SERVER['HTTP_HOST'];
            $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        }
        return $base_url;
    }
    
    /**
     * Return the site url with the provided uri segments.
     * 
     * @param string $segments
     * @return string 
     */
    public function site($segments = ''){
        $base = trim($this->base(), '/');
        return $base.'/'.ltrim($segments, '/');
    }
    
    public function current(){
        $ME = &get_instance();
        return $this->site($ME->uri->path());
    }
    
    /**
     * Generate slug titles.
     * 
     * @param string $string
     * @param string $separator
     * @return string 
     */
    public function slug($string, $separator = '-'){
        $ME = &get_instance();
        $ME->load->library('Utf8');
        $string = $ME->utf8->transliterate($string);

        $string = strtolower($string);

        $string = preg_replace("/[^a-z0-9_\s-]/", " ", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", "-", $string);
        
        $string = trim($string, '-');
        
        return $string;
    }
    
}
// END Url Class

/* End of file Url.php */
/* Location: Url.php */