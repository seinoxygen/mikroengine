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
 * String Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            String
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class String {
    
    private $utf8;
    
    public function __construct() {
        $ME = &get_instance();
        $ME->load->library('Utf8');
        $this->utf8 = $ME->utf8;
    }
    
    /**
     * Add ellipsis to a string if its too long.
     * 
     * @param string $String
     * @param integer $length
     * @return string 
     */
    public function ellipsize($String, $length){
        if (strlen($String) > $length) {
            $pos = strrpos($String, ' ');
            if ($pos !== false) {
                $String = substr($String, 0, $pos) . '&hellip;';                
            }
        }
        return $String;
    }
    
    /**
     * Camelize strings.
     * 
     * @param string $string
     * @param string $delimiter
     * @return string 
     */
    public function camelize($string, $separator = '-'){
        $arr = explode($separator, $string);
        
        $string = '';
        for ($i = 1; $i < count($arr); $i++) {
            $string .= ucfirst($string);
        }
        
        return lcfirst($string);
    }
    
    /**
     * Generate slug titles.
     * 
     * @param string $string
     * @param string $separator
     * @return string 
     */
    public function slug($string, $separator = '-'){
        $string = $this->utf8->transliterate($string);

        $string = strtolower($string);

        $string = preg_replace("/[^a-z0-9_\s-]/", " ", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", "-", $string);
        
        $string = trim($string, '-');
        
        return $string;
    }
}
// END String Class

/* End of file String.php */
/* Location: String.php */