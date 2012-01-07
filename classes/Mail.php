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
 * Mail Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Mail
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Mail {
    
    private $to;
    
    public function __construct(){
        
    }
    
    public function initialize($config = array()){
        @imap_open($mailbox, $username, $password, $options);
    }
    
    public function from($email){

    }
    
    public function reply_to($email){
        
    }
    
    public function to($emails){
        if(is_array($emails)){
            $this->to = array_merge($this->to, $emails);
        }
        else{
            array_push($this->to, $emails);
        }
    }
    
    public function cc($email){
        if(is_array($email)){
            
        }
    }
    
    public function bcc($email){
        if(is_array($email)){
            
        }
    }
    
    public function subject($subject){
        
    }
    
    public function attach($file){
        
    }
    
    public function message($message){
        
    }
    
    private function build_message(){
        
    }
    
    public function send(){
        
    }
    
}
// END Mail Class

/* End of file Mail.php */
/* Location: ./classes/Mail.php */