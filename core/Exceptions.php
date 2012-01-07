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
 * Exception Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Exceptions
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Exceptions {
    
    /*
     * Display not found error.
     */
    public function display_error(){
        header('HTTP/1.0 404 Not Found');
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }
    
    public function display_http_error($code = 404){
        
    }
    
    /**
     * Display php error.
     * 
     * @param integer $errno
     * @param string $errstr
     * @param string $errfile
     * @param integer $errline 
     */
    public function display_php_error($errno, $errstr, $errfile, $errline){
        switch ($errno) {
            case E_ERROR:
            case E_USER_ERROR:
                echo "<b>ERROR</b> [$errno]<br/>File: $errfile [$errline]<br/>Message: $errstr<br />\n";
                break;
            case E_WARNING:
            case E_USER_WARNING:
                echo "<b>WARNING</b> [$errno]<br/>File: $errfile [$errline]<br/>Message: $errstr<br />\n";
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                echo "<b>NOTICE</b> [$errno]<br/>File: $errfile [$errline]<br/>Message: $errstr<br />\n";
                break;
            default:
                echo "<b>FATAL</b> [$errno]<br/>File: $errfile [$errline]<br/>Message: $errstr<br />\n";
                break;
        }
    }
    
}
// END Exception Class

/* End of file Exception.php */
/* Location: Exception.php */