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
 * Cart Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Cart
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Cart {
    
    private $items = array();
    private $session;

    public function __construct(){
        $ME = &get_instance();
        $ME->load->library('session');
        $this->session = $ME->session;
    }

    public function add($item){
        if(is_array($item)){
            array_push($this->items, $item);
        }
        $this->session->set('cart', $this->items);
    }

    public function edit($item){

    }

    public function remove(){

    }

    /**
     * Return the total cost of the cart at the current state.
     *
     * @return integer
     */
    public function get_total(){
        $total = 0;
        if(!empty($this->items)){
            foreach($this->items as $item){
                $total += $item['quantity'] * $item['price'];
            }
        }
        return $total;
    }

    /**
     * Return the items from the cart.
     *
     * @return array
     */
    public function get_contents(){
        return $this->session->get('cart');
    }

    /**
     * Empty the cart.
     */
    public function clear(){
        unset($this->items);
        $this->session->remove('cart');
    }

}
// END Cart Class

/* End of file Cart.php */
/* Location: ./classes/Cart.php */