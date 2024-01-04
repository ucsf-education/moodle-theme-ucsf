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

            // Skip if alert has already been flagged as seen in this user's session.
            if (array_key_exists(constants::BANNERALERT_SESSION_KEY, $_SESSION)
                    && array_key_exists($i, $_SESSION[constants::BANNERALERT_SESSION_KEY])
                    && $_SESSION[constants::BANNERALERT_SESSION_KEY][$i]) {
                continue;
            }

            // Skip if the alert has not been enabled.
            if (!config::get_setting('enable' . $i . 'alert')) {
                continue;
            }

            $alertmessage = trim(config::get_setting("alert{$i}text", ''));
            // Skip if the alert has no message.
            if ('' === $alertmessage) {
                continue;
            }

            $alerttarget = config::get_setting('categories_list_alert' . $i, '');
            // Skip if no target is configured.
            if ('' === $alerttarget) {
                continue;
            }

            $alerttype = config::get_setting('recurring_alert' . $i, '');
            // Skip if no alert type is configured.
            if ('' === $alerttype) {
                continue;
            }

            // Check if this alert belongs on the current page, based on its target type.
            $currentpageisapplicabletarget = false;

            switch ($alerttarget) {
                // Dashboard only alert.
                case constants::BANNERALERT_TARGET_DASHBOARD:
                    // KLUDGE!
                    // Check the current page layout.
                    // Skip if this alert is targeting the user dashboard, and we're NOT on the dashboard.
                    $currentpageisapplicabletarget = ('mydashboard' === $this->page->pagelayout);
                    break;
                // Site-wide alert.
                case constants::BANNERALERT_TARGET_SITEWIDE:
                    $currentpageisapplicabletarget = true; // This alert always targets the current page.
                    break;
                // Category specific-alert.
                default:
                    // Check if this alert targets this page's course category or any if its parent categories.
                    // Skip if not.
                    $currentcoursecategoryid = coursecategory::get_current_category_id();
                    $categoryids = coursecategory::get_reverse_category_hierarchy($currentcoursecategoryid);
                    $currentpageisapplicabletarget = in_array($alerttarget, $categoryids);
                    break;
            }

            // Skip if this alert does not belong on the current page.
            if (!$currentpageisapplicabletarget) {
                continue;
            }

            // Check if this alert is within its given date/time boundaries.
            $alertiswithindatetimeboundaries = false;

            $now = time();
            $today = strtotime("midnight", $now); // Start of today/"today at midnight".

            switch ($alerttype) {
                case constants::BANNERALERT_TYPE_UNBOUND:
                    $alertiswithindatetimeboundaries = true;
                    break;
                case constants::BANNERALERT_TYPE_DATEBOUND:
                    $dateconfig = $this->get_date_config($i);

                    // Skip if start or end date are missing.
                    if ('' === $dateconfig['start_date'] || '' === $dateconfig['end_date']) {
                        break;
                    }

                    $startdate = date_create($dateconfig['start_date']);
                    $enddate = date_create($dateconfig['end_date']);

                    // Skip if configured dates cannot be converted to date objects.
                    if (false === $startdate || false === $enddate) {
                        break;
                    }

                    $startdate->setTime($dateconfig['start_hour'], $dateconfig['start_minute']);
                    $enddate->setTime($dateconfig['end_hour'], $dateconfig['end_minute']);

                    // Skip if today is not within range.
                    if ($startdate->getTimestamp() > $now || $enddate->getTimestamp() < $now) {
                        break;
                    }

                    $alertiswithindatetimeboundaries = true;
                    break;
                case constants::BANNERALERT_TYPE_RECURRENCE_DAILY:
                    $dateconfig = $this->get_date_config($i, '_daily');

                    // Skip if start or end date are missing.
                    if ('' === $dateconfig['start_date'] || '' === $dateconfig['end_date']) {
                        break;
                    }

                    $startdate = date_create($dateconfig['start_date']);
                    $enddate = date_create($dateconfig['end_date']);

                    // Skip if configured dates cannot be converted to date objects.
                    if (false === $startdate || false === $enddate) {
                        break;
                    }

                    // Check date range first. Skip if today's date is out of boundaries.
                    if ($startdate->getTimestamp() > $today || $enddate->getTimestamp() < $today) {
                        break;
                    }

                    // Construct a new start and end date, based on today's date, but with the configured times-of-day applied.
                    $todaystartdate = new DateTime();
                    $todaystartdate->setTimestamp($now);
                    $todaystartdate->setTime($dateconfig['start_hour'], $dateconfig['start_minute']);

                    $todayenddate = new DateTime();
                    $todayenddate->setTimestamp($now);
                    $todayenddate->setTime($dateconfig['end_hour'], $dateconfig['end_minute']);

                    $startdate->setTime($dateconfig['start_hour'], $dateconfig['start_minute']);
                    $enddate->setTime($dateconfig['end_hour'], $dateconfig['end_minute']);

                    // Check time range next. skip if the current time-of-day is out of boundaries.
                    if ($todaystartdate->getTimestamp() > $now || $todayenddate->getTimestamp() < $now) {
                        break;
                    }

                    $alertiswithindatetimeboundaries = true;
                    break;
                case constants::BANNERALERT_TYPE_RECURRENCE_WEEKLY:
                    $dayoftheweek = config::get_setting('show_week_day' . $i, '');

                    // Skip if no day of week was configured.
                    if ('' === $dayoftheweek) {
                        break;
                    }

                    // Skip if the today does not fall on the same day-of-week as the given one.
                    if ($dayoftheweek !== date('w', $now)) {
                        break;
                    }

                    $dateconfig = $this->get_date_config($i, '_weekly');

                    // Skip if start or end date are missing.
                    if ('' === $dateconfig['start_date'] || '' === $dateconfig['end_date']) {
                        break;
                    }

                    $startdate = date_create($dateconfig['start_date']);
                    $enddate = date_create($dateconfig['end_date']);

                    // Skip if configured dates cannot be converted to date objects.
                    if (false === $startdate || false === $enddate) {
                        break;
                    }

                    // Check date range first. skip if today's date is out of boundaries.
                    if ($startdate->getTimestamp() > $today || $enddate->getTimestamp() < $today) {
                        break;
                    }

                    // Construct a new start and end date, based on today's date, but with the configured times-of-day applied.
                    $todaystartdate = new DateTime();
                    $todaystartdate->setTimestamp($now);
                    $todaystartdate->setTime($dateconfig['start_hour'], $dateconfig['start_minute']);

                    $todayenddate = new DateTime();
                    $todayenddate->setTimestamp($now);
                    $todayenddate->setTime($dateconfig['end_hour'], $dateconfig['end_minute']);

                    $startdate->setTime($dateconfig['start_hour'], $dateconfig['start_minute']);
                    $enddate->setTime($dateconfig['end_hour'], $dateconfig['end_minute']);

                    // Check time range next. Skip if the current time-of-day is out of boundaries.
                    if ($todaystartdate->getTimestamp() > $now || $todayenddate->getTimestamp() < $now) {
                        break;
                    }

                    $alertiswithindatetimeboundaries = true;
            }

            // Skip if this alert is outside its configured date boundaries.
            if (!$alertiswithindatetimeboundaries) {
                continue;
            }

            // This alert should be displayed! Add it to the output.
            $alertlevel = config::get_setting("alert{$i}type", constants::BANNERALERT_LEVEL_INFORMATION);

            $alertclasses = array_key_exists($alertlevel, self::ALERT_LEVEL_CSS_CLASSES_MAP)
                    ? self::ALERT_LEVEL_CSS_CLASSES_MAP[$alertlevel]
                    : self::ALERT_LEVEL_CSS_CLASSES_MAP[constants::BANNERALERT_LEVEL_INFORMATION];

            $obj->alerts[] = [
                    'id' => $i,
                    'classes' => $alertclasses,
                    'message' => $alertmessage,
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
        return [
                'start_date' => config::get_setting('start_date' . $suffix . $index, ''),
                'start_hour' => (int) config::get_setting('start_hour' . $suffix . $index, 0),
                'start_minute' => (int) config::get_setting('start_minute' . $suffix . $index, 0),
                'end_date' => config::get_setting('end_date' . $suffix . $index, ''),
                'end_hour' => (int) config::get_setting('end_hour' . $suffix . $index, 0),
                'end_minute' => (int) config::get_setting('end_minute' . $suffix . $index, 0),
        ];
    }
}
