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
 * Admin settings form component for selecting a date range. (year, month, and day).
 *
 * @author Sasa Prsir <sasa.prsir@lambdasolutions.net>
 * @author Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_daterange extends admin_setting {
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';

    public string $startdatesettingname;
    public string $enddatesettingname;

    /**
     * @param string $name
     * @param string $start_date_setting_name
     * @param string $end_date_setting_name
     * @param string $visiblename
     * @param string $description
     */
    public function __construct(
            string $name,
            string $startdatesettingname,
            string $enddatesettingname,
            string $visiblename,
            string $description
    ) {
        $this->start_date_setting_name = $startdatesettingname;
        $this->end_date_setting_name = $enddatesettingname;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * @return array|null $data
     *  $data = [
     *    'start_date'=> (string) the start date
     *    'end_date' => (string) the end date
     *  ]
     */
    public function get_setting(): ?array {
        $startdate = $this->config_read($this->start_date_setting_name);
        $enddate = $this->config_read($this->end_date_setting_name);
        if (is_null($startdate) || is_null($enddate)) {
            return null;
        }
        return [
                self::START_DATE => $startdate,
                self::END_DATE => $enddate,
        ];
    }

    /**
     * @param array $data
     *  $data = [
     *    'start_date'=> (string) the start date
     *    'end_date' => (string) the end date
     *  ]
     * @return string empty string if ok, string error message otherwise
     * @throws coding_exception
     */
    public function write_setting($data): string {
        $startdate = ('' !== trim($data[self::START_DATE])) ? trim($data[self::START_DATE]) : '';
        $enddate = ('' !== trim($data[self::END_DATE])) ? trim($data[self::END_DATE]) : '';
        $validate = $this->validate($startdate, $enddate);
        if ('' === $validate) {
            $result = $this->config_write($this->start_date_setting_name, $startdate)
                    && $this->config_write($this->end_date_setting_name, $enddate);
            return $result ? '' : get_string('errorsetting', 'admin');
        }
        return $validate;
    }

    /**
     * Validate data before storage.
     *
     * @param string $start_date
     * @param string $end_date
     * @return string empty string if ok, string error message otherwise
     * @throws coding_exception
     */
    protected function validate(string $startdate, string $enddate): string {
        if ('' === $startdate && '' === $enddate) {
            return get_string('emptystartandenddate', 'theme_ucsf');
        }
        if ('' === $startdate) {
            return get_string('emptystartdate', 'theme_ucsf');
        }
        if ('' === $enddate) {
            return get_string('emptyenddate', 'theme_ucsf');
        }
        $timestart = strtotime($startdate);
        $timeend = strtotime($enddate);
        if (false === $timestart && false === $timeend) {
            return get_string('invalidstartandenddate', 'theme_ucsf');
        }
        if (false === $timestart) {
            return get_string('invalidstartdate', 'theme_ucsf');
        }
        if (false === $timeend) {
            return get_string('invalidenddate', 'theme_ucsf');
        }
        if ($timestart > $timeend) {
            return get_string('startsbeforeitends', 'theme_ucsf');
        }
        return '';
    }

    /**
     * Returns the rendered admin setting form elements.
     *
     * @param array|null $data
     *  $data = [
     *    'start_date'=> (string) the start date
     *    'end_date' => (string) the end date
     *  ]
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = ''): string {
        $startdate = '';
        if (is_array($data) && array_key_exists(self::START_DATE, $data)) {
            $startdate = $data[self::START_DATE];
        }
        $enddate = '';
        if (is_array($data) && array_key_exists(self::END_DATE, $data)) {
            $enddate = $data[self::END_DATE];
        }

        $default = $this->get_defaultsetting();
        $return = html_writer::start_div('form-text defaultsnext');
        $return .= html_writer::empty_tag('input', [
                'aria-label' => get_string('startdate', 'theme_ucsf'),
                'class' => 'form-control text-ltr ucsf-datepicker',
                'id' => $this->get_id() . '_' . self::START_DATE,
                'name' => $this->get_full_name() . '[' . self::START_DATE . ']',
                'size' => '15',
                'value' => s($startdate),
        ]);
        $return .= html_writer::empty_tag('br');
        $return .= html_writer::empty_tag('input', [
                'aria-label' => get_string('enddate', 'theme_ucsf'),
                'class' => 'form-control text-ltr ucsf-datepicker',
                'id' => $this->get_id() . '_' . self::END_DATE,
                'name' => $this->get_full_name() . '[' . self::END_DATE . ']',
                'size' => '15',
                'value' => s($enddate),
        ]);
        $return .= html_writer::end_div();
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $default, $query);
    }
}
