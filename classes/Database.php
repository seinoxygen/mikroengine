<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package		Mikroengine
 * @author		Mikrobytes Dev Team
 * @copyright           Copyright (c) 2011 - 2011, Mikrobytes.
 * @license
 * @link		http://www.mikroengine.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Database Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Database
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Database {

    private $config;

    private $select = "*";
    private $from = null;
    private $where;
    private $or_where;
    private $where_in;
    private $or_where_in;
    private $where_not_in;
    private $or_where_not_in;
    private $like;
    private $not_like;
    private $or_like;
    private $or_not_like;
    private $join;
    private $group;
    private $order;

    private $params = array();
    private $counter = 0;

    public $error;
    public $errorno;
    public $affected_rows;
    public $last_query;
    public $inserted_id;

    public function __construct() {
        $this->config = &load_class('Config', 'core');
        $this->config->load('database');
        $this->connect();
    }

    public function __destruct() {
        $this->pdo = null;
    }

    public function __sleep(){

    }

    public function __wakeup(){
        $this->connect();
    }

    public function initialize($config = array()){
        foreach($config as $key => $value){
            $this->config->set($key, $value);
        }
        $this->connect();
    }

    /**
     * Establish database connection
     */
    public function connect(){
        $dsn = $this->config->get('db_engine').":dbname=".$this->config->get('db_database').";host=".$this->config->get('db_server');
        $this->pdo = new PDO($dsn, $this->config->get('db_username'), $this->config->get('db_password'), $this->config->get('db_options'));
    }

    /**
     * Run a simple query.
     *
     * @param string $sql
     * @return object
     */
    public function query($sql, $params = array()){
        $this->result = $this->pdo->prepare($sql);

        return $this->result->execute($params);
        $this->clear_data();
    }

    /**
     * Inserts data array in table.
     * If fields is setted it will generate a ON DUPLICATE KEY UPDATE and will update the provided fields.
     *
     * @param array $data
     * @param string $table
     * @param array $fields
     */
    public function insert($data, $table, $fields = null){
        if(!is_array($data) || empty($table)){
            return;
        }

        $columns = array();
        $values = array();
        $update = array();
        $duplicate = "";
        foreach($data as $column => $value){
            $columns[] = $column;
            $value = $this->parametrize($column, $value);
            $values[] = $value;
            
            if(!is_null($fields) && is_array($fields)){
                if(in_array($column, $fields)){
                    $update[] = "$column = $value";
                }
                $duplicate = " ON DUPLICATE KEY UPDATE ".implode(",", $update);
            }
        }

        $sql = "INSERT INTO $table (".implode(",", $columns).") VALUES (".implode(",", $values).")".$duplicate.";";

        $sql = preg_replace('/\s+/', ' ', $sql);
        $sql = str_replace(' ;', ';', $sql);

        $this->result = $this->pdo->prepare($sql);
        
        foreach($this->params as $array){
            $param = key($array);
            $value = $array[key($array)];
            $this->result->bindValue($param, $value);
            $sql = str_replace($param, $value, $sql);
        }

        $this->last_query = $sql;

        $this->result->execute();
        
        $this->inserted_id = $this->pdo->lastInsertId();

        $this->clear_data();
    }

    /**
     * Updates data on table.
     *
     * @param array $data
     * @param string $table
     */
    public function update($data, $table){
        if(!is_array($data) || empty($table)){
            return;
        }

        $sets = array();
        foreach($data as $column => $value){
            $value = $this->parametrize($column, $value);
            $sets[] = $column." = ".$value;
        }

        $where = '';
        if(!empty($this->where)){
            $where = "WHERE ".implode(" AND ", $this->where);//FIXME: Not allways and on wheres.
        }

        $sql = "UPDATE $table SET ".implode(",", $sets)." $where;";

        $this->result = $this->pdo->prepare($sql);

        foreach($this->params as $array){
            $param = key($array);
            $value = $array[key($array)];
            $this->result->bindValue($param, $value);
            $sql = str_replace($param, $value, $sql);
        }

        $this->last_query = $sql;
        
        $this->result->execute();

        //$this->affected_rows = $this->pdo->rowCount();

        $this->clear_data();
    }

    /**
     * Delete data from specified table.
     *
     * @param string $table
     */
    public function delete($table){
        if(empty($table)){
            return;
        }

        $where = "";
        if(!empty($this->where)){
            $where = "WHERE ".implode(" AND ", $this->where);//FIXME: Not allways and on wheres.
        }

        $sql = "DELETE FROM $table $where;";

        $this->result = $this->pdo->prepare($sql);

        foreach($this->params as $array){
            $param = key($array);
            $value = $array[key($array)];
            $this->result->bindValue($param, $value);
        }

        $this->result->execute();

        $this->clear_data();
    }

    /**
     * Selects the fields to work with.
     *
     * @param string $select
     */
    public function select($select){
        $this->select = $select;
        return $this;
    }

    /**
     * Select the main table to use in queries.
     *
     * @param string $table
     */
    public function from($table){
        $this->from = $table;
        return $this;
    }

    /**
     *
     * @param string $table
     * @param integer $offset
     * @param integer $limit
     * @return object
     */
    public function get($table = null, $offset = null, $limit = null){

        $table = (is_null($table) && !is_null($this->from)) ? $this->from : $table;

        if(!empty($this->join)){
            $table = $this->from." ".implode(" ", $this->join);
        }

        $table = "FROM $table";

        // WHERE
        $where = "";
        if(!empty($this->where)){
            $where = "WHERE ".implode(" AND ", $this->where);
        }

        // Can't be a or in the sql if it has not where previously.
        $or_where = "";
        if(!empty($this->or_where)){
            if(!empty($where)){
                $or_where = " OR ".implode(" OR ", $this->or_where);
            }
        }

        // WHERE IN
        $where_in = "";
        if(!empty($this->where_in)){
            if(!empty($where)){
                $where_in = " AND ".implode(" AND ", $this->where_in);
            }
            else{
                $where = "WHERE ".implode(" AND ", $this->where_in);
            }
        }

        // Can't be a or in the sql if it has not where previously.
        $or_where_in = "";
        if(!empty($this->or_where_in)){
            if(!empty($where)){
                $or_where_in = " OR ".implode(" OR ", $this->or_where_in);
            }
        }

        // WHERE NOT IN
        $where_not_in = "";
        if(!empty($this->where_not_in)){
            if(!empty($where)){
                $where_not_in = " AND ".implode(" AND ", $this->where_not_in);
            }
            else{
                $where = "WHERE ".implode(" AND ", $this->where_not_in);
            }
        }

        // Can't be a or in the sql if it has not where previously.
        $or_where_not_in = "";
        if(!empty($this->or_where_not_in)){
            if(!empty($where)){
                $or_where_not_in = " OR ".implode(" OR ", $this->or_where_not_in);
            }
        }

        // LIKE
        $like = "";
        if(!empty($this->like)){
            if(!empty($where)){
                $like = " AND ".implode(" AND ", $this->like);
            }
            else{
                $where = "WHERE ".implode(" AND ", $this->like);
            }
        }

        // NOT LIKE
        $not_like = "";
        if(!empty($this->not_like)){
            if(!empty($where)){
                $not_like = " AND ".implode(" AND ", $this->not_like);
            }
            else{
                $where = "WHERE ".implode(" AND ", $this->not_like);
            }
        }

        // Can't be a or in the sql if it has not where previously.
        $or_like = "";
        if(!empty($this->or_like)){
            if(!empty($where)){
                $or_like = " OR ".implode(" OR ", $this->or_like);
            }
        }

        // Can't be a or in the sql if it has not where previously.
        $or_not_like = "";
        if(!empty($this->or_not_like)){
            if(!empty($where)){
                $or_not_like = " OR ".implode(" OR ", $this->or_not_like);
            }
        }

        $group = "";
        if(!empty($this->group)){
            $group = "GROUP BY ".implode(", ", $this->group);
        }

        $order = "";
        if(!empty($this->order)){
            $order = "ORDER BY ".implode(", ", $this->order);
        }

        $limit_sql = "";
        if(!empty($offset) && !empty($limit)){
            $offset = intval($offset);
            $limit = intval($limit);
            $limit_sql = "LIMIT $offset, $limit";
        }

        $sql = "SELECT " . $this->select . " $table $where $or_where $where_in $or_where_in $where_not_in $or_where_not_in $like $not_like $or_like $or_not_like $group $order $limit_sql;";

        // Remove multiple spaces.
        $sql = preg_replace('/\s+/', ' ', $sql);
        $sql = str_replace(' ;', ';', $sql);

        $this->result = $this->pdo->prepare($sql);

        foreach($this->params as $array){
            $param = key($array);
            $value = $array[key($array)];
            $this->result->bindValue($param, $value);
            $sql = str_replace($param, $value, $sql);
        }

        $this->last_query = $sql;

        $this->result->execute();

        return $this;
    }

    /**
     * Get only one row as object.
     *
     * @return object
     */
    public function result(){
        if(empty($this->result)){
            $this->get();
        }
        $result = $this->result->fetch(PDO::FETCH_OBJ);
        $this->clear_data();
        return $result;
    }

    /**
     * Get all the rows as object.
     *
     * @return object
     */
    public function result_array(){
        if(empty($this->result)){
            $this->get();
        }
        $result = $this->result->fetchAll(PDO::FETCH_OBJ);
        $this->clear_data();
        return $result;
    }

    /**
     * Get only one row as array.
     *
     * @return array
     */
    public function row(){
        if(empty($this->result)){
            $this->get();
        }
        $result = $this->result->fetch(PDO::FETCH_ASSOC);
        $this->clear_data();
        return $result;
    }

    /**
     * Get all rows as array.
     *
     * @return array
     */
    public function row_array(){
        if(empty($this->result)){
            $this->get();
        }
        $result = $this->result->fetchAll(PDO::FETCH_ASSOC);
        $this->clear_data();
        return $result;
    }
    
    /**
     * Return an unique value.
     * 
     * @return string
     */
    public function value(){
        $key = $this->select;
        $result = $this->row();
        return $result[$key];
    }

    /**
     * Count the total rows in a table.
     *
     * @param string $table
     * @return integer
     */
    public function count($table = null){
        $table = (is_null($table) && !is_null($this->from)) ? $this->from : $table;
        $table = "FROM $table";

        $where = "";
        if(!empty($this->where)){
            $where = "WHERE ".implode(" AND ", $this->where);//FIXME: Not allways and on wheres.
        }

        $sql = "SELECT COUNT(*) $table $where;";

        $this->result = $this->pdo->prepare($sql);

        foreach($this->params as $array){
            $param = key($array);
            $value = $array[key($array)];
            $this->result->bindValue($param, $value);
        }

        $this->result->execute();

        $result = $this->result->fetchColumn();

        $this->clear_data();

        return intval($result);
    }

    /**
     * Add where filter.
     *
     * @param string $column
     * @param string $value
     * @param string $operand
     */
    public function where($column, $value, $operand = '='){
        $value = $this->parametrize($column, $value);
        $this->where[] = "$column $operand $value";
        return $this;
    }

    /**
     * Add a or where filter.
     *
     * @param string $column
     * @param string $value
     * @param string $operand
     * @return Database
     */
    public function or_where($column, $value, $operand = null){
        $value = $this->parametrize($column, $value);
        if(!is_null($operand)){
            $this->or_where[] = "$column $operand $value";
        }
        else{
            $this->or_where[] = "$column = $value";
        }
        return $this;
    }

    /**
     * Add a where in filter.
     *
     * @param string $column
     * @param string $values
     * @return Database
     */
    public function where_in($column, $values){
        $values = $this->build_array($values);
        $arr = array();
        foreach($values as $value){
            $arr[] = $this->parametrize($column, $value);
        }
        $this->where_in[] = "$column IN(".implode(",", $arr).")";
        return $this;
    }

    /**
     * Add a where not in filter.
     *
     * @param string $column
     * @param string $values
     * @return Database
     */
    public function where_not_in($column, $values){
        $arr = array();
        foreach($values as $value){
            $arr[] = $this->parametrize($column, $value);
        }
        $this->where_not_in[] = "$column NOT IN('".implode("','", $arr)."')";
        return $this;
    }

    /**
     * Add a or where not in filter.
     *
     * @param string $column
     * @param string $values
     * @return Database
     */
    public function or_where_in($column, $values){
        $values = $this->build_array($values);
        $arr = array();
        foreach($values as $value){
            $arr[] = $this->parametrize($column, $value);
        }
        $this->or_where_in[] = "$column IN(".implode(",", $arr).")";
        return $this;
    }

    /**
     * Add a or where not in filter.
     *
     * @param type $column
     * @param type $values
     * @return Database
     */
    public function or_where_not_in($column, $values){
        $values = $this->build_array($values);
        $arr = array();
        foreach($values as $value){
            $arr[] = $this->parametrize($column, $value);
        }
        $this->or_where_not_in[] = "$column NOT IN('".implode("','", $arr)."')";
        return $this;
    }

    /**
     * Add a like filter.
     *
     * @param string $column
     * @param string $value
     * @param string $wildcard
     * @return Database
     */
    public function like($column, $value, $wildcard = 'both'){
        $value = $this->encapsulate($value, $wildcard);
        $value = $this->parametrize($column, $value);
        $this->like[] = "$column LIKE $value";
        return $this;
    }

    /**
     * Add a not like filter.
     *
     * @param string $column
     * @param string $value
     * @param string $wildcard
     * @return Database
     */
    public function not_like($column, $value, $wildcard = 'none'){
        $value = $this->encapsulate($value, $wildcard);
        $value = $this->parametrize($column, $value);
        $this->not_like[] = "$column NOT LIKE $value";
        return $this;
    }

    /**
     * Add a or like filter.
     *
     * @param string $column
     * @param string $value
     * @param string $wildcard
     * @return Database
     */
    public function or_like($column, $value, $wildcard = 'both'){
        $value = $this->encapsulate($value, $wildcard);
        $value = $this->parametrize($column, $value);
        $this->or_like[] = "$column LIKE $value";
        return $this;
    }

    /**
     * Add a or not like filter.
     *
     * @param string $column
     * @param string $value
     * @param string $wildcard
     * @return Database
     */
    public function or_not_like($column, $value, $wildcard = 'both'){
        $value = $this->encapsulate($value, $wildcard);
        $value = $this->parametrize($column, $value);
        $this->or_not_like[] = "$column NOT LIKE $value";
        return $this;
    }

    /**
     * Join two or more tables.
     *
     * @param string $table
     * @param string $bridge
     * @param string $method
     */
    public function join($table, $bridge, $method = ""){
        if(empty($table) || empty($bridge)){
            return;
        }

        $method = strtoupper($method);
        $methods = array("", "INNER", "LEFT", "RIGHT", "LEFT OUTER", "RIGHT OUTER");
        if(!in_array($method, $methods)){
            $method = "";
        }

        if(empty($this->from)){
            return;
        }

        $table = $this->parametrize($table);

        $this->join[] = "$method JOIN $table ON $bridge";
        return $this;
    }

    /**
     * Group by the provided field.
     *
     * @param string $field
     */
    public function group($field){
        $this->group[] = $field;
        return $this;
    }

    /**
     * Order by the provided field and order.
     *
     * @param string $field
     * @param string $order
     */
    public function order($field, $order){
        $order = strtoupper($order);
        $this->order[] = "$field $order";
        return $this;
    }

    /**
     * Clear the sql filters.
     */
    private function clear_data(){
        unset($this->select);
        unset($this->where);
        unset($this->or_where);
        unset($this->like);
        unset($this->not_like);
        unset($this->or_like);
        unset($this->or_not_like);
        unset($this->where_in);
        unset($this->or_where_in);
        unset($this->where_not_in);
        unset($this->or_where_not_in);
        unset($this->join);
        unset($this->group);
        unset($this->order);
        unset($this->params);
        $this->params = array();
        $this->result = null;
    }

    /**
     * Stores param name and value to use in sql construct.
     * The internal counter ensures that the paramether is unique if we need to print the sql statement once parced.
     *
     * @param string $param
     * @param string $value
     */
    private function parametrize($param, $value){
        $param = $param.'_'.$this->counter;
        $param = ':'.preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($param));
        array_push($this->params, array($param => $value));
        $this->counter++;
        return $param;
    }

    /**
     * Convert to array if a item is string.
     * Used in in statements where in items can be arrays.
     *
     * @param string $values
     * @return array
     */
    private function build_array($values){
        if(is_array($values)){
            return $values;
        }
        return array($values);
    }

    private function encapsulate($value, $wildcard = 'none'){
        $has_wildcard = false;
        $left = '';
        if($wildcard == 'left' || $wildcard == 'both'){
            $has_wildcard = true;
            $left = '%';
        }
        $right = '';
        if($wildcard == 'right' || $wildcard == 'both'){
            $has_wildcard = true;
            $right = '%';
        }

        return $left.$value.$right;
    }

    /**
     * Create a new table.
     *
     * @param string $table
     */
    public function create_table($table){
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS $table;");
    }

    /**
     * Drop a table.
     *
     * @param string $table
     */
    public function drop_table($table){
        $this->pdo->exec("DROP TABLE IF EXISTS $table;");
    }

    /**
     * Rename a table.
     *
     * @param string $oldname
     * @param string $newname
     */
    public function rename_table($oldname, $newname){
        $this->pdo->exec("ALTER TABLE $oldname RENAME TO $newname;");
    }
}
// END Database Class

/* End of file Database.php */
/* Location: ./classes/Database.php */