<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle's Clean theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package    theme
 * @subpackage UCSF
 * @author     Lambda Soulutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class theme_ucsf_add_category_customization extends admin_setting_configselect {

    public function write_setting ($data) {
        
        $categories = get_config('theme_ucsf');            

        if(!empty($categories->all_categories)) 
            set_config('all_categories', $categories->all_categories . ',' . $data, 'theme_ucsf');
        else 
            set_config('all_categories', $data, 'theme_ucsf');

        return parent::write_setting(0);

    }
}

class theme_ucsf_remove_category_customization extends admin_setting_configselect {

    public function write_setting ($data) {

    	$categories = get_config('theme_ucsf');            

        if(!empty($categories->all_categories)) {
            $temp_array = explode(",", $categories->all_categories);
            if(in_array($data, $temp_array)) {

                $arr = array_diff($temp_array, array($data));
                $fin = implode(",", $arr);

                set_config('all_categories', $fin, 'theme_ucsf');
            }
        } 

        return parent::write_setting(0);
    }
}


/**
 * YEAR, MONTH, DATE, HOUR AND MINUTE selector
 *
 * Class that stores selected year, month, date, hour and minute
 * in a database.
 *
 * @author Sasa Prsir <sasa.prsir@lambdasolutions.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configdatetime extends admin_setting {
    /** @var string Used for setting year, month, date, out and minut select. */
    public $name2;
    public $name3;
    public $name4;
    public $name5;
    public $name6;

    /**
     * Constructor
     * @param string $name setting for component name
     * @param string $year setting for year
     * @param string $month setting for month
     * @param string $date setting for date
     * @param string $hour setting for hour
     * @param string $minute setting for minute
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing null
     */
    public function __construct($name, $year, $month, $date, $hour, $minute, $visiblename, $description, $defaultsetting) {
        $this->name2 = $year;
        $this->name3 = $month;
        $this->name4 = $date;
        $this->name5 = $hour;
        $this->name6 = $minute;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get the selected yera, month, date, hour and minute.
     *
     * @return mixed An array containing 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx, or null if not set
     */
    public function get_setting() {
        $result2 = $this->config_read($this->name2);
        $result3 = $this->config_read($this->name3);
        $result4 = $this->config_read($this->name4);
        $result5 = $this->config_read($this->name5);
        $result6 = $this->config_read($this->name6);
        if (
                is_null($result2) 
                or is_null($result3) 
                or is_null($result4)
                or is_null($result5)
                or is_null($result6)) {
            return NULL;
        }

        return array(
            'year'      => $result2, 
            'month'     => $result3,
            'date'      => $result4,
            'hour'      => $result5,
            'minute'    => $result6
        );
    }
    
    /**
     * Store the time (Year, month, date, hour and minute)
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {
        
        if (!is_array($data)) {
            return '';
        }

        $result = $this->config_write($this->name2, (int)$data['year']) 
                && $this->config_write($this->name3, (int)$data['month'])
                && $this->config_write($this->name4, (int)$data['date'])
                && $this->config_write($this->name5, (int)$data['hour'])
                && $this->config_write($this->name6, (int)$data['minute']);
        return ($result ? '' : get_string('errorsetting', 'admin'));
        
    }

    /**
     * Returns XHTML time select fields
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @param string $query
     * @return string XHTML time select fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        // Start form for date input.
        $return  = '<div class="form-time defaultsnext">'; 
        //$return .= '<p>Date: <input type="text" id="datepicker"></p>';   
        
        // Start output for year select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'year">' . get_string('year') . '</label>';
        $return .= 'Year: <select id="' . $this->get_id() . 'year" name="' . $this->get_full_name() . '[year]">';
        $i = date('Y');
        $return .= '<option value="' . $i . '"' . ($i == $data['year'] ? ' selected="selected"' : '') . '>' . $i . '</option>';
        $j = date('Y', strtotime('+1 year'));
        $return .= '<option value="' . $j . '"' . ($j == $data['year'] ? ' selected="selected"' : '') . '>' . $j . '</option>';
        $k = date('Y', strtotime('+2 years'));
        $return .= '<option value="' . $k . '"' . ($k == $data['year'] ? ' selected="selected"' : '') . '>' . $k . '</option>';
        $return .= '</select>';
        
        // Start output for month select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'month">' . get_string('month') . '</label>';
        $return .= ' Month: <select id="' . $this->get_id() . 'month" name="' . $this->get_full_name() . '[month]">';
        for ($m = 1; $m <= 12; $m++) {
            $return .= '<option value="' . $m . '"' . ($m == $data['month'] ? ' selected="selected"' : '') . '>' . $m . '</option>';
        }
        $return .= '</select>';
        
        // Start output for date select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'date">' . get_string('date') . '</label>';
        $return .= ' Date: <select id="' . $this->get_id() . 'date" name="' . $this->get_full_name() . '[date]">';
        for ($i = 1; $i <= 31; $i++) { 
            $return .= '<option value="' . $i . '"' . ($i == $data['date'] ? ' selected="selected"' : '') . '>' . $i . '</option>';
        }
        $return .= '</select>';
        
        // Start output for hour select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'hour">' . get_string('hours') . '</label>';
        $return .= ' Hour: <select id="' . $this->get_id() . 'hour" name="' . $this->get_full_name() . '[hour]">';
        for ($i = 1; $i <= 24; $i++) {
            $return .= '<option value="' . $i . '"' . ($i == $data['hour'] ? ' selected="selected"' : '') . '>' . $i . '</option>';
        } 
        $return .= '</select>';
        
        // Start output for minute select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'minute">' . get_string('minutes') . '</label>';
        $return .= ' Minute: <select id="' . $this->get_id() . 'minute" name="' . $this->get_full_name() . '[minute]">';
        for ($i = 0; $i < 60; $i += 5) {
            $return .= '<option value="' . $i . '"' . ($i == $data['minute'] ? ' selected="selected"' : '') . '>' . $i . '</option>';
        }
        $return .= '</select>';
        $return .= '</div>';
        
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $default, $query);
    }

}


/**
 * YEAR, MONTH, DATE, HOUR AND MINUTE selector
 *
 * Class that stores selected year, month, date, hour and minute
 * in a database.
 *
 * @author Sasa Prsir <sasa.prsir@lambdasolutions.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class theme_ucsf_admin_setting_configonlytime extends admin_setting {
    /** @var string Used for setting year, month, date, out and minut select. */
    public $name5;
    public $name6;

    /**
     * Constructor
     * @param string $name setting for component name
     * @param string $year setting for year
     * @param string $month setting for month
     * @param string $date setting for date
     * @param string $hour setting for hour
     * @param string $minute setting for minute
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing null
     */
    public function __construct($name, $hour, $minute, $visiblename, $description, $defaultsetting) {
        $this->name5 = $hour;
        $this->name6 = $minute;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get the selected yera, month, date, hour and minute.
     *
     * @return mixed An array containing 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx, or null if not set
     */
    public function get_setting() {
        $result5 = $this->config_read($this->name5);
        $result6 = $this->config_read($this->name6);
        if (
                is_null($result5) 
                or is_null($result6)) {
            return NULL;
        }
        
        return array(
            'only_hour'      => $result5,
            'only_minute'    => $result6
        );
        
    }
    
    /**
     * Store the time (Year, month, date, hour and minute)
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {
        
        if (!is_array($data)) {
            return '';
        }

        $result = $this->config_write($this->name5, (int)$data['only_hour']) 
                && $this->config_write($this->name6, (int)$data['only_minute']);
        return ($result ? '' : get_string('errorsetting', 'admin'));
        
    }

    /**
     * Returns XHTML time select fields
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @param string $query
     * @return string XHTML time select fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        // Start form for date input.
        $return  = '<div class="form-time defaultsnext">'; 
        
        // Start output for hour select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'only_hour">' . get_string('only_hour', 'theme_ucsf') . '</label>';
        $return .= ' Hour: <select id="' . $this->get_id() . 'only_hour" name="' . $this->get_full_name() . '[only_hour]">';
        for ($i = 0; $i <= 23; $i++) {
            if ($i < 10) {
                $writeHour = "0".$i;
            }
            if ($i >= 10) {
                $writeHour = "".$i;
            }
            $return .= '<option value="' . $i . '"' . ($i == $data['only_hour'] ? ' selected="selected"' : '') . '>' . $writeHour . '</option>';
        } 
        $return .= '</select>';
        
        // Start output for minute select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'only_minute">' . get_string('only_minute', 'theme_ucsf') . '</label>';
        $return .= ' Minute: <select id="' . $this->get_id() . 'only_minute" name="' . $this->get_full_name() . '[only_minute]">';
        for ($i = 0; $i < 60; $i += 5) {
            if ($i < 10) {
                $writeminute = "0".$i;
            }
            if ($i >= 10) {
                $writeminute = "".$i;
            }
            $return .= '<option value="' . $i . '"' . ($i == $data['only_minute'] ? ' selected="selected"' : '') . '>' . $writeminute . '</option>';
        }
        $return .= '</select>';
        $return .= '</div>';
        
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $default, $query);
    }

}



/**
 * YEAR, MONTH, DATE, HOUR AND MINUTE jquery selector
 *
 * Class that stores selected year, month, date, hour and minute
 * in a database.
 *
 * @author Sasa Prsir <sasa.prsir@lambdasolutions.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_ucsf_datepicker extends admin_setting {
    /** @var string Used for setting year, month, date, out and minut select. */
    public $name4;
    public $name5;
    public $name6;

    /**
     * Constructor
     * @param string $name setting for component name
     * @param string $year setting for year
     * @param string $month setting for month
     * @param string $date setting for date
     * @param string $hour setting for hour
     * @param string $minute setting for minute
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing null
     */
    public function __construct($name, $datepicker, $hour,$minute,$visiblename, $description, $defaultsetting) {
        $this->name4 = $datepicker;
        $this->name5 = $hour;
        $this->name6 = $minute;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get the selected yera, month, date, hour and minute.
     *
     * @return mixed An array containing 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx, or null if not set
     */
    public function get_setting() {
        $result4 = $this->config_read($this->name4);
        $result5 = $this->config_read($this->name5);
        $result6 = $this->config_read($this->name6);
        
         if (is_null($result4) 
                or is_null($result5)
                or is_null($result6)) {
            return NULL;
        }
        

        return array(
            'datepicker'      => $result4,
            'only_hour'      => $result5,
            'only_minute'    => $result6
        );
        
    }
    
    /**
     * Store the time (Year, month, date, hour and minute)
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {
        
        $result = $this->config_write($this->name4, $data['datepicker'])
                && $this->config_write($this->name5, $data['only_hour'])
                && $this->config_write($this->name6, $data['only_minute']);
        return ($result ? '' : get_string('errorsetting', 'admin'));
        
        
    }

    /**
     * Returns XHTML time select fields
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @param string $query
     * @return string XHTML time select fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        
        $default = $this->get_defaultsetting();
        
        // Start form for date input.
        $return  = '<div class="form-time defaultsnext">'; 
        
        // Datepicker.
        $return .= '<input required type="text" id="' . $this->get_id() . 'datepicker" name="' . $this->get_full_name() . '[datepicker]" class="datepicker" value="'.s( $data['datepicker']).'">';          // Start output for hour select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'only_hour">' . get_string('only_hour', 'theme_ucsf') . '</label>';
        $return .= ' Hour: <select id="' . $this->get_id() . 'only_hour" name="' . $this->get_full_name() . '[only_hour]">';
        for ($i = 0; $i <= 23; $i++) {
            if ($i < 10) {
                $writeHour = "0".$i;
            }
            if ($i >= 10) {
                $writeHour = "".$i;
            }
            $return .= '<option value="' . $i . '"' . ($i == $data['only_hour'] ? ' selected="selected"' : '') . '>' . $writeHour . '</option>';
        } 
        $return .= '</select>';
        
        // Start output for minute select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'only_minute">' . get_string('only_minute', 'theme_ucsf') . '</label>';
        $return .= ' Minute: <select id="' . $this->get_id() . 'only_minute" name="' . $this->get_full_name() . '[only_minute]">';
        for ($i = 0; $i < 60; $i += 5) { 
            if ($i < 10) {
                $writeminute = "0".$i;
            }
            if ($i >= 10) {
                $writeminute = "".$i;
            }
            $return .= '<option value="' . $i . '"' . ($i == $data['only_minute'] ? ' selected="selected"' : '') . '>' . $writeminute . '</option>';
        }
        $return .= '</select>';
        
        
        $return .= '</div>';
        
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $default, $query);
    }

}



/**
 * YEAR, MONTH, DATE, HOUR AND MINUTE jquery selector
 *
 * Class that stores selected year, month, date, hour and minute
 * in a database.
 *
 * @author Sasa Prsir <sasa.prsir@lambdasolutions.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_ucsf_jquery_datepicker extends admin_setting {
    /** @var string Used for setting year, month, date, out and minut select. */
    public $name4;

    /**
     * Constructor
     * @param string $name setting for component name
     * @param string $year setting for year
     * @param string $month setting for month
     * @param string $date setting for date
     * @param string $hour setting for hour
     * @param string $minute setting for minute
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing null
     */
    public function __construct($name, $datepicker, $visiblename, $description, $defaultsetting) {
        $this->name4 = $datepicker;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get the selected yera, month, date, hour and minute.
     *
     * @return mixed An array containing 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx, or null if not set
     */
    public function get_setting() {
        $result = $this->config_read($this->name4);
        
        if (is_null($result)) {
            return NULL;
         }

        return $result;
        
    }
    
    /**
     * Store the time (Year, month, date, hour and minute)
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {
        
        $result = $this->config_write($this->name4, $data['datepicker']);
        return ($result ? '' : get_string('errorsetting', 'admin'));
        
        
    }

    /**
     * Returns XHTML time select fields
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @param string $query
     * @return string XHTML time select fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        $default = $this->get_defaultsetting();

        // Start form for date input.
        $return  = '<div class="form-time defaultsnext">'; 
        
        // Datepicker.
        $return .= '<input required type="text" id="' . $this->get_id() . 'datepicker" name="' . $this->get_full_name() . '[datepicker]" class="datepicker" value="'.s($data).'">';   
        
        
        $return .= '</div>';
        
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $default, $query);
    }

}