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
 * Input Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Input
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Input {

    var $config;
    var $xss_clean;

    /**
     * Initialize class.
     *
     * @param boolean $xss_clean
     * @param array $xss_tags
     */
    public function __construct() {
        $this->config = &load_class('Config', 'core');
        $this->xss_clean = $this->config->get('xss_clean');
    }

    /**
     * Get values from $_GET
     *
     * @param string $key
     * @param boolean $xss_clean
     * @return string
     */
    public function get($key = null, $xss_clean = false) {
        return $this->fetch($_GET, $key, $xss_clean);
    }

    /**
     * Get values from $_POST
     *
     * @param string $key
     * @param boolean $xss_clean
     * @return string
     */
    public function post($key = null, $xss_clean = false) {
        return $this->fetch($_POST, $key, $xss_clean);
    }

    /**
     * Get values from $_COOKIE
     *
     * @param string $key
     * @param boolean $xss_clean
     * @return string
     */
    public function cookie($key = null, $xss_clean = false) {
        return $this->fetch($_COOKIE, $key, $xss_clean);
    }

    /**
     * Get values from $_SERVER
     *
     * @param type $key
     * @param type $xss_clean
     * @return string
     */
    public function server($key = null, $xss_clean = false) {
        return $this->fetch($_SERVER, $key, $xss_clean);
    }

    /**
     * Return the user ip.
     * 
     * @return string 
     */
    public function ip(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Fetch the data from $_GET, $_POST, $_COOKIE, $_SESSION or $_SERVER.
     *
     * @param array $enviroment
     * @param string $key
     * @param boolean $xss_clean
     * @return string
     */
    private function fetch($enviroment, $key = null, $xss_clean = false) {
        if(!array_key_exists($key, $enviroment) && !is_null($key)){
            return;
        }
        if ($xss_clean === true || $this->xss_clean === true) {
            if ($key === null) {
                return $this->clean_value($enviroment, true);
            }
            return $this->clean_value($enviroment[$key], true);
        }
        if ($key === null) {
            return $this->clean_value($enviroment);
        }
        return $this->clean_value($enviroment[$key]);
    }

    /**
     * Clean data from xss attacks.
     *
     * @param string $str
     * @return string
     */
    private function clean_xss($str){
        $str = rawurldecode($str);

        // Encode php tags.
        $str = str_replace(array('<?', '?'.'>'),  array('&lt;?', '?&gt;'), $str);
        
        // Remove on* attributes.
        $str = preg_replace('#(<[^>]+[\s\r\n\"\'])(on|style|xmlns)[^>]*>#iU',"$1>",$str);
        
        // Remove unwanted tags.
        $arr = explode('|', 'applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base|video|xml|xss');
        for ($i = 1, $count = count($arr); $i < $count; $i++) {
            $str = preg_replace('/<' . $arr[$i] . '\b[^>]*>/i', '', $str);
            $str = preg_replace('/<\/' . $arr[$i] . '[^>]*>/i', '', $str);
        }
        
        $str = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $str);
        
        $search = array('document.cookie', 'document.write', '.parentNode','.innerHTML', 'window.location','<![CDATA[','<!--','-->');
        $replace = array('[removed]', '[removed]', '[removed]','[removed]', '[removed]','&lt;![CDATA[','&lt;!--','--&gt;');
        
        $str = str_replace($search, $replace, $str);
        
        return $str;
    }

    /**
     *
     * @param string $str
     * @param boolean $xss
     * @return string
     */
    private function clean_value($str, $xss = false){

        if(is_array($str)){
            $arr = array();
            foreach($str as $key => $val){
                $arr[$this->clean_key($key)] = $this->clean_value($value, $xss);
            }
            return $arr;
        }

        if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()){
            $str = stripslashes($str);
        }

        // Remove control characters.
        $str = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $str);

        // Clean xss from data
        if($xss === true){
            $str = $this->clean_xss($str);
        }
        
        // Remove control characters.
        $str = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $str);

        return $str;
    }

    /**
     * Remove ilegal characters from array keys that could exploit the script.
     *
     * @param string $key
     * @return string
     */
    private function clean_key($key){
        return preg_replace("/^[a-z0-9_-]+$/i", $key);
    }

    private function check_csrf(){

    }

    /**
     * Check if the request was done through ajax.
     *
     * @return boolean
     */
    public function is_ajax(){
        return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
    }

    /**
     * Check if the request was done through console.
     *
     * @return boolean
     */
    public function is_cli(){
        return (php_sapi_name() == 'cli') or defined('STDIN');
    }

}
// END Input Class

/* End of file Input.php */
/* Location: ./classes/Input.php */