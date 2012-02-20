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
 * Crypt Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Crypt
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Crypt {
    
    private $config;
    
    private $iv = '';
    private $key = '';

    private $cipher = MCRYPT_RIJNDAEL_256;
    private $mode = MCRYPT_MODE_CBC;
    
    public function __construct(){
        $ME = &get_instance();

        $this->config = $ME->config;
        $this->config->load('crypt');
    }
    
    /**
     * Calculate md5 or sha1 of a string.
     *
     * @param string $str
     * @param string $encoder
     * @return string
     */
    public function hash($str, $encoder = 'md5'){
        return ($encoder == 'md5') ? md5($str) : sha1($str);
    }

    /**
     * Set mcrypt cipher.
     *
     * @param integer $cipher
     */
    public function cipher($cipher){
        $this->cipher = $cipher;
    }

    /**
     * Set mcrypt mode.
     *
     * @param integer $mode
     */
    public function mode($mode){
        $this->mode = $mode;
    }

    /**
     * Encodes the provided data.
     *
     * @param string $str
     * @return string
     */
    public function encode($str){
        $str = mcrypt_encrypt($this->cipher, $this->key, $str, $this->mode, $this->iv);
        return base64_encode($str);
    }

    /**
     * Decodes.
     *
     * @param string $str
     * @return string
     */
    public function decode($str){
        $str = base64_decode($str);
        return mcrypt_decrypt($this->cipher, $this->key, $str, $this->mode, $this->iv);
    }
    
}

// END Crypt Class

/* End of file Crypt.php */
/* Location: Crypt.php */