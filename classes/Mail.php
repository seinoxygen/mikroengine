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
    
    private $connection;
    
    public $server;
    public $port = 25;
    public $timeout = 60;
    
    private $newline = "\r\n";
    private $charset = "UTF-8";
    
    private $format = "plain";
    
    public $username;
    public $password;
    
    private $from;
    private $to;
    private $cc;
    private $bcc;
    
    public $subject;
    
    public function __construct($server = null, $port = 25, $username = null, $password = null){
        if(!is_null($server)){
            $this->server = $server;
            $this->port = $port;
            $this->username = $username;
            $this->password = $password;
        }
    }
    
    public function initialize($config = array()){
        
    }
    
    public function from($email){

    }
    
    public function reply_to($email){
        
    }
    
    public function to($email){
        if(is_array($email)){
            $this->to = array_merge($this->to, $email);
        }
        else{
            array_push($this->to, $email);
        }
    }
    
    public function cc($email){
        if(is_array($email)){
            $this->cc = array_merge($this->cc, $email);
        }
        else{
            array_push($this->cc, $email);
        }
    }
    
    public function bcc($email){
        if(is_array($email)){
            $this->bcc = array_merge($this->bcc, $email);
        }
        else{
            array_push($this->bcc, $email);
        }
    }
    
    public function subject($subject){
        $this->subject = $subject;
    }
    
    public function attach($file){
        
    }
    
    public function message($message){
        
    }
    
    public function html($message){
        
    }
        
    public function send(){
        $success = false;
        $this->connection = fsockopen($this->server, $this->port, null, null, $this->timeout);
        
        fputs($this->connection, 'AUTH LOGIN' . $this->newline);
        fputs($this->connection, base64_encode($username) . $this->newline);
        fputs($this->connection, base64_encode($password) . $this->newline);
        fputs($this->connection, 'HELO ' . $localhost . $this->newline);
        fputs($this->connection, 'MAIL FROM: ' . $this->from . $this->newline);
        
        if(!empty($this->to)){
            foreach($this->to as $to){
                fputs($this->connection, 'RCPT TO: ' . $to . $this->newline);
            }
        }
        
        if(!empty($this->cc)){
            foreach($this->cc as $cc){
                fputs($smtpConnect, 'RCPT TO: ' . $cc . $this->newline);
            }
        }

        if (!empty($this->bcc)) {
            foreach($this->bcc as $bcc){
                fputs($smtpConnect, 'RCPT TO: ' . $bcc . $this->newline);
            }
        }

        fputs($this->connection, 'DATA' . $this->newline);

        flush($this->connection);
                
        fputs($this->connection, 'To:     ' . $this->to . $this->newline);
        fputs($this->connection, 'From:   ' . $this->from . $this->newline);
        fputs($this->connection, 'Subject:' . $this->subject . $this->newline);

        if($this->format != "html"){
            $headers = "Content-Type: text/plain; charset=\"".$this->charset."\"" . $this->newline;
            $headers .= "Content-Transfer-Encoding: 8bit" . $this->newline;

            $message = $this->bodyText();

            fputs($this->connection, $headers . $this->newline . $this->newline);
            fputs($this->connection, $message . $this->newline . '.' . $this->newline);
        }
        else{
            $random = md5(time());

            $headers = "Content-Type: multipart/alternative; boundary=\"--" . $random . "\"".$this->newline;
            $headers .= "--" . $random . $this->newline;
            $headers .= "Content-Type: text/plain; charset=\"".$this->charset."\"" . $this->newline;
            $headers .= "Content-Transfer-Encoding: 8bit" . $this->newline;

            $message = $this->bodyText();

            fputs($this->connection, $headers . $this->newline);
            fputs($this->connection, $message . $this->newline);

            $headers = "--" . $random . $this->newline;
            $headers .= "Content-Type: text/html; charset=\"".$this->charset."\"" . $this->newline;
            $headers .= "Content-Transfer-Encoding: 8bit" . $this->newline;

            $message = $this->bodyHtml();

            fputs($this->connection, $headers . $this->newline);
            fputs($this->connection, $message . $this->newline);

            $headers = "--" . $random . "--".$this->newline;

            fputs($this->connection, $headers . '.' . $this->newline);
        }
        
        $response = fgets($this->connection, 1024);

        if(preg_match('/^\d+$/', $response, $match)){
            if($match[0] == 250){
                $success = true;
            }
        }
        
        fputs($this->connection, 'QUIT' . $this->newline);
        
        return $success;
    }
}
// END Mail Class

/* End of file Mail.php */
/* Location: ./classes/Mail.php */