<?php

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
 * Pagination Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Pagination
 * @author		Mikrobytes Dev Team
 */

// ------------------------------------------------------------------------

class Pagination {
    
    private $config;
    private $url;

    public function __construct(){
        $ME = &get_instance();
        $this->config = $ME->config;
        $this->config->load('pagination');
        $ME->load->library('url');
        $this->url = $ME->url;
    }
    
    /**
     * Initialize custom configuration.
     * 
     * @param array $config 
     */
    public function initialize($config = array()){
        if(!empty($config)){
            foreach ($config as $key => $value) {
                if(!empty($value)){
                    $this->config->set($key, $value);
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
        
        $page_limit = $this->config->get('pag_limit');
        $current_page = $this->config->get('pag_current');
        
        // Total items.
        $total_items = $this->config->get('pag_items');

        // Total pages.
        $total_pages = floor($total_items/$page_limit);
        
        if($total_pages == 1){
            return '';
        }
        
        // Total links to show.
        $links = $this->config->get('pag_links');
        
        // half at a side and half at the other side.
        $half = floor($links/2);
        
        
        $url = trim($this->config->get('pag_url'), '/');
                
        $wraper = $this->config->get('pag_wrapper');
        $current = $this->config->get('pag_indicator');
        
        $dots = $this->config->get('pag_dots');
        
        $html = $wraper[0];
        
        $html .= '<a href="'.$this->url->site($url.'/1').'">First</a>';
        
        // We get the limit of links that we need to build the pagination.
        $start = (($current_page - $half) > 0) ? $current_page - ($half) : 1;
        $end = (($current_page + $half) < $total_pages) ? ($current_page + $half) : $total_pages;
        
        $start = ($end == $total_pages) ? ($total_pages - $links + 1) : $start;
        $end = ($start == 1) ? $links : $end; 
                
        if($start > 1){
            $html .= $dots;
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($current_page == $i) {
                $html .= $current[0] . $i . $current[1];
            } else {
                $html .= '<a href="'.$this->url->site($url.'/'.$i).'">'.$i.'</a>';
            }
        }
        
        if($end < $total_pages){
            $html .= $dots;
        }
        
        $html .= '<a href="'.$this->url->site($url.'/'.$total_pages).'">Last</a>';
        
        $html .= $wraper[1];
        
        return $html;
    }
    
}

// END Pagination Class

/* End of file Pagination.php */
/* Location: Pagination.php */