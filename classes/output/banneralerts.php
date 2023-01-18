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

namespace theme_ucsf\output;

use DateTime;
use dml_exception;
use moodle_page;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_ucsf\constants;
use theme_ucsf\utils\config;
use theme_ucsf\utils\coursecategory;

/**
 * Banner alerts.
 *
 * @package theme_ucsf
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class banneralerts implements renderable, templatable {

    protected moodle_page $page;

    const ALERT_LEVEL_CSS_CLASSES_MAP = [
            constants::BANNERALERT_LEVEL_INFORMATION => 'alert-info',
            constants::BANNERALERT_LEVEL_ANNOUNCEMENT => 'alert-warning',
            constants::BANNERALERT_LEVEL_WARNING => 'alert-danger',
    ];

    /**
     * @param moodle_page $page
     */
    public function __construct(moodle_page $page) {
        $this->page = $page;
    }

    /**
     * Retrieve banner alerts applicable to the given page.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws dml_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $obj = new stdClass();
        $obj->alerts = [];

        for ($i = 1; $i <= constants::BANNERALERT_ITEMS_COUNT; $i++) {

            // skip if alert has already been flagged as seen in this user's session.
            if (array_key_exists(constants::BANNERALERT_SESSION_KEY, $_SESSION)
                    && array_key_exists($i, $_SESSION[constants::BANNERALERT_SESSION_KEY])
                    && $_SESSION[constants::BANNERALERT_SESSION_KEY][$i]) {
                continue;
            }

            // skip if the alert has not been enabled
            if (!config::get_setting('enable' . $i . 'alert')) {
                continue;
            }

            $alert_message = trim(config::get_setting("alert{$i}text", ''));
            // skip if the alert has no message
            if ('' === $alert_message) {
                continue;
            }

            $alert_target = config::get_setting('categories_list_alert' . $i, '');
            // skip if no target is configured
            if ('' === $alert_target) {
                continue;
            }

            $alert_type = config::get_setting('recurring_alert' . $i, '');
            // skip if no alert type is configured
            if ('' === $alert_type) {
                continue;
            }

            // ----------
            // Check if this alert belongs on the current page, based on its target type.
            // ----------
            $current_page_is_applicable_target = false;

            switch ($alert_target) {
                // dashboard only alert
                case constants::BANNERALERT_TARGET_DASHBOARD:
                    // KLUDGE!
                    // check the current page layout.
                    // skip if this alert is targeting the user dashboard, and we're NOT on the dashboard.
                    $current_page_is_applicable_target = ('mydashboard' === $this->page->pagelayout);
                    break;
                // site-wide alert
                case constants::BANNERALERT_TARGET_SITEWIDE:
                    $current_page_is_applicable_target = true; //this alert always targets the current page
                    break;
                // category specific-alert
                default:
                    // check if this alert targets this page's course category or any if its parent categories.
                    // skip if not.
                    $current_course_category_id = coursecategory::get_current_category_id();
                    $category_ids = coursecategory::get_reverse_category_hierarchy($current_course_category_id);
                    $current_page_is_applicable_target = in_array($alert_target, $category_ids);
                    break;
            }

            // skip if this alert does not belong on the current page
            if (!$current_page_is_applicable_target) {
                continue;
            }

            // ----------
            // Check if this alert is within its given date/time boundaries
            // ----------
            $alert_is_within_datetime_boundaries = false;

            $now = time();
            $today = strtotime("midnight", $now); // start of today/"today at midnight".

            switch ($alert_type) {
                case constants::BANNERALERT_TYPE_UNBOUND:
                    $alert_is_within_datetime_boundaries = true;
                    break;
                case constants::BANNERALERT_TYPE_DATEBOUND:
                    $date_config = $this->get_date_config($i);

                    // skip if start or end date are missing
                    if ('' === $date_config['start_date'] || '' === $date_config['end_date']) {
                        break;
                    }

                    $start_date = date_create($date_config['start_date']);
                    $end_date = date_create($date_config['end_date']);

                    // skip if configured dates cannot be converted to date objects
                    if (false === $start_date || false === $end_date) {
                        break;
                    }

                    $start_date->setTime($date_config['start_hour'], $date_config['start_minute']);
                    $end_date->setTime($date_config['end_hour'], $date_config['end_minute']);

                    // skip if today is not within range
                    if ($start_date->getTimestamp() > $now || $end_date->getTimestamp() < $now) {
                        break;
                    }

                    $alert_is_within_datetime_boundaries = true;
                    break;
                case constants::BANNERALERT_TYPE_RECURRENCE_DAILY:
                    $date_config = $this->get_date_config($i, '_daily');

                    // skip if start or end date are missing
                    if ('' === $date_config['start_date'] || '' === $date_config['end_date']) {
                        break;
                    }

                    $start_date = date_create($date_config['start_date']);
                    $end_date = date_create($date_config['end_date']);

                    // skip if configured dates cannot be converted to date objects
                    if (false === $start_date || false === $end_date) {
                        break;
                    }

                    // check date range first. skip if today's date is out of boundaries.
                    if ($start_date->getTimestamp() > $today || $end_date->getTimestamp() < $today) {
                        break;
                    }

                    // construct a new start and end date, based on today's date, but with the configured times-of-day applied.
                    $today_start_date = new DateTime();
                    $today_start_date->setTimestamp($now);
                    $today_start_date->setTime($date_config['start_hour'], $date_config['start_minute']);

                    $today_end_date = new DateTime();
                    $today_end_date->setTimestamp($now);
                    $today_end_date->setTime($date_config['end_hour'], $date_config['end_minute']);

                    $start_date->setTime($date_config['start_hour'], $date_config['start_minute']);
                    $end_date->setTime($date_config['end_hour'], $date_config['end_minute']);

                    // check time range next. skip if the current time-of-day is out of boundaries.
                    if ($today_start_date->getTimestamp() > $now || $today_end_date->getTimestamp() < $now) {
                        break;
                    }

                    $alert_is_within_datetime_boundaries = true;
                    break;
                case constants::BANNERALERT_TYPE_RECURRENCE_WEEKLY:
                    $day_of_the_week = config::get_setting('show_week_day' . $i, '');

                    // skip if no day of week was configured
                    if ('' === $day_of_the_week) {
                        break;
                    }

                    // skip if the today does not fall on the same day-of-week as the given one.
                    if ($day_of_the_week !== date('w', $now)) {
                        break;
                    }

                    $date_config = $this->get_date_config($i, '_weekly');

                    // skip if start or end date are missing
                    if ('' === $date_config['start_date'] || '' === $date_config['end_date']) {
                        break;
                    }

                    $start_date = date_create($date_config['start_date']);
                    $end_date = date_create($date_config['end_date']);

                    // skip if configured dates cannot be converted to date objects
                    if (false === $start_date || false === $end_date) {
                        break;
                    }

                    // check date range first. skip if today's date is out of boundaries.
                    if ($start_date->getTimestamp() > $today || $end_date->getTimestamp() < $today) {
                        break;
                    }

                    // construct a new start and end date, based on today's date, but with the configured times-of-day applied.
                    $today_start_date = new DateTime();
                    $today_start_date->setTimestamp($now);
                    $today_start_date->setTime($date_config['start_hour'], $date_config['start_minute']);

                    $today_end_date = new DateTime();
                    $today_end_date->setTimestamp($now);
                    $today_end_date->setTime($date_config['end_hour'], $date_config['end_minute']);

                    $start_date->setTime($date_config['start_hour'], $date_config['start_minute']);
                    $end_date->setTime($date_config['end_hour'], $date_config['end_minute']);

                    // check time range next. skip if the current time-of-day is out of boundaries.
                    if ($today_start_date->getTimestamp() > $now || $today_end_date->getTimestamp() < $now) {
                        break;
                    }

                    $alert_is_within_datetime_boundaries = true;
            }

            // skip if this alert is outside its configured date boundaries
            if (!$alert_is_within_datetime_boundaries) {
                continue;
            }

            // ----------
            // This alert should be displayed! Add it to the output.
            // ----------
            $alert_level = config::get_setting("alert{$i}type", constants::BANNERALERT_LEVEL_INFORMATION);

            $alert_classes = array_key_exists($alert_level, self::ALERT_LEVEL_CSS_CLASSES_MAP)
                    ? self::ALERT_LEVEL_CSS_CLASSES_MAP[$alert_level]
                    : self::ALERT_LEVEL_CSS_CLASSES_MAP[constants::BANNERALERT_LEVEL_INFORMATION];

            $obj->alerts[] = [
                    'id' => $i,
                    'classes' => $alert_classes,
                    'message' => $alert_message,
            ];
        }

        return $obj;
    }

    /**
     * Retrieves the date and time values for banner alerts from the theme configuration.
     *
     * @param int $index The alert number.
     * @param string $suffix Config setting name suffix, depends on the type of alert.
     * @return array
     *  $data = [
     *    'start_date' => (string) the start date
     *    'start_hour'=> (string) the start hour
     *    'start_minute' => (string) the start minute
     *    'end_date' => (string) the end date
     *    'end_hour' => (string) then end minute
     *    'end_minute' => (string) the end minute
     *  ]
     * @throws dml_exception
     */
    protected function get_date_config(int $index, string $suffix = ''): array {
        return array(
                'start_date' => config::get_setting('start_date' . $suffix . $index, ''),
                'start_hour' => (int) config::get_setting('start_hour' . $suffix . $index, 0),
                'start_minute' => (int) config::get_setting('start_minute' . $suffix . $index, 0),
                'end_date' => config::get_setting('end_date' . $suffix . $index, ''),
                'end_hour' => (int) config::get_setting('end_hour' . $suffix . $index, 0),
                'end_minute' => (int) config::get_setting('end_minute' . $suffix . $index, 0),
        );
    }
}