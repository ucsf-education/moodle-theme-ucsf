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
 * @package theme_ucsf
 * @author Sasa Prsir <sasa.prsir@lambdasolutions.net>
 * @author Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_datetimerange extends admin_setting {

    /** @var string (Partial) start date form field name. */
    const START_DATE = 'start_date';

    /** @var string (Partial) start hour form field name. */
    const START_HOUR = 'start_hour';

    /** @var string (Partial) start minute form field name. */
    const START_MINUTE = 'start_minute';

    /** @var string (Partial) end date form field name. */
    const END_DATE = 'end_date';

    /** @var string (Partial) end hour form field name. */
    const END_HOUR = 'end_hour';

    /** @var string (Partial) end minute form field name. */
    const END_MINUTE = 'end_minute';

    /** @var string Start date config setting name. */
    public string $startdatesettingname;

    /** @var string Start minute config setting name. */
    public string $startminutesettingname;

    /** @var string Start hour config setting name. */
    public string $starthoursettingname;

    /** @var string End hour config setting name. */
    public string $enddatesettingname;

    /** @var string End minute config setting name. */
    public string $endminutesettingname;

    /** @var string End hour config setting name. */
    public string $endhoursettingname;

    /**
     * Class constructor.
     *
     * @param string $name
     * @param string $startdatesettingname
     * @param string $starthoursettingname
     * @param string $startminutesettingname
     * @param string $enddatesettingname
     * @param string $endhoursettingname
     * @param string $endminutesettingname
     * @param string $visiblename
     * @param string $description
     */
    public function __construct(
            string $name,
            string $startdatesettingname,
            string $starthoursettingname,
            string $startminutesettingname,
            string $enddatesettingname,
            string $endhoursettingname,
            string $endminutesettingname,
            string $visiblename,
            string $description
    ) {
        $this->startdatesettingname = $startdatesettingname;
        $this->starthoursettingname = $starthoursettingname;
        $this->startminutesettingname = $startminutesettingname;
        $this->enddatesettingname = $enddatesettingname;
        $this->endhoursettingname = $endhoursettingname;
        $this->endminutesettingname = $endminutesettingname;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * {@inheritdoc}
     *
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
        $startdate = $this->config_read($this->startdatesettingname);
        $starthour = $this->config_read($this->starthoursettingname);
        $startminute = $this->config_read($this->startminutesettingname);
        $enddate = $this->config_read($this->enddatesettingname);
        $endhour = $this->config_read($this->endhoursettingname);
        $endminute = $this->config_read($this->endminutesettingname);
        if (is_null($startdate)
                || is_null($starthour)
                || is_null($startminute)
                || is_null($enddate)
                || is_null($endhour)
                || is_null($endminute)
        ) {
            return null;
        }
        return [
                self::START_DATE => $startdate,
                self::START_HOUR => $starthour,
                self::START_MINUTE => $startminute,
                self::END_DATE => $enddate,
                self::END_HOUR => $endhour,
                self::END_MINUTE => $endminute,
        ];
    }

    /**
     * {@inheritdoc}
     *
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
        $startdate = ('' !== $data[self::START_DATE]) ? $data[self::START_DATE] : '';
        $starthour = ('' !== $data[self::START_HOUR]) ? $data[self::START_HOUR] : '';
        $startminute = ('' !== $data[self::START_MINUTE]) ? $data[self::START_MINUTE] : '';
        $enddate = ('' !== $data[self::END_DATE]) ? $data[self::END_DATE] : '';
        $endhour = ('' !== $data[self::END_HOUR]) ? $data[self::END_HOUR] : '';
        $endminute = ('' !== $data[self::END_MINUTE]) ? $data[self::END_MINUTE] : '';
        $validate = $this->validate($startdate, $starthour, $startminute, $enddate, $endhour, $endminute);
        if ('' === $validate) {
            $result = $this->config_write($this->startdatesettingname, $startdate)
                    && $this->config_write($this->starthoursettingname, $starthour)
                    && $this->config_write($this->startminutesettingname, $startminute)
                    && $this->config_write($this->enddatesettingname, $enddate)
                    && $this->config_write($this->endhoursettingname, $endhour)
                    && $this->config_write($this->endminutesettingname, $endminute);
            return ($result ? '' : get_string('errorsetting', 'admin'));
        }
        return $validate;
    }

    /**
     * Validate data before storage.
     *
     * @param string $startdate
     * @param string $starthour
     * @param string $startminute
     * @param string $enddate
     * @param string $endhour
     * @param string $endminute
     * @return string empty string if ok, string error message otherwise
     * @throws coding_exception
     */
    protected function validate(
            string $startdate,
            string $starthour,
            string $startminute,
            string $enddate,
            string $endhour,
            string $endminute
    ): string {
        if ('' === $startdate && '' === $enddate) {
            return get_string('emptystartandenddate', 'theme_ucsf');
        }
        if ('' === $startdate) {
            return get_string('emptystartdate', 'theme_ucsf');
        }
        if ('' === $enddate) {
            return get_string('emptyenddate', 'theme_ucsf');
        }
        $startdate = strtotime($startdate);
        $enddate = strtotime($enddate);
        if (false === $startdate && false === $enddate) {
            return get_string('invalidstartandenddate', 'theme_ucsf');
        }
        if (false === $startdate) {
            return get_string('invalidstartdate', 'theme_ucsf');
        }
        if (false === $enddate) {
            return get_string('invalidenddate', 'theme_ucsf');
        }
        if ($startdate > $enddate) {
            return get_string('startsbeforeitends', 'theme_ucsf');
        }
        $timestart = $startdate + (int) $starthour * 3600 + (int) $startminute * 60;
        $timeend = $enddate + (int) $endhour * 3600 + (int) $endminute * 60;
        if ($timestart > $timeend) {
            return (get_string('startsbeforeitends', 'theme_ucsf'));
        }
        return '';
    }

    /**
     * Returns the rendered admin setting form elements.
     *
     * @param array|null $data
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
        $startdate = '';
        if (is_array($data) && array_key_exists(self::START_DATE, $data)) {
            $startdate = $data[self::START_DATE];
        }
        $starthour = '0';
        if (is_array($data) && array_key_exists(self::START_HOUR, $data)) {
            $starthour = $data[self::START_HOUR];
        }
        $startminute = '0';
        if (is_array($data) && array_key_exists(self::START_MINUTE, $data)) {
            $startminute = $data[self::START_MINUTE];
        }
        $enddate = '';
        if (is_array($data) && array_key_exists(self::END_DATE, $data)) {
            $enddate = $data[self::END_DATE];
        }
        $endhour = '0';
        if (is_array($data) && array_key_exists(self::END_HOUR, $data)) {
            $endhour = $data[self::END_HOUR];
        }
        $endminute = '0';
        if (is_array($data) && array_key_exists(self::END_MINUTE, $data)) {
            $endminute = $data[self::END_MINUTE];
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
        $return .= ' ';
        $return .= html_writer::tag('label', get_string('start_hour', 'theme_ucsf'), [
                'class' => 'accesshide',
                'for' => $this->get_id() . '_' . self::START_HOUR,
        ]);
        $return .= html_writer::span(ucfirst(get_string('hour')) . ':', '', ['aria-hidden' => 'true']);
        $return .= ' ';
        $return .= html_writer::start_tag('select', [
                'class' => 'custom-select',
                'id' => $this->get_id() . '_' . self::START_HOUR,
                'name' => $this->get_full_name() . '[' . self::START_HOUR . ']',
        ]);
        for ($i = 0; $i <= 23; $i++) {
            $attrs = ['value' => $i];
            if ($i === (int) $starthour) {
                $attrs['selected'] = 'selected';
            }
            $return .= html_writer::tag('option', str_pad($i, 2, '0', STR_PAD_LEFT), $attrs);
        }
        $return .= html_writer::end_tag('select');
        $return .= ' ';
        $return .= html_writer::tag('label', get_string('start_minute', 'theme_ucsf'), [
                'class' => 'accesshide',
                'for' => $this->get_id() . '_' . self::START_MINUTE,
        ]);
        $return .= html_writer::span(ucfirst(get_string('minute')) . ':', '', ['aria-hidden' => 'true']);
        $return .= ' ';
        $return .= html_writer::start_tag('select', [
                'class' => 'custom-select',
                'id' => $this->get_id() . '_' . self::START_MINUTE,
                'name' => $this->get_full_name() . '[' . self::START_MINUTE . ']',
        ]);
        for ($i = 0; $i < 60; $i += 5) {
            $attrs = ['value' => $i];
            if ($i === (int) $startminute) {
                $attrs['selected'] = 'selected';
            }
            $return .= html_writer::tag('option', str_pad($i, 2, '0', STR_PAD_LEFT), $attrs);
        }
        $return .= html_writer::end_tag('select');
        $return .= html_writer::empty_tag('br');
        $return .= html_writer::empty_tag('input', [
                'aria-label' => get_string('enddate', 'theme_ucsf'),
                'class' => 'form-control text-ltr ucsf-datepicker',
                'id' => $this->get_id() . '_' . self::END_DATE,
                'name' => $this->get_full_name() . '[' . self::END_DATE . ']',
                'size' => '15',
                'value' => s($enddate),
        ]);
        $return .= ' ';
        $return .= html_writer::tag('label', get_string('start_hour', 'theme_ucsf'), [
                'class' => 'accesshide',
                'for' => $this->get_id() . '_' . self::END_HOUR,
        ]);
        $return .= html_writer::span(ucfirst(get_string('hour')) . ':', '', ['aria-hidden' => 'true']);
        $return .= ' ';
        $return .= html_writer::start_tag('select', [
                'class' => 'custom-select',
                'id' => $this->get_id() . '_' . self::END_HOUR,
                'name' => $this->get_full_name() . '[' . self::END_HOUR . ']',
        ]);
        for ($i = 0; $i <= 23; $i++) {
            $attrs = ['value' => $i];
            if ($i === (int) $endhour) {
                $attrs['selected'] = 'selected';
            }
            $return .= html_writer::tag('option', str_pad($i, 2, '0', STR_PAD_LEFT), $attrs);
        }
        $return .= html_writer::end_tag('select');
        $return .= ' ';
        $return .= html_writer::tag('label', get_string('start_minute', 'theme_ucsf'), [
                'class' => 'accesshide',
                'for' => $this->get_id() . '_' . self::END_MINUTE,
        ]);
        $return .= html_writer::span(ucfirst(get_string('minute')) . ':', '', ['aria-hidden' => 'true']);
        $return .= ' ';
        $return .= html_writer::start_tag('select', [
                'class' => 'custom-select',
                'id' => $this->get_id() . self::END_MINUTE,
                'name' => $this->get_full_name() . '[' . self::END_MINUTE . ']',
        ]);
        for ($i = 0; $i <= 60; $i += 5) {
            $attrs = ['value' => $i];
            if ($i === (int) $endminute) {
                $attrs['selected'] = 'selected';
            }
            $return .= html_writer::tag('option', str_pad($i, 2, '0', STR_PAD_LEFT), $attrs);
        }
        $return .= html_writer::end_tag('select');
        $return .= html_writer::end_div();
        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $default, $query);
    }
}
