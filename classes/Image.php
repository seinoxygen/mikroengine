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
     * Colorize the current picture.
     * 
     * @param integer $red
     * @param integer $green
     * @param integer $blue
     * @param integer $alpha 
     */
    public function colorize($red = 0, $green = 0, $blue = 0, $alpha = 0){
        imagefilter($this->image, IMG_FILTER_COLORIZE, $red, $green, $blue, $alpha);
    }
    
    /**
     * Add brightness to the current picture.
     * 
     * @param integer $depth 
     */
    public function brightness($depth){
        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $depth);
    }
    
    /**
     * Add contrast to the current picture.
     * 
     * @param integer $depth 
     */
    public function contrast($depth){
        imagefilter($this->image, IMG_FILTER_CONTRAST, $depth);
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
        $this->colorize(100, 50);
    }
    
    /**
     * Adds blur to the current picture.
     */
    public function blur(){
        imagefilter($this->image, IMG_FILTER_SELECTIVE_BLUR);
    }
    
    /**
     * Adds a gausian blur to the current picture.
     */
    public function gausian(){
        imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
    }
        
    /**
     * Pixelates the current picture.
     * 
     * @param integer $size 
     */
    public function pixelate($size = 1){
        imagefilter($this->image, IMG_FILTER_PIXELATE, $size, true);
    }
    
    /**
     * Add the scatter effect.
     * 
     * @param type $size 
     */
    public function scatter($size = 1){
        $image_width = imagesx($this->image);
        $image_height = imagesy($this->image);

        for ($x = 0; $x < $image_width; ++$x) {
            for ($y = 0; $y < $image_height; ++$y) {
                
                // Generate random positions for the new dots
                $distx = rand($size * -1, $size);
                $disty = rand($size * -1, $size);

                // Avoid get out of the image region.
                if ($x + $distx >= $image_width) continue;
                if ($x + $distx < 0) continue;
                if ($y + $disty >= $image_height) continue;
                if ($y + $disty < 0) continue;

                $oldcol = imagecolorat($this->image, $x, $y);
                $newcol = imagecolorat($this->image, $x + $distx, $y + $disty);
                imagesetpixel($this->image, $x, $y, $newcol);
                imagesetpixel($this->image, $x + $distx, $y + $disty, $oldcol);
            }
        }
    }
    
    /**
     * Create crop of the current image.
     * 
     * @param integer $width
     * @param integer $height
     * @param integer $x
     * @param integer $y 
     */
    public function crop($width, $height, $x = 0, $y = 0){        
        // Create temporal image
        $temp_image = imagecreatetruecolor($width, $height);

        // Copy resized image to temporal
        imagecopyresampled($temp_image, $this->image, 0, 0, $x, $y, $width, $height, $width, $height);
        $this->image = $temp_image;
    }
        
    /**
     * Create thumbnail of the current image
     * 
     * @param integer $size 
     */
    public function thumb($size){
        // Obtain image sizes
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
     * Add a watermark picture
     * 
     * @param string $filename
     * @param string $position
     * @param integer $opacity
     * @param integer $margin 
     */
    public function watermark($filename, $position = 'bottom-right', $opacity = 25, $margin = 0){
        // Obtain image sizes
        $image_width = imagesx($this->image);
        $image_height = imagesy($this->image);
        
        if(file_exists($filename)){
            $overlay = imagecreatefromstring(file_get_contents($filename));;
        }
        
        // Obtain watermark sizes
        $overlay_width = imagesx($overlay);
        $overlay_height = imagesy($overlay);
                
        // Select predefined positions
        switch ($position) {
            case 'top-left':
                $posx = $margin;
                $posy = $margin;
                break;
            case 'top-center':
                $posx = floor($image_width/2) - floor($overlay_width/2);
                $posy = $margin;
                break;
            case 'top-right':
                $posx = $image_width - $overlay_width - $margin;
                $posy = $margin;
                break;
            case 'center-left':
                $posx = $margin;
                $posy = floor($image_height/2) - floor($overlay_height/2);
                break;
            case 'center':
                $posx = floor($image_width/2) - floor($overlay_width/2);
                $posy = floor($image_height/2) - floor($overlay_height/2);
                break;
            case 'center-right':
                $posx = $image_width - $overlay_width - $margin;
                $posy = floor($image_height/2) - floor($overlay_height/2);
                break;
            case 'bottom-left':
                $posx = $margin;
                $posy = $image_height - $overlay_height - $margin;
                break;
            case 'bottom-center':
                $posx = floor($image_width/2) - floor($overlay_width/2);
                $posy = $image_height - $overlay_height - $margin;
                break;
            case 'bottom-right':
                $posx = $image_width - $overlay_width - $margin;
                $posy = $image_height - $overlay_height - $margin;
                break;
        }
        
        // Create a temporal image with alpha channel
        $watermark = imagecreatetruecolor($overlay_width, $overlay_height);

        // Copy the background of the oricinal picture to the temporal image
        imagecopy($watermark, $this->image, 0, 0, $posx, $posy, $overlay_width, $overlay_height);

        // Copy the overlay picture to the temporal image
        imagecopy($watermark, $overlay, 0, 0, 0, 0, $overlay_width, $overlay_height);
        
        // Overrlay the temporal image on the main picture
        imagecopymerge($this->image, $watermark, $posx, $posy, 0, 0, $overlay_width, $overlay_height, $opacity);
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