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
 * Upload Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Upload
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Upload {
    
    private $upload_path = './';
    private $allowed_exts = array();
    private $max_filesize = 2048; //Kb
    private $sanitize_name = false;
    private $encode_name = false;
    private $encode_method = 'hash';
    
    private $current_file = '';
    
    private $uploaded_files = array();
    
    public function __construct(){
        
    }
    
    /**
     * Initialize config.
     * 
     * @param array $config 
     */
    public function initialize($config = array()) {
        $this->upload_path = (!empty($config['upload_path'])) ? $config['upload_path'] : './';
        $this->allowed_exts = (!empty($config['allowed_exts'])) ? $config['allowed_exts'] : array();
        $this->max_filesize = (!empty($config['max_filesize'])) ? $config['max_filesize'] : 2048;
        $this->sanitize_name = (!empty($config['sanitize_name'])) ? $config['sanitize_name'] : false;
        $this->encode_name = (!empty($config['encode_name'])) ? $config['encode_name'] : false;
        $this->encode_method = (!empty($config['encode_method'])) ? $config['encode_method'] : 'hash';
    }
    
    /**
     * Process the uploaded files.
     * 
     * @param type $field 
     */
    public function process($field = 'userfile'){
        $files = $this->rebuild($_FILES[$field]);
        
        foreach ($files as $file) {
            $this->current_file = $file;
            $tmp = $file['tmp_name'];
            $name = $file['name'];
            $extension = strtolower(end(explode('.', $name)));
            $filesize = round($file['size']/1024, 2);
            
            // Comes with an upload error?
            if($file['error'] != UPLOAD_ERR_OK) {
                //Error
            }
            
            // Has an allowed extension?
            if(!in_array($extension, $this->allowed_exts)){
                //Error
            }
            
            // Filesize is less than wanted?
            if($filesize > $this->max_filesize){
                //Error
            }
            
            $new_name = $this->rename().".".$extension;            
            $final_file = $this->upload_path.$new_name;
            
            // Finally try to move it.
            if(!@move_uploaded_file($tmp, $final_file)){
                //Error
            }
            
            $processed['ext'] = $extension;
            $processed['size'] = $file['size'];
            $processed['is_image'] = $this->is_image();
            $processed['ori_name'] = $name;
            $processed['new_name'] = $new_name;
            $processed['final_path'] = $final_file;
                        
            // Add file to list of processed files.
            array_push($this->uploaded_files, $processed);
        }
    }
    
    /**
     * Rebuild $_FILE array to make it more friendly.
     * 
     * @param array $file
     * @return array 
     */
    private function rebuild(&$file) {
        $array = array();
        $count = count($file['name']);
        $keys = array_keys($file);

        for ($i=0; $i<$count; $i++) {
            foreach ($keys as $key) {
                $array[$i][$key] = $file[$key][$i];
            }
        }

        return $array;
    }
    
    /**
     * Generate a new filename.
     * 
     * @return type 
     */
    private function rename(){
        if($this->encode_name !== true){
            return $this->sanitize();
        }
        $method = strtolower($this->encode_method);
        switch ($method) {
            case 'timestamp':
                return time();
                break;
            case 'hash':
            default:
                return md5_file($this->current_file['tmp_name']);
                break;
        }
    }
    
    /**
     * Sanitize the file name or return it without extension.
     * 
     * @return string 
     */
    private function sanitize(){
        $filename = $this->current_file['name'];
                        
        $parts = explode('.', $filename);
        $parts = array_pop($parts); // Remove the extension.
        $filename = implode('.', $parts);
        
        if($this->sanitize_name !== true){
            return $filename;
        }
        
        // Remove invalid characters.
        $invalid = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
        $filename = str_replace($invalid, '', $filename);
        $filename = preg_replace('/[\s-]+/', '-', $filename);
        $filename = trim($filename, '.-_');
        
        return $filename;
    }
    
    /**
     * Check if the uoloaded file is an image.
     * 
     * @return string 
     */
    private function is_image(){
        $is_image = false;
        
        // Check from extension.
        $exts = array('bmp', 'gif', 'png', 'jpg');
        $ext = strtolower(end(explode('.', $this->current_file['name'])));
        $is_image = in_array($ext, $exts);
        
        // Check from image size.
        $size = getimagesize($this->current_file['tmp_name']);
        $is_image = ($size[0] > 0 && $size[1] > 0) ? true : false;
        
        return $is_image;
    }
    
}

// END Upload Class

/* End of file Upload.php */
/* Location: Upload.php */