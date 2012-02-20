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
 * Utf8 Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Utf8
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Utf8 {
        
    public function __construct(){
        if(function_exists('iconv')){
            if (extension_loaded('mbstring')){
		mb_internal_encoding('UTF-8');
            }
        }
    }
    
    public function to_utf8($str, $encoding){
        if (function_exists('iconv')){
            return @iconv($encoding, 'UTF-8', $str);
	}
	elseif (function_exists('mb_convert_encoding')){
            return @mb_convert_encoding($str, 'UTF-8', $encoding);
	}
	else{
            return false;
        }
    }
    
    public function transliterate($str){
        return iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    }
    
}
// END Utf8 Class

/* End of file Utf8.php */
/* Location: Utf8.php */