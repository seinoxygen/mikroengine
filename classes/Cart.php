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
        $this->items = $this->session->get('cart');
    }

    /**
     * Add a new item in the cart.
     * 
     * @param array $item 
     */
    public function add($item){
        if(is_array($item)){
            foreach($item as $subitem){
                if(!is_array($subitem)){
                    $item['uid'] = sha1($item['id'].serialize($item['options']));
                    array_push($this->items, $item);
                }
                else{
                    $subitem['uid'] = sha1($subitem['id'].serialize($subitem['options']));
                    array_push($this->items, $subitem);
                }
            }
        }
        $this->session->set('cart', $this->items);
    }

    /**
     * Edit an item in the cart.
     * 
     * @param array $item 
     */
    public function edit($item){
        $i = 0;
        $remove = false;
        for ($i = 0; $i < sizeof($this->items); $i++){
            if($this->items[$i]['uid'] == $item['uid']){
                if($item['quantity'] == 0){
                    $remove = true;
                }
                break;
            }
        }
        // Remove the item from the cart if the quantity is zero.
        if($remove === true){
            unset($this->items[$i]);
        }
        else{
            array_merge($this->items[$i], $item);
        }
    }

    /**
     * Remove the item from the cart.
     * 
     * @param type $uid 
     */
    public function remove($uid){
        $this->edit(array('uid' => $uid, 'quantity' => 0));
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
     * Return the total items in the cart.
     * 
     * @return integer
     */
    public function get_item_count(){
        $total = 0;
        foreach($this->items as $item){
            $total += $item['quantity'];
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