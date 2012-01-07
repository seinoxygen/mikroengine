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
 * Image Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Image
 * @author		Mikrobytes Dev Team
 */
// ------------------------------------------------------------------------

class Image {
    
    private $image;
    
    /**
     * Create a new image.
     * 
     * @param integer $width
     * @param integer $height 
     */
    public function create($width, $height){
        $this->image = imagecreate($width, $height);
    }
    
    /**
     * Save current image.
     * 
     * @param string $filename
     * @param string $format
     * @param integer $quality 
     */
    public function save($filename, $format = 'png', $quality = 75){
        switch ($format) {
            case 'gif':
                imagegif($this->image, $filename);
            break;
            case 'jpg':
                imagejpeg($this->image, $filename, $quality);
            break;
            case 'png':
                imagepng($this->image, $filename, $quality);
            break;
        }        
    }
    
    /**
     * Rotate the image.
     * 
     * @param integer $angle
     * @param integer $bgcolor 
     */
    public function rotate($angle, $bgcolor = 0){
        $this->image = imagerotate($this->image, $angle, $bgcolor);
    }

    /**
     * Convert hexadecimal to rgb array.
     * 
     * @param string $hex
     * @return array 
     */
    public function hex2rgb($hex){
        $rgb = array();
        if (strlen($hex) == 6) {
            $val = hexdec($hex);
            $rgb['r'] = 0xFF & ($val >> 0x10);
            $rgb['g'] = 0xFF & ($val >> 0x8);
            $rgb['b'] = 0xFF & $val;
        }
        return $rgb;
    }

}
// END Image Class

/* End of file Image.php */
/* Location: Image.php */