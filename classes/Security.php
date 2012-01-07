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
 * @category            Security
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Security {

    private $input;
    private $config;
    private $session;
    private $image;

    public function __construct(){
        $ME = &get_instance();

        $this->config = $ME->config;
        $this->config->load('security');

        $this->input = $ME->input;

        $ME->load->library('session');
        $this->session = $ME->session;

        $ME->load->library('image');
        $this->image = $ME->image;
    }

    /**
     * Generate a unique tokento be inserted in forms.
     *
     * @return string
     */
    public function csrf(){
        $hash = md5(uniqid(rand(), true));
        $name = $this->config->get('csrf_token');
        $this->session->set($name, array('hash' => $hash, 'time' => time()));

        return $hash;
    }

    /**
     * Check if csrf token is valid.
     * 
     * @return type 
     */
    public function check_csrf(){
        
        $ips = $this->config->get('csrf_ips');
        if(is_array($ips)){
            foreach($ips as $ip){
                $viewer = $this->input->ip();
                if($ip == $viewer){
                    return true;
                }
            }
        }
        
        $name = $this->config->get('csrf_token');
        $life = $this->config->get('csrf_life');
        $token = $this->session->get($name);
        if($token['hash'] == $this->input->post($name)){
            if(time() < $token['time'] + $life){
                return true;
            }
        }
        return false;
    }

    /**
     * Return a captcha pic or question.
     */
    public function captcha() {
        
        // Remove old pictures.
        $life = $this->config->get('captcha_life');
        $captcha_dir = @opendir('data/captcha/');
        while ($filename = @readdir($captcha_dir)) {
            if ($filename != "." && $filename != ".." && $filename != "index.html") {
                $name = explode('.',$filename);
                if (time() > $name[0] + $life) {
                    @unlink('data/captcha/' . $filename);
                }
            }
        }
        @closedir($captcha_dir);
        
        $url = $this->config->get('captcha_url');
        $filename = time() . '.png';
        $url = trim($url, '\\/') . '/' . $filename;        

        $word = '';

        $chars = $this->config->get('captcha_chars');
        $length = $this->config->get('captcha_lenght');
        for ($i = 0; $i < $length; $i++) {
            $word .= $chars{rand(0, strlen($chars) - 1)};
        }

        $height = $this->config->get('captcha_height');
        $width = $this->config->get('captcha_width');

        $captcha = imagecreate($width, $height);

        $color = $this->config->get('captcha_colors');
        
        // Set the background color.
        $bg_color = $this->image->hex2rgb($color['background']);

        $bg = imagecolorallocate($captcha, $bg_color['r'], $bg_color['g'], $bg_color['b']);
        imagefilledrectangle($captcha, 0, 0, $width, $height, $bg);

        // If noise is enabled we generate random dots.
        $noise = $this->config->get('captcha_noise');
        if ($noise === true) {
            $ne_color = $this->image->hex2rgb($color['noise']);

            $noise = imagecolorallocate($captcha, $ne_color['r'], $ne_color['g'], $ne_color['b']);

            for ($i = 0; $i < ($width * $height / 4); $i++) {
                $x = rand(0, $width - 1);
                $y = rand(0, $height - 1);
                imagesetpixel($captcha, $x, $y, $noise);
            }
        }

        $br_color = $this->image->hex2rgb($color['border']);

        $border = imagecolorallocate($captcha, $br_color['r'], $br_color['g'], $br_color['b']);
        imagerectangle($captcha, 0, 0, $width-1, $height-1, $border);

        $tx_color = $this->image->hex2rgb($color['text']);

        $text = imagecolorallocate($captcha, $tx_color['r'], $tx_color['g'], $tx_color['b']);
        imagestring($captcha, 5, 30, round($height/3)-3, $word, $text);

        if($this->config->get('captcha_sensitive') === false){
            $answer = strtolower($answer);
        }
        $this->session->set('captcha', md5($word));

        if (!file_exists('data/captcha')) {
            mkdir('data/captcha/', 0755, true);
        }

        imagepng($captcha, 'data/captcha/' . $filename);
        imagedestroy($captcha);
        return $url;
    }

}
// END Security Class

/* End of file Security.php */
/* Location: ./classes/Security.php */