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
 * Mime Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Calendar
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Mime {
    
    private $config;
    
    public function __construct(){
        $ME = &get_instance();
        $this->config = $ME->config;
        $this->config->load('mime');
    }
    
    /**
     * Return the mime from the provided extension.
     * 
     * @param string $extension
     * @return string 
     */
    public function get_mime($extension){
        if(!empty($extension)){
            $extension = strtolower($extension);
            $mimes = $this->config->get('mime_types');
            if(array_key_exists($extension, $mimes)){
                return $mimes[$extension];
            }
        }        
        return;
    }
    
    /**
     * Return the extension from the mime
     * 
     * @param string $mime
     * @return string 
     */
    public function get_extension($mime){
        if(!empty($mime)){
            $mime = strtolower($mime);
            $mimes = $this->config->get('mime_types');
            foreach($mimes as $ext => $cmime){
                if($cmime == $mime){
                    return $ext;
                }
            }
            return;
        }
    }
}
// END Mime Class

/* End of file Mime.php */
/* Location: Mime.php */