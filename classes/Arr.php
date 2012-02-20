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
 * Arr Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Arr
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Arr {
    
    /**
     * Determine if the provided array is an associative array.
     * 
     * @param array $array
     * @return boolean 
     */
    public function is_assoc($array){
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Array map for multi-dimensional arrays.
     *
     * @param string $function
     * @param array $array
     * @return array
     */
    public function array_map_recursive($function, $array){
        $arr = array();
        foreach ($array as $key => $val) {
            if(is_array($val)){
                $arr[$key] = $this->array_map_recursive($function, $val);
            }
            else{
                if(is_array($function)){
                    $arr[$key] = call_user_func_array($function, $val);
                }
                else{
                    $arr[$key] = call_user_func($function, $val);
                }
            }
        }
        return $arr;
    }

}
// END Arr Class

/* End of file Arr.php */
/* Location: Arr.php */