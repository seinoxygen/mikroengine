<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @package		Mikroengine
 * @author		Keovi Dev Team
 * @copyright           Copyright (c) 2011 - 2011, Mikrobytes.
 * @license
 * @link		http://www.mikroengine.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Validation Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Validation
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Validation {

    var $rules = array();
    var $errors = array();
    var $messages = array();
    var $field;
    var $label;

    public function __construct() {
        $ME = &get_instance();
        $ME->config->load('security');
        if ($ME->config->get('csrf_enable') === true) {
            $name = $ME->config->get('csrf_token');
            $this->add_rule($name, 'csrf', array('csrf'));
        }
    }

    /**
     * Adds a group of rules for a field.
     *
     * @param string $field
     * @param string $label
     * @param array $rules
     */
    public function add_rule($field, $label, $rules) {

        if (empty($field) || empty($label)) {
            return;
        }

        if (!is_array($rules) && is_string($rules)) {
            $rules = array($rules);
        }

        $this->rules[] = array('field' => $field, 'label' => $label, 'rules' => $rules);
    }

    /**
     * Add a message to return if valitation fail in the provided field.
     *
     * @param string $field
     * @param string $message
     */
    public function add_message($field, $message) {
        if (empty($field) || empty($message)) {
            return;
        }

        $this->messages[$field] = $message;
    }

    /**
     * Clear all rules to use in next form.
     */
    public function clear_rules() {
        $this->rules = array();
    }

    /**
     * Test submited data with current rules.
     *
     * @return boolean
     */
    public function check() {
        $submited = false;
        $ME = &get_instance();
        foreach ($this->rules as $rule) {

            $this->field = $rule['field'];
            $this->label = $rule['label'];

            $data = $ME->input->post($this->field);

            foreach ($rule['rules'] as $action) {

                $param = false;
                if (preg_match("/(.*?)\[(.*)\]/", $action, $match)) {
                    $action = $match[1];
                    $param = $match[2];
                }

                //Execute common functions that return values.
                if (function_exists($action)) {
                    $data = call_user_func_array($action, array($data));
                }

                //Check if the data is valid.
                if (method_exists($this, $action)) {
                    $datarray = array($data);
                    if ($param) {
                        $datarray[] = $param;
                    }
                    $result = call_user_func_array(array(&$this, $action), $datarray);
                }

                // Form was submitted
                if (isset($_POST[$this->field])) {
                    $submited = true;
                }

                if ($result === false) {
                    if (empty($this->messages[$this->field])) {
                        $this->messages[$this->field] = "Unable to locate the error message for $this->field.";
                    }
                    $this->errors[$rule['field']] = $this->messages[$rule['field']];
                }
            }
        }
        // If form was not submitted submitted we're dealing with a simple form display. No action neeed.
        if ($submited === false) {
            $this->errors = array();
            return false;
        }

        return empty($this->errors);
    }

    /**
     * Check if the value is empty.
     *
     * @param string $str
     * @return boolean
     */
    public function required($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field is required.", $this->label);
        return (bool) !empty($str);
    }

    /**
     * Check if the value is a number.
     *
     * @param string $str
     * @return boolean
     */
    public function integer($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must be an integer.", $this->label);
        return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
    }

    /**
     * Check if the value is a natural number.
     *
     * @param string $str
     * @return boolean
     */
    public function natural($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must be only positive numbers.", $this->label);
        return (bool) (preg_match('/^[0-9]+$/', $str));
    }

    /**
     * Check if the value is a natural number and is not zero.
     *
     * @param string $str
     * @return boolean
     */
    public function natural_no_zero($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must be a number greater than zero.", $this->label);
        return (bool) (preg_match('/^[0-9]+$/', $str) || $str == 0);
    }

    /**
     * Check if the value is a decimal.
     *
     * @param type $str
     * @return boolean
     */
    public function decimal($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must contain a decimal number.", $this->label);
        return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
    }

    /**
     * Check if only contains alphabetical characters.
     *
     * @param string $str
     * @return boolean
     */
    public function alpha($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field may only contain alphabetical characters.", $this->label);
        return (bool) preg_match("/^([a-z])+$/i", $str);
    }

    /**
     * Check if only contains alpha-numeric characters.
     *
     * @param string $str
     * @return boolean
     */
    public function alpha_numeric($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field may only contain alpha-numeric characters.", $this->label);
        return (bool) preg_match("/^([a-z0-9])+$/i", $str);
    }

    /**
     * Check if only contains alpha-numeric characters and dashes.
     *
     * @param string $str
     * @return boolean
     */
    public function alpha_dash($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field may only contain alpha-numeric characters, underscores, and dashes.", $this->label);
        return (bool) preg_match("/^([-a-z0-9_-])+$/i", $str);
    }

    /**
     * Check if the string reaches the min length.
     *
     * @param string $str
     * @param integer $len
     * @return boolean
     */
    public function min_length($str, $len) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must be at least %s characters in length.", $this->label, $len);
        return (bool) (strlen($str) >= $len);
    }

    /**
     * Check if the string don't exceeds the max length.
     *
     * @param string $str
     * @param integer $len
     * @return boolean
     */
    public function max_length($str, $len) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field can not exceed %s characters in length.", $this->label, $len);
        return (bool) (strlen($str) <= $len);
    }

    /**
     * Check if the string reaches the exact length.
     *
     * @param string $str
     * @param integer $len
     * @return boolean
     */
    public function exact_length($str, $len) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must be exactly %s characters in length.", $this->label, $len);
        return (bool) (strlen($str) == $len);
    }

    /**
     * Check if the fields contain the same string.
     *
     * @param string $str
     * @param string $field
     * @return boolean
     */
    public function match($str, $field) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field does not match with the %s field.", $this->label, $field);
        if (!isset($_POST[$field])) {
            return FALSE;
        }
        return (bool) ($str == $_POST[$field]);
    }

    /**
     * Check if the captcha code is valid.
     *
     * @param string $str
     * @param string $field
     * @return boolean
     */
    public function captcha($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The entered code in %s does not match.", $this->label);
        $ME = &get_instance();
        if ($ME->config->get('captcha_sensitive') === false) {
            $str = strtolower($str);
        }
        return (bool) ($ME->session->get('captcha') == md5($str));
    }

    /**
     * Validate csrf token.
     * @param string $str
     * @return type 
     */
    public function csrf($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The csrf token is not valid.");
        $ME = &get_instance();
        return (bool) $ME->security->check_csrf();
    }

    /**
     * Check for a valid uuid.
     *
     * @param string $str
     * @return boolean
     */
    public function uuid($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field may only contain a valid uuid.", $this->label);
        return (bool) preg_match("/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}+$/i", $str);
    }

    /**
     * Validate credit cards.
     * @param string $str
     * @param string $type
     * @return boolean 
     */
    public function creditcard($str, $type) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must be a valid credit card number.", $this->label);
        $str = str_replace(array('-', ' '), '', $str);
        if (strlen($str) < 13) {
            return false;
        }
        $cards = array(
            'amex' => '/^3[4|7]\\d{13}+$/i',
            'visa' => '/^4\\d{12}(\\d{3})?+$/i',
            'diners' => '/^(?:3(0[0-5]|[68]\\d)\\d{11})|(?:5[1-5]\\d{14})+$/i',
        );
        
        $return = false;
        if (isset($cards[$type])) {
            if (preg_match($cards[$type], $str)) {
                $return = $this->luhn($str);
            }
        }
        else{
            foreach ($cards as $check) {
                if (preg_match($check, $str)) {
                    $return = $this->luhn($str);
                }
            }
        }   
        
        return $return;
    }

    /**
     * Luhn validation.
     * @param integer $str
     * @return boolean 
     */
    public function luhn($str) {
        $odd = true;
        $sum = 0;

        foreach (array_reverse(str_split($str)) as $num) {
            $sum += array_sum(str_split(($odd = !$odd) ? $num * 2 : $num));
        }

        return (($sum % 10 == 0) && ($sum != 0));
    }

    /**
     * Check if the value is a valid email.
     *
     * @param string $str
     * @return boolean
     */
    public function valid_email($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must be a valid email address.", $this->label);
        return (bool) preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str);
    }

    /**
     * Check if the value is a valid email.
     *
     * @param type $str
     * @return boolean
     */
    public function valid_url($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must be a valid url.", $this->label);
        $regex = "/^((https?|ftp)\:\/\/)?([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?([a-z0-9-.]*)\.([a-z]{2,3})(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?$/";
        return (bool) preg_match($regex, $str);
    }

    /**
     * Check if the value is a valid ip.
     *
     * @param type $str
     * @return boolean
     */
    public function valid_ip($str) {
        $this->messages[$this->field] = (!empty($this->messages[$this->field])) ? $this->messages[$this->field] : sprintf("The %s field must be a valid ip.", $this->label);

        $segments = explode('.', $str);

        if ($segments[0][0] == '0') {
            return false;
        }

        if (count($segments) != 4) {
            return false;
        }

        foreach ($segments as $segment) {
            if ($segment == '' || preg_match("/[^0-9]/", $segment) || $segment > 255 || strlen($segment) > 3) {
                return false;
            }
        }

        return true;
    }

}

// END Validation Class

/* End of file Validation.php */
/* Location: ./classes/Validation.php */