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
 * Auth Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Auth
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Auth {

    private $driver = 'file';
    private $path = '';
    private $validation = false;

    private $errors = array();

    public function __construct(){
        $this->path = BASEPATH.'data/users/';
    }

    public function initialize($config = array()){
        if(!empty($config)){
            $this->path = $config['path'];
            $this->validation = $config['validation'];
        }
    }

    public function register($name, $pass, $email){
        switch ($this->driver) {
            case 'database':
                $ME = &get_instance();
                $ME->load->library('database');
                if($this->exists($name)){
                    array_push($this->errors, 'Username is already taken.');
                    return false;
                }
                $data = array(
                    'name' => $name,
                    'pass' => $pass,
                    'email' => $email
                );
                $ME->database->insert($data, 'users');
                return true;
            break;

            case 'file':
            default:
                if($this->exists($name)){
                    array_push($this->errors, 'Username is already taken.');
                    return false;
                }

                $doc = new DOMDocument('1.0');

                $doc->formatOutput = true;

                $root = $doc->createElement('user');
                $root = $doc->appendChild($root);

                $e_name = $doc->createElement('name');
                $e_name = $root->appendChild($e_name);

                $text = $doc->createTextNode($name);
                $text = $e_name->appendChild($text);

                $e_pass = $doc->createElement('pass');
                $e_pass = $root->appendChild($e_pass);

                $text = $doc->createTextNode($pass);
                $text = $e_pass->appendChild($text);

                $e_mail = $doc->createElement('email');
                $e_mail = $root->appendChild($e_mail);

                $text = $doc->createTextNode($email);
                $text = $e_mail->appendChild($text);

                if($this->validation === true){
                    $e_active = $doc->createElement('active');
                    $e_active = $root->appendChild($e_active);

                    $text = $doc->createTextNode('0');
                    $text = $e_active->appendChild($text);

                    $e_key = $doc->createElement('key');
                    $e_key = $root->appendChild($e_key);

                    $text = $doc->createTextNode(substr(md5(time()), 0, 16));
                    $text = $e_key->appendChild($text);
                }

                $bytes = $doc->save($this->path.$name.'.xml');

                return ($bytes > 0 && $bytes !== false) ? true : false;
            break;
        }
    }

    private function exists($name){
        switch ($this->driver) {
            case 'database':
                $ME = &get_instance();
                $ME->load->library('database');
                $count = $ME->database->count('users')->where('name', $name);
                return ($count > 0) ? true : false;
            break;

            case 'file':
            default:
                return file_exists($this->path.$name.'.xml');
            break;
        }
    }

    /**
     * Activate the account of the provided user.
     *
     * @param string $name
     * @param string $key
     * @return boolean
     */
    public function activate($name, $key) {
        switch ($this->driver) {
            case 'database':
                $ME = &get_instance();
                $ME->load->library('database');
                $ME->database->update('users', array('active' => '1'))->where('name', $name)->where('pass', $pass);
            break;

            case 'file':
                if ($this->exists($name)) {
                    $doc = new DOMDocument();
                    $doc->load($this->path . $name . '.xml');

                    if ($this->validation === true) {
                        $s_active = $doc->getElementsByTagName('active')->item(0)->nodeValue;
                        $s_key = $doc->getElementsByTagName('key')->item(0)->nodeValue;
                        if ($s_active == '1') {
                            array_push($this->errors, 'The account is already active.');
                            return false;
                        }
                        if ($s_key != $key) {
                            array_push($this->errors, 'Activation key does not match.');
                            return false;
                        }
                        $doc->getElementsByTagName('active')->item(0)->nodeValue = '1';
                        $doc->getElementsByTagName('key')->item(0)->nodeValue = '0';
                        $doc->save($this->path . $name . '.xml');
                    }

                }
                else {
                    array_push($this->errors, 'Username does not exists.');
                    return false;
                }
            default:
            break;
        }
    }

    public function reset_password($name, $pass){

    }

    /**
     * Login user using the provided credentials.
     * Returns true on success or false on failure.
     *
     * @param string $name
     * @param string $pass
     * @return boolean
     */
    public function login($name, $pass) {
        $ME = &get_instance();
        $ME->load->library('database');
        $ME->load->library('session');
        switch ($this->driver) {
            case 'database':
                $user = $ME->database->select('*')->from('users')->where('name', $name)->where('pass', $pass)->row_array();

                if(!empty($user)){
                    $ME->session->set('logged_in', true);
                    $ME->session->set('name', $name);
                    $ME->session->set('id', $user['id']);
                    return true;
                }

                if($this->exists($name)){
                    array_push($this->errors, 'Wrong password.');
                    return false;
                }

                array_push($this->errors, 'Username does not exists.');
                return false;
            break;

            case 'file':
            default:
                if ($this->exists($name)) {
                    $doc = new DOMDocument();
                    $doc->load($this->path . $name . '.xml');
                    $s_name = $doc->getElementsByTagName('name')->item(0)->nodeValue;
                    $s_pass = $doc->getElementsByTagName('pass')->item(0)->nodeValue;
                    if ($this->validation === true) {
                        $s_active = $doc->getElementsByTagName('active')->item(0)->nodeValue;
                        if ($s_active == '0') {
                            array_push($this->errors, 'Account is inactive.');
                            return false;
                        }
                    }
                    if ($pass != $s_pass) {
                        array_push($this->errors, 'Wrong password.');
                        return false;
                    }
                }
                else {
                    array_push($this->errors, 'Username does not exists.');
                    return false;
                }
                $ME->session->set('logged_in', true);
                $ME->session->set('name', $name);
                return true;
            break;
        }
    }

    /**
     * Remove session vars.
     */
    public function logout(){
        $ME = &get_instance();
        $ME->load->library('session');
        $ME->session->remove('name');
        $ME->session->remove('id');
        $ME->session->remove('logged_in');
    }

    /**
     * Return the stored errors.
     *
     * @return array
     */
    public function errors(){
        return $this->errors;
    }

}
// END Auth Class

/* End of file Auth.php */
/* Location: Auth.php */