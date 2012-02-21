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
 * Image Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Image
 * @author		Keovi Dev Team
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
        $this->image = imagecreatetruecolor($width, $height);
    }
    
    /**
     * Creates an image from a file.
     * Returns true if file exists, false if not.
     * 
     * @param string $filename
     * @return boolean 
     */
    public function open($filename){
        if(file_exists($filename)){
            $this->image = imagecreatefromstring(file_get_contents($filename));
            return true;
        }
        return false;
    }
    
    /**
     * Save current image.
     * 
     * @param string $filename
     * @param string $format
     * @param integer $quality 
     */
    public function save($filename = null, $format = 'png', $quality = 75){
        $format = strtolower($format);
        switch ($format) {
            case 'bmp':
                imagewbmp($this->image, $filename);
                break;
            case 'jpg':
                imagejpeg($this->image, $filename, $quality);
            break;
            case 'gif':
                imagegif($this->image, $filename);
            break;
            case 'png':
                if(!is_null($quality)){
                    $quality = floor($quality * (9 / $quality));
                }
                imagepng($this->image, $filename, $quality);
            break;
        }        
    }
    
    /**
     * Prints the image in the browser.
     * 
     * @param string $format 
     */
    public function draw($format = 'png'){
        header('Content-type: image/'.$format);
        $this->save(null, $format, null);
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
     * Flips the image horizontally or vertically.
     * 
     * @param string $mode 
     */
    public function flip($mode = 'horizontal'){
        $image_width = imagesx($this->image);
        $image_height = imagesy($this->image);

        $temp_image = imagecreatetruecolor($image_width, $image_height);
        if($mode == 'horizontal'){
            imagecopyresampled($temp_image, $this->image, 0, 0, ($image_width-1), 0, $image_width, $image_height, 0-$image_width, $image_height);
        }
        if($mode == 'vertical'){
            imagecopyresampled($temp_image, $this->image, 0, 0, 0, ($image_height-1), $image_width, $image_height, $image_width, 0-$image_height);
        }
        $this->image = $temp_image;
    }
    
    /**
     * Convert to negative the current picture.
     */
    public function negative(){
        imagefilter($this->image, IMG_FILTER_NEGATE);
    }
    
    /**
     * Convert to grayscale the current picture.
     */
    public function grayscale(){
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
    }
    
    /**
     * Convert to sepia the current picture.
     */
    public function sepia(){
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        imagefilter($this->image, IMG_FILTER_COLORIZE, 100, 50, 0);
    }
        
    /**
     * Create thumbnail of the current image
     * 
     * @param integer $size 
     */
    public function thumb($size){
        $image_width = imagesx($this->image);
        $image_height = imagesy($this->image);

        // Calculate aspect ratio
        $width_ratio = $size / $image_width;
        $height_ratio = $size / $image_height;

        // Image is vertical
        if(($width_ratio * $image_height) < $size ){
            $thumb_width = $size;
            $thumb_height = ceil($width_ratio * $image_height);
        }
        
        // Image is horizontal
        if(($height_ratio * $image_width) < $size){
            $thumb_height = $size;
            $thumb_width = ceil($height_ratio * $image_width);
        }
        
        // Image is squared
        if(($width_ratio * $image_height) == ($height_ratio * $image_width)){
            $thumb_width = $size;
            $thumb_height = $size;
        }
                
        // Create temporal image
        $temp_image = imagecreatetruecolor($thumb_width, $thumb_height);

        // Copy resized image to temporal
        imagecopyresampled($temp_image, $this->image, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);
        $this->image = $temp_image;
    }
    
    /**
     * Clear the current image
     */
    public function clear(){
        $this->image = null;
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