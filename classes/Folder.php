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
 * Folder Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Folder
 * @author		Mikrobytes Dev Team
 */
// ------------------------------------------------------------------------

class Folder {
      
    private $files;
    private $folders;
    
    private $folder_size = 0;
    
    /**
     * Return folder contents.
     * 
     * @param string $path
     * @return array 
     */
    public function read($path){
        $path = $this->normalize($path);
        $tree = array();
        $dir = scandir($path);
        foreach ($dir as $key => $folder) {
            if(is_dir($path.$folder) && $folder != '.' && $folder != '..'){
                $tree[$folder] = array();
            }
            else{
                if($folder != '.' && $folder != '..'){
                    $size = filesize($path.$folder);
                    $this->folder_size += $size;
                    $tree[$folder] = $size;
                }
            }
        }
        return $tree;
    }
    
    /**
     * Returh the tree view of a folder.
     * 
     * @param string $path
     * @return array 
     */
    public function tree($path){
        $tree = array();
        $path = $this->normalize($path);
        $dir = $this->read($path);
        foreach ($dir as $folder => $file) {
            if(is_dir($path.$folder) && $folder != '.' && $folder != '..'){
                $temp = $path.$folder.DIRECTORY_SEPARATOR;
                $tree[$folder] = $this->tree($temp);
            }
            else{
                if($folder != '.' && $folder != '..'){
                    $size = filesize($path.$folder);
                    $this->folder_size += $size;
                    $tree[$folder] = $size;
                }
            }
        }
        return $tree;
    }
    
    /**
     * Fund files in the provided directory.
     * 
     * @param type $path
     * @param type $search 
     */
    public function find($path, $search = '.*'){
        $path = $this->normalize($path);
        $dir = $this->read($path);
        return preg_grep('/^' . $search . '$/i', array_keys($dir));
    }
    
    /**
     * Return the folder size of the current search.
     * 
     * @param string $path
     * @return integer 
     */
    public function size($path = null){
        if(is_null($path)){
            return $this->folder_size;
        }
    }
   
    /**
     * Create a directory.
     * 
     * @param string $path
     * @param integer $mode
     * @param boolean $recursive 
     * @return boolean
     */   
    public function create($path, $mode = 0755, $recursive = false){
        $path = $this->normalize($path);
        return mkdir($path, $mode, $recursive);
    }
    
    /**
     * Change directory permissions recursively.
     * 
     * @param string $path
     * @param integer $mode
     * @param boolean $recursive
     * @return boolean 
     */
    public function chmod($path, $mode = 0755, $recursive = false){
        $path = $this->normalize($path);
        if(!file_exists($path)){
            return false;
        }

        if(is_dir($path)) {
            $dirs = $this->read($path);
            // Remove "." and ".." from the list
            $dirs = array_slice($dirs, 2);

            foreach($dirs as $dir){
                $this->chmod($path.$dir, $mode, $recursive);
            }
            chmod($path, $mode);
        }
        
        return true;
    }
    
    public function copy($path_from, $path_to){
        
    }
    
    public function move($path_from, $path_to){
        
    }
    
    /**
     * Delete a directory.
     * 
     * @param string $path 
     * @param boolean $recursive 
     */
    public function delete($path, $recursive = false){
        
    }
    
    public function normalize($path){
        $path = str_replace('//', DIRECTORY_SEPARATOR, $path);
        $path = trim($path, '/');
        $path = $path.DIRECTORY_SEPARATOR;
        return $path;
    }
    
}

// END Folder Class

/* End of file Folder.php */
/* Location: Folder.php */