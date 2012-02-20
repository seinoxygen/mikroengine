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
 * File Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            File
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class File {
    
    private $mime;
    
    public function __construct() {
        $ME = &get_instance();
        $ME->load->library('Mime');
        $this->mime = $ME->mime;
    }
    
    /**
     * Output the file content to the browser. 
     * Speed must be provided in kb/s.
     * 
     * @param string $file
     * @param string $name
     * @param decimal $speed 
     */
    public function download($file, $name = null, $speed = null){
        $info = pathinfo($file);
        $mime = $this->mime->get_mime($info['extension']);
        
        if(is_null($name)){
            $name = basename($file);
        }

        set_time_limit(0);
        
        // Send file headers
        header("Content-type: $mime");
        header("Content-Disposition: attachment; filename=$name");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($file));
        
        // Send the file contents.
        if(is_null($speed)){
            readfile($file);
        }
        else{
            flush();
            $stream = fopen($file, "r");

            // Send the file in chunks
            while (!feof($stream)) {
                print fread($file, round($speed * 1024));
                flush();
                sleep(1);
            }

            // Close the stream
            fclose($file);
        }       
    }
}
// END File Class

/* End of file File.php */
/* Location: File.php */