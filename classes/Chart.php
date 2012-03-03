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
 * Chart Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Chart
 * @author		Keovi Dev Team
 */
// ------------------------------------------------------------------------

class Chart {
    
    private $image;
    
    private $type = 'bars';
    
    private $margin = 25;
    private $steps = 2;
    private $middle_steps;
    private $max, $ratio;
    private $draw_vertical = false;
    private $title;
    
    private $background_color, $border_color, $grid_color, $graph_color, $text_color;
    
    private $width, $height;
    private $grid_width, $grid_height;
    
    private $leyend_width;
    private $leyend_height = 0;
    
    private $bar_width, $pieces;
    
    private $data, $labels, $colors;
    
    public function __construct() {
        $ME = &get_instance();
        $ME->load->library('Image');
        $this->image = $ME->image;
        $config = $ME->config->load('chart', true);

        $this->initialize($config);
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
     * Set graph size.
     * 
     * @param type $width
     * @param type $height 
     */
    public function set_size($width, $height){
        $this->image->create($width, $height);
        $this->width = $width;
        $this->height = $height;
        $this->grid_width = $this->width - $this->margin * 2;
        $this->grid_height = $this->height - $this->margin * 2;
    }
    
    /**
     * Set the type of the chart.
     * 
     * @param type $type 
     */
    public function set_type($type){
        $type = strtolower($type);
        $types = array('bars', 'lines', 'pie');
        if(in_array($type, $types)){
            $this->type = $type;
        }
    }
    
    public function set_style($options){
        if(!is_array($array)){
            return;
        }
        $this->initialize($options);
    }
    
    public function set_title($title){
        $this->title = $title;
    }
    
    public function set_data($array, $label = null, $color = null){
        if(!is_array($array)){
            return;
        }
        $newarray = array();
        foreach($array as $value){
            if(is_array($value)){
                $this->data[] = $value;
            }
            else{
                $newarray[] = $value;
            }
        }
        if(!empty($newarray)){
            $this->data[] = $newarray;
        }
        if(!is_null($label)){
            $this->set_labels($label);
        }
        
        if(!is_null($color)){
            $this->set_color($color);
        }
    }
    
    public function set_labels($array){
        if(!is_array($array)){
            $this->labels[] = $array;
        }
        $this->labels = $array;
    }
        
    public function set_color($color){
        if(!empty($color)){
            $this->graph_color[] = $color;
        }
    }
    
    /**
     * Display or save the chart in a file.
     * 
     * @param string $filename
     * @param string $format
     * @param integer $quality 
     */
    public function draw($filename = null, $format = 'png', $quality = 75){
        
        $this->image->create($this->width, $this->height);
        //FIX
        //$this->graph_color = $this->image->color($this->image->hex2rgb($this->graph_color));
        $this->background_color = $this->image->color($this->image->hex2rgb($this->background_color));
        $this->border_color = $this->image->color($this->image->hex2rgb($this->border_color));
        $this->grid_color = $this->image->color($this->image->hex2rgb($this->grid_color));
        
        $this->text_color = $this->image->color($this->image->hex2rgb($this->text_color));

        // Set background color and border.
        $this->image->rectangle(0, 0, $this->width-1, $this->height-1, $this->border_color, $this->background_color);
        
        // Find the max value.
        $this->max = $this->max($this->data);
        
        // Calculate the ratio.
        $this->ratio = $this->grid_height / $this->max;
        
        // Total pieces in array.
        $this->pieces = count($this->data[0]);       
                
        $this->draw_legend();        
        
        switch ($this->type) {
            case 'bars':
                $this->draw_columns();
                break;
                
            case 'lines':
                $this->draw_lines();                
                break;
        }
        
        $this->draw_remark();
                
        $this->image->draw();
    }
    
    private function draw_grid(){
        $color = $this->grid_color;
        $lines = $this->max / $this->steps;
        $margin = $this->grid_height / $lines;
        for ($i = 0; $i <= $lines; $i++) {
            $y = $this->height - $this->margin - $this->leyend_height - $margin * $i;
            
            // Set offset if middle steps are enabled.
            $offset = 0;
            if($this->middle_steps === true){
                $offset = 5;
            }
            
            // Horizontal grid line.
            $this->image->line($this->margin-$offset, $y, $this->width - $this->margin, $y, $color);

            // Add horizontal legend.
            $v = $margin * $i / $this->ratio;
            $this->image->text($v, 0, $this->text_color, 5, $y - 5);
        }
        
        // Draw middle steps.
        if($this->middle_steps === true){
            for ($i = 0; $i <= $lines*2; $i++) {
                $y = $this->height - $this->margin - $this->leyend_height - ($margin/2) * $i ;
                if($i != 0){
                    $this->image->line($this->margin-3, $y, $this->margin, $y, $color);
                }
            }
        }
        
        // Draw vertical lines.
        if($this->draw_vertical === true){
            for ($i = 0; $i <= $this->pieces; $i++) { 
                $this->image->line($this->margin + $this->bar_width * $i, $this->height - $this->margin, $this->margin + $this->bar_width * $i, $this->margin, $color);
            }
        }
    }
    
    private function draw_legend(){
        $x = $this->margin;
        $y = $this->height - $this->margin;
        
        $legend_padding = 5;
        
        $width = 0;
        foreach ($this->labels as $label) { //compute width
            if($width < strlen($label)){
                $width = strlen($label);
            }
        }

        $fontsize = 0;
        
        // Calculate font sizes.
        $width = $width * imagefontwidth($fontsize);
        $height = imagefontheight($fontsize);
        
        $this->leyend_height = $height + $legend_padding * 2;
        $this->grid_height -= $this->leyend_height;
        
        // Recalculate the ratio.
        $this->ratio = $this->grid_height / $this->max;
        
        $this->image->rectangle($x, $y - $legend_padding, $this->width - $this->margin, $y + $height + $legend_padding, $this->border_color);
                
        foreach($this->labels as $key => $label){
            $x = $x + $width + 15;
            if($key == 0){
                $x = $this->margin + $legend_padding * 2;
            }
            
            if(isset($this->graph_color[$key])){
                $graph_color = $this->image->color($this->image->hex2rgb($this->graph_color[$key]));
                $this->image->text($label, $fontsize, $graph_color, $x, $y);
                $this->image->line($x, $y+$height, $x+$width, $y+$height, $graph_color);
            }
            
        }
    }
    
    private function draw_columns() {
        $this->bar_width = $this->grid_width / ($this->pieces * count($this->data)) - 10;

        // Draw grid lines.
        $this->draw_grid();

        $margin = ($this->grid_width - ($this->pieces * count($this->data)) * $this->bar_width ) / (($this->pieces * count($this->data)) + 1);

        // Draw the bars.
        $offset = 0;
        for ($i = 0; $i < $this->pieces; $i++) {
            
            // Draw the groups
            foreach ($this->data as $key => $data) {
                $value = $data[$i];
                
                $x1 = $this->margin + $margin + $offset * ($margin + $this->bar_width);
                $x2 = $x1 + $this->bar_width;

                $y1 = $this->margin + $this->grid_height - intval($value * $this->ratio);
                $y2 = $this->height - $this->margin - $this->leyend_height;

                // Set the color for each bar.
                $graph_color = $this->image->color($this->image->hex2rgb('#004080'));
                if (!empty($this->graph_color) && isset($this->graph_color[$key])) {
                    $graph_color = $this->image->color($this->image->hex2rgb($this->graph_color[$key]));
                }

                // Add values over rows.
                $this->image->text($value, 0, $this->text_color, $x1 + 3, $y1 - 10);

                // Add the bar.
                $this->image->rectangle($x1, $y1, $x2, $y2, $graph_color, $graph_color);
                $offset++;
            }
            
        }
        

    }
    
    private function draw_lines() {
        $this->image->antialiasing(true);
        $this->pieces = $this->pieces - 1;
        $this->bar_width = $this->grid_width / $this->pieces;

        // Draw grid lines.
        $this->draw_grid();

        $margin = ($this->grid_width - $this->pieces * $this->bar_width ) / ($this->pieces + 1);

        $dots = array();
        // Draw the lines.
        foreach ($this->data as $key => $data) {
            
            $graph_color = $this->image->color($this->image->hex2rgb('#004080'));
            if(isset($this->graph_color[$key])){
                $graph_color = $this->image->color($this->image->hex2rgb($this->graph_color[$key]));
            }
            
            for ($i = 0; $i < $this->pieces; $i++) {

                //$x1 = $this->margin + $margin + $i * ($bar_width);
                $x1 = $this->margin + $i * ($this->bar_width);
                $x2 = $x1 + $this->bar_width;

                $y1 = $this->margin + $this->grid_height - intval($data[$i] * $this->ratio) ;
                $y2 = $this->margin + $this->grid_height - intval($data[$i + 1] * $this->ratio) ;

                // Store dots.
                $dots[] = array($x1, $y1, $graph_color);
                if ($i == $this->pieces - 1) {
                    $x2 = $x1 + $this->bar_width + $margin;
                    $dots[] = array($x2, $y2, $graph_color);
                }

                // Line.
                $this->image->line($x1, $y1, $x2, $y2, $graph_color);

                $this->image->text($data[$i], 0, $this->text_color, $x1 - 5, $y1 - 10);
                $this->image->text($data[$i + 1], 0, $this->text_color, $x2 - 5, $y2 - 10);
            }
        }

        // Add the dots at the end. This way they're over the lines.
        for ($i = 0; $i < count($dots); $i++) {
                        
            $dot_color = $this->image->color($this->image->hex2rgb('#004080'));
            if (isset($dots[$i][2])) {
                $dot_color = $dots[$i][2];
            }

            $this->image->circle(5, $dots[$i][0], $dots[$i][1], $dot_color, $dot_color);
        }
    }
    
    private function draw_pie(){
        
    }
    
    private function draw_remark() {
        $y = $this->grid_height + $this->margin;
        // Horizontal
        $this->image->line($this->margin, $y, $this->width - $this->margin, $y, $this->border_color);

        // Vertical
        $this->image->line($this->margin, $y, $this->margin, $this->margin, $this->border_color);
    }
    
    function max($array) {
        foreach ($array as $value) {
            if (is_array($value)) {
                $value = $this->max($value);
            }
            if (!(isset($max))) {
                $max = $value;
            } else {
                $max = $value > $max ? $value : $max;
            }
        }
        return $max;
    }

        
}
// END Chart Class

/* End of file Chart.php */
/* Location: Chart.php */