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
    private $charset = "utf-8";
    
    public $format = "plain";
    
    public $username;
    public $password;
    
    private $headers;
    
    private $from;
    private $to = array();
    private $cc = array();
    private $bcc = array();
    
    public $priority = 3;
    
    public $subject;
    public $message = "";
    
    private $boundary;
    private $attachments;
    
    public $debug = array();
        
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
    
    public function reply_to($email){
        if(is_null($name)){
            $this->headers['Reply-To'] = "<$email>";
        }
        else{
            $this->headers['Reply-To'] = "$name <$email>";
        }
    }
    
    public function to($email){
        if(is_array($email)){
            $this->to = array_merge($this->to, $email);
        }
        else{
            array_push($this->to, $email);
        }
        $this->headers['To'] = "<".implode(', ', $this->to).">";
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
        }
    }
    
    public function message($message){
        $this->message = $message;
    }
    
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
    
    private function boundary(){
        if(!empty($this->boundary)){
            return $this->boundary;
        }
        $this->boundary = strtoupper(sha1(time()));
        return $this->boundary;
    }
    
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
                echo "ingreso a ".$this->format."<br>";
                $headers .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
                $headers .= "Content-Transfer-Encoding: 8bit" . $this->newline;
                break;
            
            case 'plain-attach':
                echo "ingreso a ".$this->format."<br>";
                $headers .= "Content-Type: multipart/mixed; boundary=\"" . $this->boundary() . "\"" . $this->newline;
                break;
            
            case 'html':
                echo "ingreso a ".$this->format."<br>";
                $headers .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
		$headers .= "Content-Transfer-Encoding: quoted-printable" . $this->newline;
                break;
        }
                
        return $headers;
    }
    
    private function compile_message(){
        
        $headers = $this->compile_headers();

        if(count($this->attachments) > 0){
            $message = $headers . $this->newline;
            $message .= "--" . $this->boundary() . $this->newline;
            
            $message .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
            $message .= "Content-Transfer-Encoding: 8bit" . $this->newline.$this->newline;
            $message .= preg_replace('/^\./m', '..$1', $this->message) . $this->newline;
                        
            foreach($this->attachments as $attachment){
                $file = pathinfo($attachment);
                if ($file['extension'] == ""){
                    $filetype = "application/octet-stream";
                }
                else{
                    $filetype = $this->mime($file['extension']);
                }
                $name = basename($attachment);

                $message .= $this->newline."--".$this->boundary() . $this->newline;
                $message .= "Content-Type: $filetype" . $this->newline;
                $message .= "Content-Transfer-Encoding: base64" . $this->newline;
                $message .= "Content-Disposition: attachment; filename=\"$name\"" . $this->newline . $this->newline;

                $file = fopen($attachment, "r");
                while ($tmp = fread($file, 570)) {
                    $message .= chunk_split(base64_encode($tmp));
                }
                fclose($file);
            }

            $message .= $this->newline . "--" . $this->boundary() . "--" . $this->newline;

            return  $message;
        }
        else{
            return  $headers . $this->newline . preg_replace('/^\./m', '..$1', $this->message);
        }
    }
    
    private function send_data($data, $nls = 1){
        $newlines = "";
        for ($i = 0; $i < $nls; $i++) {
            $newlines .= $this->newline;
        }
        
        fwrite($this->connection, $data . $newlines);
    }
    
    public function send_command($command, $nls = 1, $expected = null){
        
        $newlines = "";
        for ($i = 0; $i < $nls; $i++) {
            $newlines .= $this->newline;
        }

        fwrite($this->connection, $command . $newlines);

        $this->debug[] = "Cmd: ".htmlentities($command); 
        
        if($command == "QUIT"){
            return;
        }        
        
        return $this->response();
    }
    
    public function response(){
        $data = fread($this->connection, 512);
        $this->debug[] = $data; 
        return $data;
    }
        
    public function send(){
        $success = false;

        $this->connection = fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
        $this->response();
        
        $this->send_command('EHLO '.$this->server);

        $this->send_command('AUTH LOGIN');
        
        $this->send_command(base64_encode($this->username));
        
        $this->send_command(base64_encode($this->password));
                
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
    
    public function debug(){
        return $this->debug;
    }
    
    public function mime($ext){
        $mime = array(
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
        );
        return $mime[strtolower($ext)];
    }
}
// END Mail Class

/* End of file Mail.php */
/* Location: ./classes/Mail.php */