<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

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
 * Template Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Zip
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Zip {
    
    private $zip;
    
    /**
     * Initialize zip class.
     */
    public function __construct(){
        $this->zip = new ZipArchive();
    }
    
    /**
     * Destruct zip class.
     */
    public function __destruct() {
        $this->close();
    }
    
    /**
     * Open a zip file.
     * 
     * @param string $path
     * @return boolean 
     */
    public function open($path){
        return ($this->zip->open($path) === true) ? true : false;
    }
    
    /**
     * Create a new zip file.
     * 
     * @param string $path
     * @param string $comment
     * @return boolean 
     */
    public function create($path, $comment = null){
        $create = ($this->zip->open($path, ZIPARCHIVE::CREATE) === true) ? true : false;
        if($comment !== null){
            $this->zip->setArchiveComment($comment);
        }
        return $create;
    }
    
    /**
     * Close the zip file.
     */
    public function close(){
        $this->zip->close();
    }
    
    /**
     * Add an empty dir.
     * 
     * @param string $name 
     */
    public function add_directory($name){
        $this->zip->addEmptyDir($name);
    }
    
    /**
     * Add a file.
     * 
     * @param string $name
     * @param string $path 
     */
    public function add_file($name, $path){
        if(is_array($name)){
            $files = $name;
            foreach($files as $name => $path){
                $this->zip->addFile($path, $name);
            }
        }
        else{
            $this->zip->addFile($path, $name);
        }
    }
    
    /**
     * Add a stream and saves it into a file inside the zip.
     * 
     * @param string $name
     * @param string $data 
     */
    public function add_stream($name, $data){
        $this->zip->addFromString($name, $data);
    }
    
    /**
     * Return zip contents.
     * 
     * @param boolean $original
     * @return array 
     */
    public function contents($original = false){
        $files = array();
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $file = $this->zip->statIndex($i);
            if($original === true){
                $files[$i] = $file;
            }
            else{
                $files[$i]['index'] = $file['index'];
                $files[$i]['name'] = $file['name'];
                $files[$i]['size'] = $file['size'];
                $files[$i]['time'] = $file['mtime'];
                $files[$i]['crc'] = $file['crc'];
            }
        }
        return $files;
    }
    
    /**
     * Extract file contents.
     * 
     * @param string $path 
     */
    public function extract($path){
        $this->zip->extractTo($path);
    }
    
    /**
     * Remove a file from the zip.
     * 
     * @param integer $index 
     */
    public function delete($index){
        if(is_array($index)){
            $indexes = $index;
            foreach($indexes as $index){
                $this->zip->deleteIndex($index);
            }
        }
        else{
            $this->zip->deleteIndex($index);
        }
    }
    
    /**
     * Rename a file inside the zip.
     * 
     * @param integer $index
     * @param name $name 
     */
    public function rename($index, $name){
        $this->zip->renameIndex($index, $name);
    }
    
}
// END Zip Class

/* End of file Zip.php */
/* Location: ./classes/Zip.php */