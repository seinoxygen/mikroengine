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
 * Template Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Model
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Model {

    public function __construct() {

    }

    public function __get($var) {
        $ME = & get_instance();
        return $ME->$var;
    }

}