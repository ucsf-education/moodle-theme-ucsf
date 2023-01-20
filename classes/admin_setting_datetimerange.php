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

namespace theme_ucsf;

use admin_setting;
use coding_exception;
use html_writer;

/**
 * Admin settings form component for selecting a date-time range (year, month, day, hours, and minutes).
 *
 * @author Sasa Prsir <sasa.prsir@lambdasolutions.net>
 * @author Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_datetimerange extends admin_setting {
    const START_DATE = 'start_date';
    const START_HOUR = 'start_hour';
    const START_MINUTE = 'start_minute';
    const END_DATE = 'end_date';
    const END_HOUR = 'end_hour';
    const END_MINUTE = 'end_minute';

    public string $start_date_setting_name;
    public string $start_minute_setting_name;
    public string $start_hour_setting_name;
    public string $end_date_setting_name;
    public string $end_minute_setting_name;
    public string $end_hour_setting_name;

    /**
     * @param string $name
     * @param string $start_date_setting_name
     * @param string $start_hour_setting_name
     * @param string $start_minute_setting_name
     * @param string $end_date_setting_name
     * @param string $end_hour_setting_name
     * @param string $end_minute_setting_name
     * @param string $visiblename
     * @param string $description
     */
    public function __construct(
            string $name,
            string $start_date_setting_name,
            string $start_hour_setting_name,
            string $start_minute_setting_name,
            string $end_date_setting_name,
            string $end_hour_setting_name,
            string $end_minute_setting_name,
            string $visiblename,
            string $description
    ) {
        $this->start_date_setting_name = $start_date_setting_name;
        $this->start_hour_setting_name = $start_hour_setting_name;
        $this->start_minute_setting_name = $start_minute_setting_name;
        $this->end_date_setting_name = $end_date_setting_name;
        $this->end_hour_setting_name = $end_hour_setting_name;
        $this->end_minute_setting_name = $end_minute_setting_name;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * @return array|null $data
     *  $data = [
     *    'start_date' => (string) the start date
     *    'start_hour'=> (string) the start hour
     *    'start_minute' => (string) the start minute
     *    'end_date' => (string) the end date
     *    'end_hour' => (string) then end minute
     *    'end_minute' => (string) the end minute
     *  ]
     */
    public function get_setting(): ?array {
        $start_date = $this->config_read($this->start_date_setting_name);
        $start_hour = $this->config_read($this->start_hour_setting_name);
        $start_minute = $this->config_read($this->start_minute_setting_name);
        $end_date = $this->config_read($this->end_date_setting_name);
        $end_hour = $this->config_read($this->end_hour_setting_name);
        $end_minute = $this->config_read($this->end_minute_setting_name);
        if (is_null($start_date)
                || is_null($start_hour)
                || is_null($start_minute)
                || is_null($end_date)
                || is_null($end_hour)
                || is_null($end_minute)
        ) {
            return null;
        }
        return array(
                self::START_DATE => $start_date,
                self::START_HOUR => $start_hour,
                self::START_MINUTE => $start_minute,
                self::END_DATE => $end_date,
                self::END_HOUR => $end_hour,
                self::END_MINUTE => $end_minute
        );
    }

    /**
     * @param array $data
     *  $data = [
     *    'start_date' => (string) the start date
     *    'start_hour'=> (string) the start hour
     *    'start_minute' => (string) the start minute
     *    'end_date' => (string) the end date
     *    'end_hour' => (string) then end minute
     *    'end_minute' => (string) the end minute
     *  ]
     * @return string empty string if ok, string error message otherwise
     * @throws coding_exception
     */
    public function write_setting($data): string {
        $data = array_map('trim', $data);
        $start_date = ('' !== $data[self::START_DATE]) ? $data[self::START_DATE] : '';
        $start_hour = ('' !== $data[self::START_HOUR]) ? $data[self::START_HOUR] : '';
        $start_minute = ('' !== $data[self::START_MINUTE]) ? $data[self::START_MINUTE] : '';
        $end_date = ('' !== $data[self::END_DATE]) ? $data[self::END_DATE] : '';
        $end_hour = ('' !== $data[self::END_HOUR]) ? $data[self::END_HOUR] : '';
        $end_minute = ('' !== $data[self::END_MINUTE]) ? $data[self::END_MINUTE] : '';
        $validate = $this->validate($start_date, $start_hour, $start_minute, $end_date, $end_hour, $end_minute);
        if ('' === $validate) {
            $result = $this->config_write($this->start_date_setting_name, $start_date)
                    && $this->config_write($this->start_hour_setting_name, $start_hour)
                    && $this->config_write($this->start_minute_setting_name, $start_minute)
                    && $this->config_write($this->end_date_setting_name, $end_date)
                    && $this->config_write($this->end_hour_setting_name, $end_hour)
                    && $this->config_write($this->end_minute_setting_name, $end_minute);
            return ($result ? '' : get_string('errorsetting', 'admin'));
        }
        return $validate;
    }

    /**
     * Validate data before storage.
     *
     * @param string $start_date
     * @param string $start_hour
     * @param string $start_minute
     * @param string $end_date
     * @param string $end_hour
     * @param string $end_minute
     * @return string empty string if ok, string error message otherwise
     * @throws coding_exception
     */
    protected function validate(
            string $start_date,
            string $start_hour,
            string $start_minute,
            string $end_date,
            string $end_hour,
            string $end_minute
    ): string {
        if ('' === $start_date && '' === $end_date) {
            return get_string('emptystartandenddate', 'theme_ucsf');
        }
        if ('' === $start_date) {
            return get_string('emptystartdate', 'theme_ucsf');
        }
        if ('' === $end_date) {
            return get_string('emptyenddate', 'theme_ucsf');
        }
        $start_date = strtotime($start_date);
        $end_date = strtotime($end_date);
        if (false === $start_date && false === $end_date) {
            return get_string('invalidstartandenddate', 'theme_ucsf');
        }
        if (false === $start_date) {
            return get_string('invalidstartdate', 'theme_ucsf');
        }
        if (false === $end_date) {
            return get_string('invalidenddate', 'theme_ucsf');
        }
        if ($start_date > $end_date) {
            return get_string('startsbeforeitends', 'theme_ucsf');
        }
        $time_start = $start_date + (int) $start_hour * 3600 + (int) $start_minute * 60;
        $time_end = $end_date + (int) $end_hour * 3600 + (int) $end_minute * 60;
        if ($time_start > $time_end) {
            return (get_string('startsbeforeitends', 'theme_ucsf'));
        }
        return '';
    }

    /**
     * Returns the rendered admin setting form elements.
     *
     * @param array $data
     *  $data = [
     *    'start_date'=> (string) the start date
     *    'start_hour'=> (string) the start hour
     *    'start_minute' => (string) the start minute
     *    'end_date' => (string) the end date
     *    'end_hour' => (string) then end minute
     *    'end_minute' => (string) the end minute
     *  ]
     * @param string $query
     * @return string
     * @throws coding_exception
     */
    public function output_html($data, $query = ''): string {
        $default = $this->get_defaultsetting();
        $return = html_writer::start_div('form-text defaultsnext');
        $return .= html_writer::empty_tag('input', array(
                'aria-label' => get_string('startdate', 'theme_ucsf'),
                'class' => 'form-control text-ltr ucsf-datepicker',
                'id' => $this->get_id() . '_' . self::START_DATE,
                'name' => $this->get_full_name() . '[' . self::START_DATE . ']',
                'size' => '15',
                'value' => s($data[self::START_DATE]),
        ));
        $return .= ' ';
        $return .= html_writer::tag('label', get_string('start_hour', 'theme_ucsf'), array(
                'class' => 'accesshide',
                'for' => $this->get_id() . '_' . self::START_HOUR,
        ));
        $return .= html_writer::span(ucfirst(get_string('hour')) . ':', '', array('aria-hidden' => 'true'));
        $return .= ' ';
        $return .= html_writer::start_tag('select', array(
                'class' => 'custom-select',
                'id' => $this->get_id() . '_' . self::START_HOUR,
                'name' => $this->get_full_name() . '[' . self::START_HOUR . ']',
        ));
        for ($i = 0; $i <= 23; $i++) {
            $attrs = array('value' => $i);
            if ($i === (int) $data[self::START_HOUR]) {
                $attrs['selected'] = 'selected';
            }
            $return .= html_writer::tag('option', str_pad($i, 2, '0', STR_PAD_LEFT), $attrs);
        }
        $return .= html_writer::end_tag('select');
        $return .= ' ';
        $return .= html_writer::tag('label', get_string('start_minute', 'theme_ucsf'), array(
                'class' => 'accesshide',
                'for' => $this->get_id() . '_' . self::START_MINUTE,
        ));
        $return .= html_writer::span(ucfirst(get_string('minute')) . ':', '', array('aria-hidden' => 'true'));
        $return .= ' ';
        $return .= html_writer::start_tag('select', array(
                'class' => 'custom-select',
                'id' => $this->get_id() . '_' . self::START_MINUTE,
                'name' => $this->get_full_name() . '[' . self::START_MINUTE . ']',
        ));
        for ($i = 0; $i < 60; $i += 5) {
            $attrs = array('value' => $i);
            if ($i === (int) $data[self::START_MINUTE]) {
                $attrs['selected'] = 'selected';
            }
            $return .= html_writer::tag('option', str_pad($i, 2, '0', STR_PAD_LEFT), $attrs);
        }
        $return .= html_writer::end_tag('select');
        $return .= html_writer::empty_tag('br');
        $return .= html_writer::empty_tag('input', array(
                'aria-label' => get_string('enddate', 'theme_ucsf'),
                'class' => 'form-control text-ltr ucsf-datepicker',
                'id' => $this->get_id() . '_' . self::END_DATE,
                'name' => $this->get_full_name() . '[' . self::END_DATE . ']',
                'size' => '15',
                'value' => s($data[self::END_DATE]),
        ));
        $return .= ' ';
        $return .= html_writer::tag('label', get_string('start_hour', 'theme_ucsf'), array(
                'class' => 'accesshide',
                'for' => $this->get_id() . '_' . self::END_HOUR,
        ));
        $return .= html_writer::span(ucfirst(get_string('hour')) . ':', '', array('aria-hidden' => 'true'));
        $return .= ' ';
        $return .= html_writer::start_tag('select', array(
                'class' => 'custom-select',
                'id' => $this->get_id() . '_' . self::END_HOUR,
                'name' => $this->get_full_name() . '[' . self::END_HOUR . ']',
        ));
        for ($i = 0; $i <= 23; $i++) {
            $attrs = array('value' => $i);
            if ($i === (int) $data[self::END_HOUR]) {
                $attrs['selected'] = 'selected';
            }
            $return .= html_writer::tag('option', str_pad($i, 2, '0', STR_PAD_LEFT), $attrs);
        }
        $return .= html_writer::end_tag('select');
        $return .= ' ';
        $return .= html_writer::tag('label', get_string('start_minute', 'theme_ucsf'), array(
                'class' => 'accesshide',
                'for' => $this->get_id() . '_' . self::END_MINUTE,
        ));
        $return .= html_writer::span(ucfirst(get_string('minute')) . ':', '', array('aria-hidden' => 'true'));
        $return .= ' ';
        $return .= html_writer::start_tag('select', array(
                'class' => 'custom-select',
                'id' => $this->get_id() . self::END_MINUTE,
                'name' => $this->get_full_name() . '[' . self::END_MINUTE . ']',
        ));
        for ($i = 0; $i <= 60; $i += 5) {
            $attrs = array('value' => $i);
            if ($i === (int) $data[self::END_MINUTE]) {
                $attrs['selected'] = 'selected';
            }
            $return .= html_writer::tag('option', str_pad($i, 2, '0', STR_PAD_LEFT), $attrs);
        }
        $return .= html_writer::end_tag('select');
        $return .= html_writer::end_div();
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $default, $query);
    }
}
