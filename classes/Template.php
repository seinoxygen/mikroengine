<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package		Mikroengine
 * @author		Keovi Dev Team
 * @copyright           Copyright (c) 2011 - 2011, Mikrobytes.
 * @license
 * @link		http://www.mikroengine.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Template Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Template
 * @author		Keovi Dev Team
 */

// ------------------------------------------------------------------------

class Template{
    
    private $vars;
       
    /**
     * Parse template file and assigns variables.
     * 
     * @param string $file
     * @param array $data 
     */
    public function parse($file, $data = array()){
        
        $this->vars = $data;
        
        $ME = &get_instance();
        $template = $ME->load->view($file, $data, true);
        
        // Remove php comments.
        $comment_pattern = array('#/\*.*?\*/#s', '#(?<!:)//.*#');  
        $template = preg_replace($comment_pattern, NULL, $template);
        
        if(!empty($this->vars)){
            foreach($this->vars as $name => $value){
                if(!is_array($value)){
                    $template = $this->single($name, $value, $template);
                }
                else{
                    $template = $this->pair($name, $value, $template);
                }
            }
        }
        
        $matchs = $this->match_if($template);
        if($matchs !== false){
            foreach ($matchs as $match){
                // Evaluate the matched expression.
                $true = eval("return ({$match[1]});");

                $template = str_replace($match[0], $true ? (string)$match[2] : '', $template);
            }
        }
        
        $ME->output->append_output($template);
    }            
    
    /**
     * Parse a single template tag.
     * 
     * @param string $name
     * @param string $value
     * @param string $template
     * @return string 
     */
    private function single($name, $value, $template){
        return str_replace('{'.$name.'}', $value, $template);
    }
    
    /**
     * Parse a pair.
     * 
     * @param string $name
     * @param array $data
     * @param string $template
     * @return string 
     */
    private function pair($name, $data, $template) {
        $match = $this->match_pair($template, $name);
        if (false === $match) {
            return $template;
        }

        $item = $match['0'];
        
        $structure = '';
        foreach ($data as $row) {
            $content = $match['1'];
            foreach ($row as $name => $value) {
                if (!is_array($value)) {
                    $content = $this->single($name, $value, $content);
                } 
                else {
                    $content = $this->pair($name, $value, $content);
                }
            }
            $structure .= $content;
        }

        return str_replace($item, $structure, $template);
    }

    /**
     * Tag is pair?
     * 
     * @param string $template
     * @param string $name
     * @return type 
     */
    private function match_pair($template, $name) {
        if (!preg_match("|\{$name\}(.+?)\{/$name\}|s", $template, $match)) {
            return false;
        }
        return $match;
    }
    
    /**
     * Match if there exists any if tag.
     * 
     * @param type $template
     * @return type 
     */
    private function match_if($template){
        if (!preg_match_all('|\{if ([^}]+)\}(.+?)\{/if\}|s', $template, $matches, PREG_SET_ORDER)){
            return false;
        }
        return $matches;
    }
}
// END Template Class

/* End of file Template.php */
/* Location: ./classes/Template.php */