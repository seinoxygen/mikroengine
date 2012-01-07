<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

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
 * Template Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Ftp
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Ftp {
    
    private $host;
    private $method;
    private $username;
    private $password;
    
    private $stream;
    
    public function __construct($host, $username, $password, $method = false) {
        $this->host = $host;
        $this->method = $method;
        $this->username = $username;
        $this->password = $password;
        
        $this->stream = ftp_connect($host);
        ftp_login($this->stream, $this->username, $this->password);
        
        ftp_pasv($this->stream, $this->method);
    }
    
    public function __destruct() {
        $this->close();
    }
    
    /**
     * Close ftp connection.
     */
    public function close(){
        ftp_close($this->stream);
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
     */
    public function mkdir($path, $perms = 755){
        ftp_mkdir($this->stream, $path);
        $this->chmod($path, $perms);
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
    
}
// END Ftp Class

/* End of file Ftp.php */
/* Location: ./classes/Ftp.php */