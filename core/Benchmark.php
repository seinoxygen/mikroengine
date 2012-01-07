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
 * Benchmark Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Benchmark
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Benchmark {
    
    public $marks = array();
        
    /**
     * Stores a mark time to be compared later.
     * 
     * @param string $name 
     */
    public function mark($name){
        $this->marks[$name] = microtime(true);
    }
    
    /**
     * Compares two marks and return the elapsed time.
     * 
     * @param string $mark1
     * @param string $mark2
     * @return integer 
     */
    public function elapsed($mark1, $mark2 = null){
        $mark1 = $this->marks[$mark1];
        $mark2 = (is_null($mark2)) ? microtime(true) : $this->marks[$mark2];
        return round($mark2 - $mark1, 4);
    }
    
}
// END Benchmark Class

/* End of file Benchmark.php */
/* Location: ./classes/Benchmark.php */