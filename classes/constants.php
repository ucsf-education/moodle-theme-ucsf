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
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface constants {

    const HELPMENU_ITEMS_COUNT = 10;

    const BANNERALERT_ITEMS_COUNT = 10;
    const BANNERALERT_TYPE_UNBOUND = '1';
    const BANNERALERT_TYPE_DATEBOUND = '2';
    const BANNERALERT_TYPE_RECURRENCE_DAILY = '3';
    const BANNERALERT_TYPE_RECURRENCE_WEEKLY = '4';
    const BANNERALERT_LEVEL_INFORMATION = 'info';
    const BANNERALERT_LEVEL_WARNING = 'error';
    const BANNERALERT_LEVEL_ANNOUNCEMENT = 'success';
    const BANNERALERT_TARGET_DASHBOARD = 'dashboard';
    const BANNERALERT_TARGET_SITEWIDE = '0';
    const BANNERALERT_SESSION_KEY = 'alerts';
    const BANNERALERT_WEEKDAYS_SUNDAY = 0;
    const BANNERALERT_WEEKDAYS_MONDAY = 1;
    const BANNERALERT_WEEKDAYS_TUESDAY = 2;
    const BANNERALERT_WEEKDAYS_WEDNESDAY = 3;
    const BANNERALERT_WEEKDAYS_THURSDAY = 4;
    const BANNERALERT_WEEKDAYS_FRIDAY = 5;
    const BANNERALERT_WEEKDAYS_SATURDAY = 6;
}
