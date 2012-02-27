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
 * Number Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Number
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Number {
    
    /**
     * Number the bytes in their unity.
     * 
     * @param integer $bytes
     * @param integer $precision
     * @return string 
     */
    public function bytes($bytes, $precision = 2) {
        // Possible units.
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'); 
        
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        
        $pow = min($pow, count($units) - 1); 

        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
    
    /**
     * Return a number formatted to be readable.
     * 
     * @param int $number
     * @return string 
     */
    public function humanize($number){
        // Possible units.
        $units = array('M', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
        
        if ($number >= 1000) {
            for ($i=-1; $number>=1000; ++$i) {
                $number /= 1000;
            }
            return floor($number).$units[$i];
        }
        return $number;
    }
    
}
// END Number Class

/* End of file Number.php */
/* Location: Number.php */