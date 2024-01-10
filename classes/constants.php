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

/**
 * Constants interface for this theme.
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface constants {

    /** @var int The maximum number of help menu items. */
    const HELPMENU_ITEMS_COUNT = 10;

    /** @var int The maximum number of banner alerts. */
    const BANNERALERT_ITEMS_COUNT = 10;

    /** @var string Banner alert type - the alert is not bound to any date range restrictions. */
    const BANNERALERT_TYPE_UNBOUND = '1';
    /** @var string Banner alert type - the alert is not restricted by a start- and end-date. */
    const BANNERALERT_TYPE_DATEBOUND = '2';

    /** @var string Banner alert type - the alert recurs daily within a given start- and end-time. */
    const BANNERALERT_TYPE_RECURRENCE_DAILY = '3';

    /** @var string Banner alert type - the alert recurs weekly on a given weekday, within given date and time range. */
    const BANNERALERT_TYPE_RECURRENCE_WEEKLY = '4';

    /** @var string Banner alert level - the alert is informational. */
    const BANNERALERT_LEVEL_INFORMATION = 'info';

    /** @var string Banner alert level - the alert is a warning. */
    const BANNERALERT_LEVEL_WARNING = 'error';

    /** @var string Banner alert level - the alert is an announcement. */
    const BANNERALERT_LEVEL_ANNOUNCEMENT = 'success';

    /** @var string Banner alert target - the alert appears on the Dashboard page only. */
    const BANNERALERT_TARGET_DASHBOARD = 'dashboard';
    /** @var string Banner alert target - the alert appears on every page of the site. */
    const BANNERALERT_TARGET_SITEWIDE = '0';

    /** @var string The key to look up banner alerts in the user session. */
    const BANNERALERT_SESSION_KEY = 'alerts';
    /** @var int Banner alert index key for events appearing on Sunday. */
    const BANNERALERT_WEEKDAYS_SUNDAY = 0;

    /** @var int Banner alert index key for events appearing on Monday. */
    const BANNERALERT_WEEKDAYS_MONDAY = 1;

    /** @var int Banner alert index key for events appearing on Tuesday. */
    const BANNERALERT_WEEKDAYS_TUESDAY = 2;

    /** @var int Banner alert index key for events appearing on Wednesday. */
    const BANNERALERT_WEEKDAYS_WEDNESDAY = 3;

    /** @var int Banner alert index key for events appearing on Thursday. */
    const BANNERALERT_WEEKDAYS_THURSDAY = 4;

    /** @var int Banner alert index key for events appearing on Friday. */
    const BANNERALERT_WEEKDAYS_FRIDAY = 5;

    /** @var int Banner alert index key for events appearing on Saturday. */
    const BANNERALERT_WEEKDAYS_SATURDAY = 6;
}
