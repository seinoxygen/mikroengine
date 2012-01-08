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
 * Arr Class
 *
 * @package		Mikroengine
 * @subpackage          Libraries
 * @category            Calendar
 * @author		Mikrobytes Dev Team
 */
// ------------------------------------------------------------------------

class Calendar {
    
    private $config;
    
    public function __construct(){
        $ME = &get_instance();
        $this->config = $ME->config;
        $this->config->load('calendar');
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
     *
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @param array $links
     * @return string 
     */
    function generate($year = null, $month = null, $day = null, $data = array()) {

        if(is_null($year)){
            $year = date('Y');
        }
        
        if(is_null($month)){
            $month = date('m');
        }
        
        $first_of_month = gmmktime(0, 0, 0, $month, 1, $year);

        $first_day = $this->config->get('calendar_start_day');
        $day_name_length = $this->config->get('calendar_day_length');

        $day_names = array();
        for ($n = 0, $t = (3 + $first_day) * 86400; $n < 7; $n++, $t+=86400) {
            $day_names[$n] = ucfirst(gmstrftime('%A', $t));
        }

        list($month, $year, $month_name, $weekday) = explode(',', gmstrftime('%m,%Y,%B,%w', $first_of_month));
        $weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
        $title = ucfirst($month_name) . '&nbsp;' . $year;

        $calendar = '<table class="calendar">';

        $calendar .= '<tr><td colspan="7" class="calendar-month">' . $title . '</td></tr>';

        $calendar .= '<tr>';
        
        if ($day_name_length > 0) {
            foreach ($day_names as $d) {
                $calendar .= '<th>' . ($day_name_length < 4 ? substr($d, 0, $day_name_length) : $d) . '</th>';
            }
            $calendar .= '</tr>';
        }

        $calendar .= '<tr>';

        if ($weekday > 0) {
            $calendar .= '<td colspan="' . $weekday . '">&nbsp;</td>';
        }
        
        for ($day = 1, $days_in_month = gmdate('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++) {
            if ($weekday == 7) {
                $weekday = 0;
                $calendar .= "</tr><tr>";
            }
            if (array_key_exists($day, $data)) {
                if (is_array($data[$day])) {
                    $data_day = implode('', $data[$day]);
                } else {
                    $data_day = $data[$day];
                }
            } else {
                $data_day = $day;
            }
            $calendar .= "<td>$data_day</td>";
        }
        
        if ($weekday > 7){
            $calendar .= '<td colspan="' . (7 - $weekday) . '">&nbsp;</td>';
        }

        $calendar .= "</tr>";
        
        $calendar .= "</table>";
        
        return $calendar;
    }
    
}
// END Calendar Class

/* End of file Calendar.php */
/* Location: Calendar.php */