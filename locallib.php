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
 * @package   theme_ucsf
 * @copyright 2018 The Regents of the University of California
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_ucsf_add_category_customization extends admin_setting_configselect
{

    /**
     * @inheritdoc
     */
    public function write_setting($data)
    {

        $categories = get_config('theme_ucsf');

        if ($data != 0) {
            set_config('displaycoursetitle'.$data, 1, 'theme_ucsf');
        }

        if ( ! empty($categories->all_categories)) {
            set_config('all_categories', $categories->all_categories.','.$data, 'theme_ucsf');
        } else {
            set_config('all_categories', $data, 'theme_ucsf');
        }

        return parent::write_setting(0);

    }
}

class theme_ucsf_remove_category_customization extends admin_setting_configselect
{

    /**
     * @inheritdoc
     */
    public function write_setting($data)
    {

        $categories = get_config('theme_ucsf');

        if ( ! empty($categories->all_categories)) {
            $temp_array = explode(",", $categories->all_categories);
            if (in_array($data, $temp_array)) {

                $arr = array_diff($temp_array, array($data));
                $fin = implode(",", $arr);

                set_config('all_categories', $fin, 'theme_ucsf');
            }
        }

        return parent::write_setting(0);
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
    public $start_date;
    public $end_date;

    /**
     * Constructor
     * @param string $name setting for component name
     * @param string $datepicker
     * @param string $end_datepicker
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing null
     */
    public function __construct($name, $datepicker, $end_datepicker, $visiblename, $description, $defaultsetting) {
        $this->start_date = $datepicker;
        $this->end_date = $end_datepicker;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get the selected year, month, date, hour and minute.
     *
     * @return mixed An array containing 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx, or null if not set
     */
    public function get_setting() {
        $start_date = $this->config_read($this->start_date);
        $end_date   = $this->config_read($this->end_date);

        if (is_null($start_date)
                or is_null($end_date)) {
            return NULL;
        }

        return array(
                'datepicker'          => $start_date,
                'end_datepicker'      => $end_date
        );

    }

    /**
     * Store the time (Year, month, date, hour and minute)
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {

        $validate = $this->validate($data);
        if ($validate === true){
            $result = $this->config_write($this->start_date, isset($data['datepicker']) ? $data['datepicker'] : '')
                    && $this->config_write($this->end_date, isset($data['end_datepicker']) ? $data['end_datepicker'] : '');
            return ($result ? '' : get_string('errorsetting', 'admin'));
        } else {
            if (is_null($data['datepicker']) || is_null($data['end_datepicker']) || $data['datepicker'] == "" || $data['end_datepicker'] ==""){
                return (get_string('emptyDateFieldError', 'theme_ucsf'));
            } else {
                return (get_string('oneTimeStartEndDateError', 'theme_ucsf'));
            }
        }

    }

    /**
     * Validate data before storage
     * @param string $data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        $time_start = strtotime($data['datepicker']);
        $time_end = strtotime($data['end_datepicker']);

        if($data['datepicker'] == null || $data['datepicker'] == ''){
            return false;
        } elseif ($time_start > $time_end) {
            return false;
        } else {
            return true;
        }
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
        $return  = '<div class="form-text defaultsnext">';
        // Datepicker.
        $return .= '<input type="text" id="' . $this->get_id() . 'datepicker" name="' . $this->get_full_name() . '[datepicker]" class="form-control text-ltr datepicker" value="'.s( $data['datepicker']).'" size="15"><br />';

        // Datepicker.
        $return .= '<input type="text" id="' . $this->get_id() . 'end_datepicker" name="' . $this->get_full_name() . '[end_datepicker]" class="form-control text-ltr datepicker" value="'.s( $data['end_datepicker']).'" size="15">';

        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $default, $query);
    }

}

/**
 * HOUR AND MINUTE jquery selector
 *
 * Class that stores selected year, month, date, hour and minute
 * in a database.
 *
 * @author Dusan Sparavalo <dusan.sparavalo@lambdasolutions.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class theme_ucsf_datepicker_time extends admin_setting {
    public $start_minute;
    public $start_hour;
    public $end_minute;
    public $end_hour;

    /**
     * Constructor
     * @param string $name setting for component name
     * @param string $hour
     * @param string $minute
     * @param string $end_hour
     * @param string $end_minute
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing null
     */
    public function __construct($name, $hour, $minute, $end_hour, $end_minute, $visiblename, $description, $defaultsetting) {
        $this->start_hour = $hour;
        $this->start_minute = $minute;

        $this->end_hour = $end_hour;
        $this->end_minute = $end_minute;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get the selected yera, month, date, hour and minute.
     *
     * @return mixed An array containing 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx, or null if not set
     */
    public function get_setting() {
        $start_hour = $this->config_read($this->start_hour);
        $start_minute = $this->config_read($this->start_minute);

        $end_hour = $this->config_read($this->end_hour);
        $end_minute = $this->config_read($this->end_minute);

        if (is_null($start_hour)
                or is_null($start_minute)
                or is_null($end_hour)
                or is_null($end_minute)) {
            return NULL;
        }

        return array(
                'start_hour'    => $start_hour,
                'start_minute'  => $start_minute,
                'end_hour'      => $end_hour,
                'end_minute'    => $end_minute
        );
    }

    /**
     * Store the time (Year, month, date, hour and minute)
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {

        $validate = $this->validate($data);
        if ($validate === true) {
            $result = $this->config_write($this->start_hour, isset($data['start_hour']) ? $data['start_hour'] : '')
                    && $this->config_write($this->start_minute, isset($data['start_minute']) ? $data['start_minute'] : '')
                    && $this->config_write($this->end_hour, isset($data['end_hour']) ? $data['end_hour'] : '')
                    && $this->config_write($this->end_minute, isset($data['end_minute']) ? $data['end_minute'] : '');
            return ($result ? '' : get_string('errorsetting', 'admin'));
        } else {
            return (get_string('oneTimeStartEndTimeError', 'theme_ucsf'));
        }

    }

    /**
     * Validate data before storage
     * @param string $data
     * @return mixed true if ok and false if error found
     */
    public function validate($data) {
        $time_start = $data['start_hour'] * 3600 + $data['start_minute'] * 60;
        $time_end = $data['end_hour'] * 3600 + $data['end_minute'] * 60;

        if($time_start < $time_end) {
            return true;
        } else {
            return false;
        }
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

        $return = '<div class="form-select defaultsnext">';

        // Start output for hour select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'start_hour">' . get_string('start_hour', 'theme_ucsf') . '</label>';
        $return .= ' Hour: <select id="' . $this->get_id() . 'start_hour" name="' . $this->get_full_name() . '[start_hour]" class="custom-select">';
        for ($i = 0; $i <= 23; $i++) {
            $writeHour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $return .= '<option value="' . $i . '" ' . ($i == $data['start_hour'] ? ' selected="selected"' : '') . '>' . $writeHour . '</option>';
        }
        $return .= '</select>';

        // Start output for minute select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'start_minute">' . get_string('start_minute', 'theme_ucsf') . '</label>';
        $return .= ' Minute: <select id="' . $this->get_id() . 'start_minute" name="' . $this->get_full_name() . '[start_minute]" class="custom-select">';
        for ($i = 0; $i < 60; $i += 5) {
            $writeminute = str_pad($i, 2, '0', STR_PAD_LEFT);
            $return .= '<option value="' . $i . '" ' . ($i == $data['start_minute'] ? ' selected="selected"' : '') . '>' . $writeminute . '</option>';
        }
        $return .= '</select>';
        $return .= '</br>';

        // End output for hour select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'end_hour">' . get_string('end_hour', 'theme_ucsf') . '</label>';
        $return .= ' Hour: <select id="' . $this->get_id() . 'end_hour" name="' . $this->get_full_name() . '[end_hour]" class="custom-select">';
        for ($i = 0; $i <= 23; $i++) {
            $writeHour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $return .= '<option value="' . $i . '" ' . ($i == $data['end_hour'] ? ' selected="selected"' : '') . '>' . $writeHour . '</option>';
        }
        $return .= '</select>';

        // End output for minute select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'end_minute">' . get_string('end_minute', 'theme_ucsf') . '</label>';
        $return .= ' Minute: <select id="' . $this->get_id() . 'end_minute" name="' . $this->get_full_name() . '[end_minute]" class="custom-select">';
        for ($i = 0; $i < 60; $i += 5) {
            $writeminute = str_pad($i, 2, '0', STR_PAD_LEFT);
            $return .= '<option value="' . $i . '" ' . ($i == $data['end_minute'] ? ' selected="selected"' : '') . '>' . $writeminute . '</option>';
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
class theme_ucsf_datepicker_with_validation extends admin_setting {
    /** @var string Used for setting year, month, date, out and minut select. */
    public $start_date;
    public $start_hour;
    public $start_minute;
    public $end_date;
    public $end_hour;
    public $end_minute;

    /**
     * Constructor
     * @param string $name setting for component name
     * @param string $datepicker
     * @param string $hour setting for hour
     * @param string $minute setting for minute
     * @param string $end_datepicker
     * @param string $end_hour
     * @param string $end_minute
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing null
     */
    public function __construct($name, $datepicker, $hour, $minute, $end_datepicker,  $end_hour, $end_minute, $visiblename,$description, $defaultsetting) {
        $this->start_date = $datepicker;
        $this->start_hour = $hour;
        $this->start_minute = $minute;


        $this->end_date = $end_datepicker;
        $this->end_hour = $end_hour;
        $this->end_minute = $end_minute;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get the selected yera, month, date, hour and minute.
     *
     * @return mixed An array containing 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx, or null if not set
     */
    public function get_setting() {

        $start_date = $this->config_read($this->start_date);
        $start_hour = $this->config_read($this->start_hour);
        $start_minute = $this->config_read($this->start_minute);


        $end_date = $this->config_read($this->end_date);
        $end_hour = $this->config_read($this->end_hour);
        $end_minute = $this->config_read($this->end_minute);

        if (is_null($start_date)
                or is_null($start_hour)
                or is_null($start_minute)
                or is_null($end_date)
                or is_null($end_hour)
                or is_null($end_minute)) {
            return NULL;
        }

        return array(
                'datepicker'            => $start_date,
                'start_hour'             => $start_hour,
                'start_minute'           => $start_minute,
                'end_datepicker'        => $end_date,
                'end_hour'              => $end_hour,
                'end_minute'            => $end_minute
        );

    }

    /**
     * Store the time (Year, month, date, hour and minute)
     *
     * @param array $data Must be form 'year'=>xxxx, 'month'=>xx, 'date'=>xx, 'hour'=>xx, 'minute'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {

        $validate = $this->validate($data);
        if ($validate === true){
            $result = $this->config_write($this->start_date,  isset($data['datepicker']) ? $data['datepicker'] : '')
                    && $this->config_write($this->start_hour, isset($data['start_hour']) ? $data['start_hour'] : '')
                    && $this->config_write($this->start_minute, isset($data['start_minute']) ? $data['start_minute'] : '')
                    && $this->config_write($this->end_date, isset($data['end_datepicker']) ? $data['end_datepicker'] : '')
                    && $this->config_write($this->end_hour, isset($data['end_hour']) ? $data['end_hour'] : '')
                    && $this->config_write($this->end_minute, isset($data['end_minute']) ? $data['end_minute'] : '');

            return ($result ? '' : get_string('errorsetting', 'admin'));
        } else {
            if (is_null($data['datepicker']) || is_null($data['end_datepicker']) || $data['datepicker'] == "" || $data['end_datepicker'] ==""){
                return (get_string('emptyDateFieldError', 'theme_ucsf'));
            } else {
                return (get_string('oneTimeStartEndDateError', 'theme_ucsf'));
            }
        }

    }

    /**
     * Validate data before storage
     * @param string $data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {

        $time_start = strtotime($data['datepicker']) + $data['start_hour'] * 3600 + $data['start_minute'] * 60;
        $time_end = strtotime($data['end_datepicker']) + $data['end_hour']* 3600 + $data['end_minute'] * 60;

        if($data['datepicker'] == null || $data['datepicker'] == ''){
            return false;
        } elseif ($time_start > $time_end) {
            return false;
        } else {
            return true;
        }
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
        $return  = '<div class="form-select defaultsnext">';
        // Datepicker.
        $return .= '<input type="text" id="' . $this->get_id() . 'datepicker" name="' . $this->get_full_name() . '[datepicker]" class="form-control text-ltr datepicker" value="'.s( $data['datepicker']).'" size="15">';
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'start_hour">' . get_string('start_hour', 'theme_ucsf') . '</label>';
        $return .= ' Hour: <select id="' . $this->get_id() . 'start_hour" name="' . $this->get_full_name() . '[start_hour]" class="custom-select">';
        for ($i = 0; $i <= 23; $i++) {
            $writeHour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $return .= '<option value="' . $i . '" ' . ($i == $data['start_hour'] ? ' selected="selected"' : '') . '>' . $writeHour . '</option>';
        }
        $return .= '</select>';

        // Start output for minute select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'start_minute">' . get_string('start_minute', 'theme_ucsf') . '</label>';
        $return .= ' Minute: <select id="' . $this->get_id() . 'start_minute" name="' . $this->get_full_name() . '[start_minute]" class="custom-select">';
        for ($i = 0; $i < 60; $i += 5) {
            $writeminute = str_pad($i, 2, '0', STR_PAD_LEFT);
            $return .= '<option value="' . $i . '" ' . ($i == $data['start_minute'] ? ' selected="selected"' : '') . ' >' . $writeminute . '</option>';
        }
        $return .= '</select>';
        $return .= '<br />';


        // Datepicker.
        $return .= '<input type="text" id="' . $this->get_id() . 'end_datepicker" name="' . $this->get_full_name() . '[end_datepicker]" class="form-control text-ltr datepicker" value="'.s( $data['end_datepicker']).'" size="15">';
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'end_hour">' . get_string('end_hour', 'theme_ucsf') . '</label>';
        $return .= ' Hour: <select id="' . $this->get_id() . 'end_hour" name="' . $this->get_full_name() . '[end_hour]" class="custom-select">';
        for ($i = 0; $i <= 23; $i++) {
            $writeHour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $return .= '<option value="' . $i . '" ' . ($i == $data['end_hour'] ? ' selected="selected"' : '') . '>' . $writeHour . '</option>';
        }
        $return .= '</select>';

        // Start output for minute select box.
        $return .= '<label class="accesshide" for="' . $this->get_id() . 'end_minute">' . get_string('end_minute', 'theme_ucsf') . '</label>';
        $return .= ' Minute: <select id="' . $this->get_id() . 'end_minute" name="' . $this->get_full_name() . '[end_minute]" class="custom-select">';
        for ($i = 0; $i < 60; $i += 5) {
            $writeminute = str_pad($i, 2, '0', STR_PAD_LEFT);
            $return .= '<option value="' . $i . '" ' . ($i == $data['end_minute'] ? ' selected="selected"' : '') . '>' . $writeminute . '</option>';
        }
        $return .= '</select>';


        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $default, $query);
    }

}
