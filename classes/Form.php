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
 * Form Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Form
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Form {

    private $url;
    private $config;
    private $security;

    public function __construct(){
        $ME = &get_instance();
        $ME->load->library('url');
        $ME->load->library('security');
        $this->security = $ME->security;
        $this->config = $ME->config;
        $this->url = $ME->url;
    }


    /**
     * Opens a form tag.
     *
     * @param string $action
     * @param string $method
     * @param array $properties
     * @return string
     */
    public function open($action, $method = 'post', $properties = array()){
        $html = '';
        $html .= '<form ';
        $html .= 'action="'.$this->url->base($action).'" ';
        $html .= 'method="'.$method.'" ';
        foreach($properties as $key => $value){
            $html .= $key.'="'.$value.'" ';
        }
        $html .= '>';

        if($this->config->get('csrf_enable') === true){
            $name = $this->config->get('csrf_token');
            $html .= $this->hidden($name, $this->security->csrf());
        }

        return $html;
    }

    /**
     * Opens a multipart form tag.
     *
     * @param string $action
     * @param string $method
     * @param array $properties
     * @return string
     */
    public function multipart_open($action, $method = 'post', $properties = array()){
        $properties['enctype'] = 'multipart/form-data';
        return $this->open($action, $method, $properties);
    }

    /**
     * Closes a form tag.
     *
     * @return string
     */
    public function close(){
        return "</form>";
    }

    /**
     * Open a fieldset tag.
     *
     * @param string $legend
     * @return string
     */
    public function fieldset_open($legend = ''){
        $legend = (!empty($legend)) ? '<legend>'.$legend.'</legend>' : '';
        return '<fieldset>'.$legend;
    }

    /**
     * Closes a fieldset tag.
     *
     * @return string
     */
    public function fieldset_close(){
        return "</fieldset>";
    }

    /**
     * Generate a text field.
     *
     * @param string $name
     * @param string $value
     * @param array $properties
     * @return string
     */
    public function input($name, $value = '', $properties = array()){

        $default['type'] = 'text';
        $default['name'] = $name;
        $default['value'] = $value;

        $properties = array_merge($default, $properties);

        $html = '';
        $html .= '<input ';
        foreach($properties as $key => $value){
            $html .= $key.'="'.$value.'" ';
        }
        $html .= '/>';
        return $html;
    }

    /**
     * Generate a password field.
     *
     * @param string $name
     * @param string $value
     * @param array $properties
     * @return string
     */
    public function password($name, $value = '', $properties = array()){
        $properties['type'] = 'password';
        return $this->input($name, $value, $properties);
    }

    /**
     * Generate a textarea field.
     *
     * @param string $name
     * @param string $value
     * @param array $properties
     * @return string
     */
    public function textarea($name, $value, $properties = array()){
        $html = '';
        $html .= '<textarea ';
        $html .= 'name="'.$name.'" ';
        foreach($properties as $key => $value){
            $html .= $key.'="'.$value.'" ';
        }
        $html .= '>'.$value.'</textarea>';
        return $html;
    }

    /**
     * Generate a hidden field.
     *
     * @param string $name
     * @param string $value
     * @param array $properties
     * @return string
     */
    public function hidden($name, $value = '', $properties = array()){
        $properties['type'] = 'hidden';
        return $this->input($name, $value, $properties);
    }

    /**
     * Generate a file field.
     *
     * @param string $name
     * @param array $properties
     * @return string
     */
    public function file($name, $properties = array()){
        $properties['type'] = 'file';
        return $this->input($name, $value, $properties);
    }

    /**
     * Generate a checkbox field.
     *
     * @param string $name
     * @param string $value
     * @param boolean $checked
     * @param array $properties
     * @return string
     */
    public function checkbox($name, $value, $checked = false, $properties = array()){
        $properties['type'] = 'checkbox';
        if($checked === true){
            $properties['checked'] = 'checked';
        }
        return $this->input($name, $value, $properties);
    }

    /**
     * Generate a radio field.
     *
     * @param string $name
     * @param string $value
     * @param boolean $checked
     * @param array $properties
     * @return string
     */
    public function radio($name, $value, $checked = false, $properties = array()){
        $properties['type'] = 'radio';
        if($checked === true){
            $properties['checked'] = 'checked';
        }
        return $this->input($name, $value, $properties);
    }

    /**
     * Generate a select field.
     *
     * @param string $name
     * @param string $default
     * @param array $options
     * @param array $properties
     * @return string
     */
    public function select($name, $default, $options, $properties = array()){
        $html = '';
        $html .= '<select ';
        $html .= 'name="'.$name.'" ';
        foreach($properties as $key => $value){
            $html .= $key.'="'.$value.'" ';
        }
        $html .= '>';
        foreach($options as $value => $name){
            $selected = ($value == $default) ? 'selected="selected" ' : '';
            $html .= '<option value="'.$value.'" '.$selected.'>'.$name.'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * Generate a captcha field.
     *
     * @param string $name
     * @param array $properties
     * @return string
     */
    public function captcha($name, $properties = array()){
        $captcha = $this->security->captcha();
        $html = '<img class="captcha" src="'.$captcha.'" alt="Captcha Image" />';
        $html .= $this->input($name, '', $properties);
        return $html;
    }

    /**
     * Generate a button tag.
     *
     * @param string $name
     * @param array $properties
     * @return string
     */
    public function button($name, $value, $properties = array()){
        $properties['type'] = 'button';
        return $this->input($name, $value, $properties);
    }

    /**
     * Generate a reset button.
     *
     * @param string $name
     * @param array $properties
     * @return string
     */
    public function reset($name, $value, $properties = array()){
        $properties['type'] = 'reset';
        return $this->input($name, $value, $properties);
    }

    /**
     * Generate a submit button.
     *
     * @param string $name
     * @param array $properties
     * @return string
     */
    public function submit($name, $value, $properties = array()){
        $properties['type'] = 'submit';
        return $this->input($name, $value, $properties);
    }

    /**
     * Set the submited value or the default one.
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public function set_value($key, $default = ''){
        if(isset($_POST[$key])){
            return $_POST[$key];
        }
        else{
            return $default;
        }
    }

}
// END Form Class

/* End of file Form.php */
/* Location: Form.php */