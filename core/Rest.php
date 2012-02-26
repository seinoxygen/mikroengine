<?php

/**
 * @package		Mikroengine
 * @author		Keovi Dev Team
 * @copyright           Copyright (c) 2011 - 2011, Keovi.
 * @license
 * @link		
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Log Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Rest
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Rest extends Controller {
    
    public $rest = true;
    
    // Config.
    public $format;
    public $key;
    public $table;
    public $table_key;
    public $table_limit;
    public $table_time;
    
    // Data arrays
    private $get;
    private $post;
    
    public $resource;
    
    private $method;
    
    // Max api calls the user can do in one hour.
    public $limit;
    
    public function __construct() {
        parent::__construct();
        
        $config = $this->config->load('rest', true);
        $this->initialize($config);
        
        // Get values are generated in key/pair values. Sliced to remove controller/method.
        $this->get = array_slice($this->uri->to_assoc(), 1);
        $this->post = $this->input->post();
        
        $this->process();
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
     * Start the workflow.
     */
    private function process(){
        // Set the rewuest method.
        $this->method = $this->request_type();
                
        // Set the resource path.
        $this->resource = $this->method.'_'.$this->uri->segment(2);
        
        // If method doesn't exist throw 404 error.
        if(!method_exists($this, $this->resource)){
            $this->response(array('status' => 404, 'error' => 'Unknown method.'), 404);
        }
        
        $key = $this->get_api_key();
        
        // Check api key.
        if(!empty($this->table) && !empty($this->table_key)){
            $count = $this->database->where($this->table_key, $key)->count($this->table);
            if($count == 0){
                $this->response(array('status' => 403, 'error' => 'Unknown api key.'), 403);
            }
        }
        
        // Check limit.
        if(isset($this->limit) && $this->limit > 0){
                        
            $query = $this->database->where($this->table_key, $key)->get($this->table)->row();

            $wait = date('i:s', $query[$this->table_time] - (time() - (60 * 60)));
            
            // Time limit reached.
            $time_limit_reached = ($query[$this->table_time] > time() - (60 * 60)) ? true : false;
            
            // Hits limit reached.           
            $hits_limit_reached = ($query[$this->table_limit] >= $this->limit) ? true : false;
            
            if($time_limit_reached && $hits_limit_reached){
                $this->response(array('status' => 403, 'error' => 'Limit reached.', 'wait' => $wait), 403);
            }
            
            // Reset the counter.
            $count = 1;
            
            if (!$hits_limit_reached) {
                $count = $query[$this->table_limit] + 1;
            }
            
            // Update the database with the new values.
            $update = array(
                $this->table_limit => $count,
                $this->table_time => time()
            );

            $this->database->where($this->table_key, $key)->update($update, $this->table);
        }
    }
    
    /**
     * Detect the request method to call the current function.
     * 
     * @return string
     */
    private function request_type(){
        $method = strtolower($this->input->server('REQUEST_METHOD'));
        if(in_array($method, array('get', 'post'))){
            return $method;
        }
        return 'get';
    }
            
    /**
     * Find the api key in the post or get.
     * 
     * @return string
     */
    public function get_api_key(){
        return $this->get_value($this->key);
    }
    
    /**
     * Return the format response.
     * 
     * @return string
     */
    public function get_format(){
        return $this->get_value('format', $this->format);
    }
    
    /**
     * Return the value for the key in the post/get array.
     * 
     * @param string $key
     * @param string $default
     * @return string 
     */
    public function get_value($key, $default = null){
        switch ($this->method) {
            case 'get':
                if(isset($this->get[$key])){
                    return $this->get[$key];
                }
                break;
            case 'post':
                if(isset($this->post[$key])){
                    return $this->post[$key];
                }
                break;
        }
        if(!is_null($default)){
            return $default;
        }
        return;
    }
    
    /**
     * Format the response to json.
     * 
     * @param array $data
     * @return string 
     */
    private function to_json($data){
        return json_encode($data);
    }
    
    /**
     * Format the response to xml.
     * 
     * @param array $data
     * @return string 
     */
    private function to_xml($data){
        // creating object of SimpleXMLElement
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><response></response>");

        // function call to convert array to xml
        $this->array_to_xml($data, $xml);
        return $xml->asXML();
    }
    
    /**
     * Generate the xml body.
     * 
     * @param array $data
     * @param simplexmlelement $xml 
     */
    private function array_to_xml($data, &$xml) {
        foreach($data as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                }
                else{
                    $this->array_to_xml($value, $xml);
                }
            }
            else {
                $xml->addChild("$key","$value");
            }
        }
    }
    
    /**
     * Format the response to csv.
     * 
     * @param array $data
     * @return string 
     */
    private function to_csv($data){        
        $header = array_keys($data);
        $footer = array_values($data);

        $output = '"' . implode($header, ('"' . ',' . '"')) . '"' . "\n";
        $output .= '"' . implode($footer, ('"' . ',' . '"')) . '"' . "\n";

        return $output;
    }
    
    /**
     * Serialize the response.
     * 
     * @param array $data
     * @return string 
     */
    private function to_serialize($data){
        return serialize($data);
    }


    /**
     * Output the response.
     */
    public function response($data, $code = 200){
        
        if (!is_array($data)) {
            $data = array(
                'msg' => $data
            );
        }
        
        switch ($this->get_format()) {
            case 'json':
                    $data = $this->to_json($data);
                break;
            case 'xml':
                    header('Content-type: text/xml');
                    $data = $this->to_xml($data);
                break;
            case 'csv':
                    $data = $this->to_csv($data);
                break;
            case 'serialize':
                    $data = $this->to_serialize($data);
                break;
        }
        
        header('HTTP/1.1: ' . $code);
	header('Status: ' . $code);
        header('Content-Length: ' . strlen($data));
        die($data);
    }
    
}
// END Rest Class

/* End of file Rest.php */
/* Location: Rest.php */