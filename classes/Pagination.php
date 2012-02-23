<?php

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
 * Pagination Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Pagination
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Pagination {
        
    private $item_limit = 20;                                                   // How many items per page.
    private $total_items;                                                       // Total items
    private $page_links = 10;                                                   // Links to show in the pagination
    private $current_page;                                                      // Current page
    private $base_url;                                                          // Base url to apply
    private $ellipsis = '<span>...</span>';                                     // Ellipsis to show between links
    private $main_wrapper = array('<div class="pagination">', '</div>');        // The main wrapper that will contain all links
    private $current_wrapper = array('<b>', '</b>');                            // Current page wrapper
    
    private $config;
    private $url;

    public function __construct(){
        $ME = &get_instance();
        $this->config = $ME->config;
        $this->config->load('pagination');
        $ME->load->library('url');
        $this->url = $ME->url;
        
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
     * Generate pagination links and return them.
     * 
     * @return string 
     */
    public function create_links(){
        // Total pages.
        $total_pages = floor($this->total_items/$this->item_limit);

        if($total_pages == 1){
            return '';
        }
                
        // Half at a side and half at the other side.
        $half = floor($this->page_links/2);
        
        $url = trim($this->base_url, '/');
            
        $html = $this->main_wrapper[0];
        
        $html .= '<a href="'.$this->url->site($url.'/1').'">First</a>';
        
        // We get the limit of links that we need to build the pagination.
        $start = (($this->current_page - $half) > 0) ? $this->current_page - ($half) : 1;
        $end = (($this->current_page + $half) < $total_pages) ? ($this->current_page + $half) : $total_pages;
        
        $start = ($end == $total_pages) ? ($total_pages - $this->page_links + 1) : $start;
        $end = ($start == 1) ? $this->page_links : $end; 
                
        // Add the first dots between first link and main links
        if($start > 1){
            $html .= $this->ellipsis;
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($this->current_page == $i) {
                $html .= $this->current_wrapper[0] . $i . $this->current_wrapper[1];
            } else {
                $html .= '<a href="'.$this->url->site($url.'/'.$i).'">'.$i.'</a>';
            }
        }
        
        // Add the lasts dots between last link and main links
        if($end < $total_pages){
            $html .= $this->ellipsis;
        }
        
        $html .= '<a href="'.$this->url->site($url.'/'.$total_pages).'">Last</a>';
        
        $html .= $this->main_wrapper[1];
        
        return $html;
    }
    
}

// END Pagination Class

/* End of file Pagination.php */
/* Location: Pagination.php */