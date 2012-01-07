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
 * Output Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Output
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Output {

    public $cache = 0;

    protected $views;
    protected $headers;
    protected $output;

    public function __construct() {

    }
    
    //Allow to use classes on views. For example the form class.
    public function __get($var) {
        $ME = & get_instance();
        return $ME->$var;
    }
    
    /**
     * Appends data to the output.
     *
     * @param string $data
     */
    public function append_output($data) {
        if ($this->output == '') {
            $this->output = $data;
        } else {
            $this->output .= $data;
        }
    }

    /**
     * Adds a header to be processed in the output.
     *
     * @param string $header
     * @param boolean $replace
     */
    public function set_header($header, $replace = true){
        $this->headers[] = array('header' => $header, 'replace' => $replace);
    }

    /**
     * Set cache time in minutes.
     *
     * @param integer $time
     */
    function cache($time){
        $this->cache = $time * 60;
    }

    /**
     * Loads the views files and stores them in a temporal output to be processed later.
     *
     * @param string $file
     * @param array $vars
     * @param boolean $return
     * @return string
     */
    public function load($file, $vars, $return = false){
        if (is_array($vars) && count($vars) > 0) {
            extract($vars, EXTR_OVERWRITE);
        }

        $file = 'views/' . strtolower($file) . '.php';

        if(!file_exists($file)){
            throw new Exception('The view was not found.');
        }

        $output = '';

        $generate = true;

        // Cache is enabled?
        if ($this->cache > 0){
            $cache = $this->get_cache();

            // It has contents?
            if($cache !== false){

                // Then override the output with it.
                $this->output = $cache;
                $generate = false;
            }
        }

        // Cache is not loaded or file didn't exists then we capture the output.
        if($generate === true){
            ob_start();
            include($file);
            $output = ob_get_contents();
            @ob_end_clean();
        }

        if($return === true){
            return $output;
        }
        else{
            $this->append_output($output);
        }
    }

    /**
     * Creates the cache file with the output.
     *
     * @param string $output
     */
    private function set_cache($output){
        $ME = &get_instance();
        $path = $ME->uri->path();
        $file = BASEPATH . 'cache/' . md5($path);
        if (!file_exists($file) || time() - $this->cache > filemtime($file)) {
            $filew = fopen($file, 'w');
            fwrite($filew, $output);
            fclose($filew);
        }
    }

    /**
     * Get the cache file if it exists, return false if not.
     *
     * @return string
     */
    private function get_cache(){
        $ME = &get_instance();
        $path = $ME->uri->path();
        $file = BASEPATH . 'cache/' . md5($path);
        if (file_exists($file) && time() - $this->cache < filemtime($file)) {
            return file_get_contents($file);
        }
        return false;
    }

    /**
     * Print the output to be interpreted by the browser.
     *
     * @param string $output
     */
    public function display($output = null){

        if(!is_null($output)){
            $this->output = $output;
        }

        // There is any server header to add?
	if (!headers_sent() && count($this->headers) > 0) {
            foreach ($this->headers as $header) {
                @header($header['header'], $header['replace']);
            }
        }

        // Add information if it's on templates
        $ME = &get_instance();
        $time = $ME->benchmark->elapsed('app');
        $this->output = str_replace('{total_time}', $time, $this->output);

        $memory = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).' MB';
        $this->output = str_replace('{memory_usage}', $memory, $this->output);
        
        $memory = (!function_exists('memory_get_peak_usage')) ? '0' : round(memory_get_peak_usage()/1024/1024, 2).' MB';
        $this->output = str_replace('{memory_peak_usage}', $memory, $this->output);

        if ($this->cache > 0) {
            $this->set_cache($this->output);
        }

        echo $this->output;
    }
}
// END Output Class

/* End of file Output.php */
/* Location: ./classes/Output.php */