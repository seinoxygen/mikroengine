<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

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
 * Mail Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Mail
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Mail {
    
    protected $connection;
    
    private $smtp_server;
    private $smtp_port = 25;
    private $smtp_timeout = 60;
    private $smtp_username;
    private $smtp_password;
    
    private $newline = "\r\n";
    private $charset = "utf-8";
    
    public $format = "plain";
        
    private $headers;
    
    private $from;
    private $to = array();
    private $cc = array();
    private $bcc = array();
    
    public $priority = 3;
    
    public $subject;
    public $message = "";
    
    private $boundary;
    private $attachments = array();
    
    public $debug = array();
    
    private $config;
    private $mime;
        
    public function __construct(){
        $ME = &get_instance();
        $ME->load->library('Mime');
        $ME->config->load('mail');
        
        $this->config = $ME->config;
        $this->mime = $ME->mime;
        
        $this->initialize($this->config->items());
    }
    
    /**
     * Initialize custom configuration.
     * 
     * @param array $config 
     */
    public function initialize($config = array()){
        if(!empty($config)){
            foreach ($config as $key => $val) {
                if(!empty($val)){
                    $this->$key = $val;
                }
            }
        }
    }
        
    /**
     * Set the from email
     * 
     * @param string $email
     * @param string $name 
     */
    public function from($email, $name = null){
        if(is_null($name)){
            $from = "<$email>";
        }
        else{
            $from = "$name <$email>";
        }
        $this->from = $from;
        $this->headers['From'] = $from;
    }
    
    /**
     * Set the reply to email
     * 
     * @param string $email 
     */
    public function reply_to($email){
        if(is_null($name)){
            $this->headers['Reply-To'] = "<$email>";
        }
        else{
            $this->headers['Reply-To'] = "$name <$email>";
        }
    }
    
    /**
     * Set the receipments
     * 
     * @param string $email 
     */
    public function to($email){
        if(is_array($email)){
            $this->to = array_merge($this->to, $email);
        }
        else{
            array_push($this->to, $email);
        }
        $this->headers['To'] = "<".implode(', ', $this->to).">";
    }
    
    /**
     * Adds cc
     * 
     * @param string $email
     */
    public function cc($email){
        if(is_array($email)){
            $this->cc = array_merge($this->cc, $email);
        }
        else{
            array_push($this->cc, $email);
        }
    }
    
    /**
     * Adds bcc
     * 
     * @param string $email
     */
    public function bcc($email){
        if(is_array($email)){
            $this->bcc = array_merge($this->bcc, $email);
        }
        else{
            array_push($this->bcc, $email);
        }
    }
    
    /**
     * Set the subject
     * 
     * @param string $subject 
     */
    public function subject($subject){
        $this->subject = $subject;
    }
    
    /**
     * Set the message
     * 
     * @param string $message 
     */
    public function message($message){
        $this->message = $message;
    }
    
    /**
     * Attachs a file to be send.
     * Returns true if file exists and was attached, false if not.
     * 
     * @param string $file
     * @return boolean 
     */
    public function attach($file){
        if(file_exists($file)){
            switch ($this->format) {
                case 'plain':
                case 'plain-attach':
                    $this->format = 'plain-attach';
                    break;
                case 'html':
                case 'html-attach':
                    $this->format = 'html-attach';
                    break;
                default:
                    $this->format = 'plain';
                    break;
            }
            
            $this->attachments[] = $file;
            return true;
        }
        return false;
    }
    
    /**
     * Set the mail format.
     * 
     * @param string $format 
     */
    public function format($format){
        $format = strtolower($format);
        if(in_array($format, array('plain', 'html'))){
            $this->format = $format;
        }
    }
    
    /**
     * Set the mail priority.
     * 
     * @param type $priority 
     */
    public function priority($priority){
        if($priority <= 5 && $priority >= 1){
            $this->priority = $priority;
        }
    }
        
    /**
     * Set RFC 822 Date
     *
     * @return	string
     */
    private function date() {
        $diff_second = date("Z");
        if ($diff_second > 0){
            $sign = "+";
        }
        else{
            $sign = "-";
        }

        $diff_second = abs($diff_second);

        $diff_hour = floor($diff_second / 3600);
        $diff_minute = floor(($diff_second - 3600 * $diff_hour) / 60);

        $zonename = "(" . strftime("%Z") . ")";
        $result = sprintf("%s%02d%02d %s", $sign, $diff_hour, $diff_minute, $zonename);

        return date("D, j M Y H:i:s ", time()) . $result;
    }
    
    /**
     * Return the boundary or generate one
     * 
     * @return type 
     */
    private function boundary(){
        if(!empty($this->boundary)){
            return $this->boundary;
        }
        $this->boundary = strtoupper(sha1(time()));
        return $this->boundary;
    }
    
    /**
     * Compile the headers depending of the message format
     * 
     * @return string 
     */
    private function compile_headers(){
        $headers = "";    
        $headers .= "X-Mailer: Mikroengine". $this->newline; 
        $headers .= "X-Priority: " .$this->priority. $this->newline;
        
        $headers .= "Date: " . $this->date() . $this->newline;     
        $headers .= "Subject: " . $this->subject . $this->newline;
                
        foreach ($this->headers as $key => $val) {
            $headers .= $key . ": " . $val . $this->newline;
        }
        
        $headers .= "MIME-Version: 1.0" . $this->newline;
        
        switch ($this->format){
            case 'plain':
                $headers .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
                $headers .= "Content-Transfer-Encoding: 8bit" . $this->newline;
                break;
            
            case 'plain-attach':
                $headers .= "Content-Type: multipart/mixed; boundary=\"" . $this->boundary() . "\"" . $this->newline;
                break;
            
            case 'html':
                $headers .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
		$headers .= "Content-Transfer-Encoding: quoted-printable" . $this->newline;
                break;
        }
                
        return $headers;
    }
    
    /**
     * Build the message body and add atachments
     * 
     * @return string
     */
    private function compile_message(){
        
        $headers = $this->compile_headers();

        switch ($this->format) {
            case 'plain':
                return $headers . $this->newline . preg_replace('/^\./m', '..$1', $this->message);
                break;

            case 'plain-attach':
                $message = $headers . $this->newline;
                $message .= "--" . $this->boundary() . $this->newline;

                $message .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
                $message .= "Content-Transfer-Encoding: 8bit" . $this->newline . $this->newline;
                $message .= preg_replace('/^\./m', '..$1', $this->message) . $this->newline;

                foreach ($this->attachments as $attachment) {
                    $file = pathinfo($attachment);
                    $mime = $this->mime->get_mime($file['extension']);
                    $name = basename($attachment);

                    $message .= $this->newline . "--" . $this->boundary() . $this->newline;
                    $message .= "Content-Type: $mime" . $this->newline;
                    $message .= "Content-Transfer-Encoding: base64" . $this->newline;
                    $message .= "Content-Disposition: attachment; filename=\"$name\"" . $this->newline . $this->newline;

                    $file = fopen($attachment, "r");
                    while ($tmp = fread($file, 570)) {
                        $message .= chunk_split(base64_encode($tmp));
                    }
                    fclose($file);
                }
                return $message . $this->newline . "--" . $this->boundary() . "--" . $this->newline;
                break;

            case 'html':
                return $headers . $this->newline . preg_replace('/^\./m', '..$1', $this->message);
                break;
        }
    }
    
    /**
     * Send data to the smtp server
     * 
     * @param string $data
     * @param integer $nls 
     */
    private function send_data($data, $nls = 1){
        $newlines = "";
        for ($i = 0; $i < $nls; $i++) {
            $newlines .= $this->newline;
        }
        fwrite($this->connection, $data . $newlines);
    }
    
    /**
     * Send a command to the smtp server
     * 
     * @param string $command
     * @param integer $nls
     * @return string 
     */
    private function send_command($command, $nls = 1){
        $this->send_data($command, $nls);

        $this->debug[] = "Cmd: ".htmlentities($command); 
        
        if($command == "QUIT"){
            return;
        }        
        
        return $this->response();
    }
    
    /**
     * Get the response from server
     * 
     * @return string 
     */
    public function response(){
        $data = fread($this->connection, 512);
        $this->debug[] = $data; 
        return $data;
    }
        
    /**
     * Send the email
     * Return true on success
     * 
     * @return boolean 
     */
    public function send(){
        $success = false;

        $this->connection = fsockopen($this->smtp_server, $this->smtp_port, $errno, $errstr, $this->smtp_timeout);
        $this->response();
        
        $this->send_command('EHLO '.$this->smtp_server);

        $this->send_command('AUTH LOGIN');
        
        $this->send_command(base64_encode($this->smtp_username));
        
        $this->send_command(base64_encode($this->smtp_password));
                
        if(!empty($this->from)){
            $this->send_command('MAIL FROM: ' . $this->from);
        }
        
        if(!empty($this->to)){
            foreach($this->to as $to){
                $this->send_command('RCPT TO: ' . $to);
            }
        }
        
        if(!empty($this->cc)){
            foreach($this->cc as $cc){
                $this->send_command('RCPT TO: ' . $cc);
            }
        }
        
        if(!empty($this->bcc)){
            foreach($this->bcc as $bcc){
                $this->send_command('RCPT TO: ' . $bcc);
            }
        }       

        $this->send_command('DATA');
                                                           
        $this->send_data($this->compile_message(),0);
        
        $response = $this->send_command($this->newline.'.'.$this->newline);

        if(substr($response, 0, 3) == 250){
            $success = true;
        }
        
        $this->send_command('QUIT');
        fclose($this->connection);
                        
        return $success;
    }
    
    /**
     * Return the debug array
     * 
     * @return array
     */
    public function debug(){
        return $this->debug;
    }
}
// END Mail Class

/* End of file Mail.php */
/* Location: ./classes/Mail.php */