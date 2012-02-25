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
 * Session Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Session
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Session {

    private $sid;
    
    private $config;
    
    private $use_database = false;
    private $table = null;
    private $database;

    public function __construct() {
        $ME = &get_instance();
        $this->config = $ME->config;
        $this->config->load('session');
        
        $life = $this->config->get('sess_life');
        session_cache_limiter('must-revalidate');
        session_cache_expire($life / 60);
        
        if($this->config->get('sess_table') && $this->config->get('db_enable')){
            $this->table = $this->config->get('sess_table');
            $this->use_database = true;
        }
        
        if($this->use_database === true){
            if(!isset($ME->database)){
                $ME->load->library('database');
            }
            $this->database = $ME->database;
        }
        
        $this->sid = session_id();
        if(empty($this->sid)){
            session_start();
            $this->sid = session_id();
            if(!is_null($this->table)){
                $data = array(
                    'id' => $this->sid,
                    'date' => time(),
                    'data' => ''
                );
                $this->database->insert($data, $this->table, array('date'));
            }
        }
    }

    /**
     * Set session var.
     *
     * @param type $key
     * @param type $val
     */
    public function set($key, $val){
        if($this->use_database === true){
            $data = $this->database->select('data')->from($this->table)->where('id', $this->sid)->value();
            
            // If there's a ", ', :, or ; in any of the array values the serialization gets corrupted thats why we use base64.
            if($data != 'null'){
                $data = unserialize(base64_decode($data));
            }
            if(is_array($data)){
                $data = array_merge($data, array($key => $val));
            }
            else{
                $data = array($key => $val);
            }
            
            // If var is null it was called by remove function.
            if(is_null($val)){
                unset($data[$key]);
            }
            
            $data = base64_encode(serialize($data));
            
            $update = array(
                'data' => $data
            );
            $this->database->update($update, $this->table);
        }
        else{
            $_SESSION[$key] = $val;
        }
    }

    /**
     * Get session value from specified var.
     *
     * @param type $key
     * @return type
     */
    public function get($key){
        if($this->use_database === true){
            $data = $this->database->select('data')->from($this->table)->where('id', $this->sid)->value();
            $data = unserialize(base64_decode($data));
            return (!empty($data[$key])) ? $data[$key] : '';
        }
        return (!empty($_SESSION[$key])) ? $_SESSION[$key] : '';
    }

    /**
     * Unset session var.
     *
     * @param string $key
     */
    public function remove($key){
        if($this->use_database === true){
            $this->set($key, null);
        }
        unset($_SESSION[$key]);
    }

    /**
     * Destroy session values.
     */
    public function destroy(){
        if($this->use_database === true){
            $this->database->where('id', $this->sid);
            $this->database->delete($this->table);
        }
        session_destroy();
    }
    
    /**
     * Remove old sessions.
     */
    private function remove_old(){
        $life = $this->config->get('sess_life');
        $void = time() - $life;
        $this->database->where('date', $void, '<');
        $this->database->delete($this->table);
    }

}
// END Session Class

/* End of file Session.php */
/* Location: ./classes/Sessions.php */