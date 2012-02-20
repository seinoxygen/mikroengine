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
 * Html Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Html
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Html {
    
    public function heading($string, $size = 1){
        $sizes = array(1,2,3,4,5,6);
        if(!in_array($size, $sizes)){return;}
        return "<h".$size.">".$string."</h".$size.">";
    }
    
    /**
     * Return br tag.
     * 
     * @param integer $times
     * @return string 
     */
    public function br($times = 1){
        return str_repeat('<br/>', $times);
    }
    
}
// END Html Class

/* End of file Html.php */
/* Location: Html.php */