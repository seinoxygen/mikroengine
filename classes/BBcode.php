<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BBcode
 *
 * @author clavepablo
 */
class BBcode {

    public function __construct(){
        
    }

    /**
     * Replace short codes with html tags.
     * 
     * @param string $str
     * @return string 
     */
    public function parse($str){
        $str = trim($str);
        
	$search = array( 	 
            '/\[b\](.*?)\[\/b\]/ms',	
            '/\[i\](.*?)\[\/i\]/ms',
            '/\[u\](.*?)\[\/u\]/ms',
            '/\[img\](.*?)\[\/img\]/ms',
            '/\[email\](.*?)\[\/email\]/ms',
            '/\[url\](.*?)\[\/url\]/ms',
            '/\[url\=?(.*?)?\](.*?)\[\/url\]/ms',
            '/\[size\=?(.*?)?\](.*?)\[\/size\]/ms',
            '/\[color\=?(.*?)?\](.*?)\[\/color\]/ms',
            '/\[code](.*?)\[\/code]/ms',
            '/\[quote](.*?)\[\/quote\]/ms',
            '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
            '/\[list\](.*?)\[\/list\]/ms',
            '/\[\*\]\s?(.*?)\n/ms'
	);

	$replace = array(
            '<strong>\1</strong>',
            '<em>\1</em>',
            '<u>\1</u>',
            '<img src="\1" alt="\1" />',
            '<a href="mailto:\1">\1</a>',
            '<a href="\1">\1</a>',
            '<a href="\1">\2</a>',
            '<span style="font-size:\1px">\2</span>',
            '<span style="color:\1">\2</span>',
            '<pre>\1</pre>',
            '<blockquote>\1</blockquote>',
            '<ol start="\1">\2</ol>',
            '<ul>\1</ul>',
            '<li>\1</li>'
	);
        
	$str = preg_replace($search, $replace, $str);
		
	$str = str_replace("\r", "", $str);
	$str = "<p>".preg_replace("/(\n){2,}/", "</p><p>", $str)."</p>";
	$str = nl2br($str);
        
        return $str;
    }
}
// END BBcode Class

/* End of file BBcode.php */
/* Location: ./classes/BBcode.php */