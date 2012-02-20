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
    
    public function download($file, $name = null){
        $type = filetype($file);

        // Send file headers
        header("Content-type: $type");
        header("Content-Disposition: attachment;filename=$name");
        header("Content-Transfer-Encoding: binary");
        header('Pragma: no-cache');
        header('Expires: 0');
        // Send the file contents.
        set_time_limit(0);
        readfile($file);
    }
    
}
// END File Class

/* End of file File.php */
/* Location: File.php */