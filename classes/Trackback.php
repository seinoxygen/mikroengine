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
 * Trackback Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Trackback
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Trackback {
    
    private $config;
    private $response = '';
    
    public function __construct(){
        $ME = &get_instance();
        $this->config = $ME->config;
        $this->config->load('trackback');
    }
    
    /**
     * Initialize custom configuration.
     * 
     * @param array $config 
     */
    public function initialize($config = array()){
        if(!empty($config)){
            foreach ($config as $key => $val) {
                if(!empty($val)){
                    $this->config->set($key, $val);
                }
            }
        }        
    }
        
    /**
     * Send a trackback. 
     * 
     * @return boolean 
     */
    public function send(){
        
        $data = array();
        foreach($this->config->items() as $key => $val){
            if(substr($key, 0, 4) == 'tbk_' && $key != 'tbk_target'){
                $data[$key] = str_replace('tbk_', '', $key).'='.urlencode($val);
            }
        }
                
        $target = $this->config->get('tbk_target');
        $target = parse_url($target);
        $query = (isset($target['query'])) ? '?'.$target['query'] : '';
             
        // Error connecting to the host.
        if(!$conn = fsockopen($target['host'], 80)){
            return false;
        }
        
        $data = implode('&', $data);
                                                                                
        fputs($conn, "POST /" . trim($target['path'] . $query, '/') . " HTTP/1.0\r\n");
        fputs($conn, "Host: " . $target['host'] . "\r\n");
        fputs($conn, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($conn, "Content-length: " . strlen($data) . "\r\n");
        fputs ($conn, "Connection: close\r\n\r\n" );
	fputs ($conn, $data);

        while (!feof($conn)) {
            $this->response .= @fgets($conn, 4096);
        } 

        fclose($conn); 
        
        if(preg_match('"<error>0</error>"', $this->response)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Recibe a trackback and validate it.
     * 
     * @return boolean 
     */
    public function receive($extended = array()){
        $default = array('url', 'title', 'blog_name', 'excerpt');
        $default = array_merge($default, $extended);
        foreach($default as $key){
            $val = $this->input->post($key);
            if(empty($val)){
                return false;
            }
        }
        return true;
    }
    
    /**
     * Send a trackback response after validate it.
     * 
     * @param boolean $success
     * @param string $message
     * @return string 
     */
    public function response($success = false, $message = "Error"){
        $return = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $return .= "<response>\n"; 
        if ($success === true) {
            $return .= "<error>0</error>\n";
        } else {
            $return .= "<error>1</error>\n";
            $return .= "<message>" . $message . "</message>\n";
        } 
        $return .= "</response>";
        return $return;
    }
      
}

// END Trackback Class

/* End of file Trackback.php */
/* Location: Trackback.php */