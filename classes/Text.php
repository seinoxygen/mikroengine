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
 * Text Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Text
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Text {
    
    /**
     * Add ellipsis to a string if its too long.
     * 
     * @param string $text
     * @param integer $length
     * @return string 
     */
    public function ellipsize($text, $length){
        if (strlen($text) > $length) {
            $pos = strrpos($text, ' ');
            if ($pos !== false) {
                $text = substr($text, 0, $pos) . '&hellip;';                
            }
        }
        return $text;
    }
    
}
// END Text Class

/* End of file Text.php */
/* Location: Text.php */