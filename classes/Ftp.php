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
 * @category            Ftp
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Ftp {
    
    private $host;
    private $passive = true;
    private $username;
    private $password;
    
    private $stream;
    
    private $local_files;
    private $remote_files;
        
    public function __construct() {
        $ME = &get_instance();
        $config = $ME->config->load('ftp', true);
        $this->initialize($config);
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
                    $this->$key = $val;
                }
            }
        }
        
        $this->stream = ftp_connect($this->host);
        ftp_login($this->stream, $this->username, $this->password);
        
        ftp_pasv($this->stream, $this->passive);
    }
    
    public function __destruct() {
        $this->close();
    }
        
    /**
     * Return curren directory.
     * 
     * @return string 
     */
    public function pwd(){
        return ftp_pwd($this->stream);
    }
    
    /**
     * Return all files and directories in the current directory.
     * 
     * @param string $directory
     * @return array 
     */
    public function files($directory = "."){
        return ftp_nlist($this->stream, $directory);
    }
    
    /**
     * Change directory on server.
     * 
     * @param string $directory 
     */
    public function chdir($directory){
        ftp_chdir($this->stream, $directory);
    }
    
    /**
     * Gets file modified time.
     *  
     * @param string $file 
     */
    public function mtime($file){
        return ftp_mdtm($this->stream, $file);
    }
    
    /**
     * Upload a file.
     * 
     * @param string $local
     * @param string $remote
     * @param string $mode
     * @param integer $perms 
     */
    public function upload($local, $remote, $mode = 'auto', $perms = 755){
        ftp_put($this->stream, $remote, $local, $mode);
        $this->chmod($remote, $perms);
    }
    
    /**
     * Create a directory.
     * 
     * @param string $path
     * @param integer $perms
     * @return boolean
     */
    public function mkdir($directory, $perms = 755){
        $success = ftp_mkdir($this->stream, $directory);
        $this->chmod($path, $perms);
        return $success;
    }
    
    /**
     * Remove directory from server.
     * 
     * @param string $directory
     * @return boolean 
     */
    public function rmdir($directory){
        return ftp_rmdir($this->stream, $directory);
    }
    
    /**
     * Change the file permissions.
     * 
     * @param string $path
     * @param integer $perms 
     */
    public function chmod($path, $perms = 755){
        ftp_chmod($this->stream, $perms, $remote);
    }
    
    /**
     * Download a file.
     * 
     * @param string $local
     * @param string $remote
     * @param string $mode 
     */
    public function download($local, $remote, $mode = 'auto'){
        ftp_get($this->stream, $local, $remote, $mode);
    }
    
    /**
     * Rename a file on the server.
     * 
     * @param string $oldname
     * @param string $newname 
     */
    public function rename($oldname, $newname){
        ftp_rename($this->stream, $oldname, $newname);
    }
    
    /**
     * Move a file from one directory to other.
     * 
     * @param string $oldname
     * @param string $newname 
     */
    public function move($oldname, $newname){
        $this->rename($oldname, $newname);
    }
    
    /**
     * Removes a file or a directoy.
     * 
     * @param string $path 
     */
    public function delete($path){
        ftp_delete($ftp_stream, $path);
    }
    
    /**
     * Check if is a directory.
     * 
     * @param string $directory
     * @return boolean 
     */
    public function is_dir($directory){
        $pwd = $this->pwd();
        if($this->chdir($directory)){
            $this->chdir($pwd);
            return true;
        }
        return false;
    }
    
    /**
     * Get all files in a directory recursively from server.
     * 
     * @param string $dir
     * @return array 
     */
    public function getRemoteFilesRecursive($dir){
        $remoteFiles = $this->files($dir);
        if (is_array($remoteFiles)) {
            foreach ($remoteFiles as $f) {
                if ($this->is_dir($f)) {
                    $this->getRemoteFilesRecursive($f);
                } 
                else {
                    $this->remote_files[] = $f;
                }
            }
        }
        return $this->remote_files; 
    }
    
    /**
     * Get all files in a directory recursively.
     * 
     * @param string $dir
     * @return array 
     */
    public function getLocalFilesRecursive($dir){
        $handle = new DirectoryIterator($dir);
        foreach($handle as $f) {
            if(!$f->isDot() && !$this->_isInBlackList($f->getPathname())) {
                if($f->isDir()) {
                    $this->getLocalFilesRecursive($f->getPathname());
                }else {
                    $this->local_files[] = $f->getPathname();
                }
            }
        }
        return $this->local_files;
    }
    
    /**
     * Syncronize direcoties and files.
     * 
     * @param string $localDir
     * @param string $remoteDir
     * @param boolean $remote 
     */
    public function sync($localDir, $remoteDir, $remote = false){
        foreach($this->local_files as $local){
            $remote = str_replace($localDir, $remoteDir, $local);        
            $remote = str_replace("\\", "/", $remote);
            $remote_time = $this->mtime($remote);
            $local_time = filemtime($local);
            
            // Sync local to remote.
            if($remote === false){
                if ($remote_time < $local_time) {
                    $this->mkdir(dirname($remote));
                    $this->upload($local, $remote);
                }
            }
            else{
                if ($remote_time > $local_time) {
                    $this->mkdir(dirname($remote));
                    $this->download($local, $remote);
                }
            }
        }
    }
    
    /**
     * Close ftp connection.
     */
    public function close(){
        ftp_close($this->stream);
    }
}
// END Ftp Class

/* End of file Ftp.php */
/* Location: ./classes/Ftp.php */