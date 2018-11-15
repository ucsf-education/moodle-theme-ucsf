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
 * A one column layout for the ucsf theme.
 *
 * @package   theme_ucsf
 * @copyright 2018 The Regents of the University of California
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$bodyattributes = $OUTPUT->body_attributes([]);

$helpmenu = $OUTPUT->help_menu(theme_ucsf_get_helpmenu($PAGE));
$custom_alerts = $OUTPUT->custom_alerts(theme_ucsf_get_custom_alerts($PAGE));
$custom_menu_items = theme_ucsf_get_custom_menu($PAGE);
$custom_menu = $OUTPUT->custom_menu($custom_menu_items);
$custom_menu_mobile = $OUTPUT->custom_menu_mobile($custom_menu_items);
$headerbrand = $OUTPUT->header_brand(theme_ucsf_get_header_brand($PAGE));
$categorylabel = $OUTPUT->category_label(theme_ucsf_get_category_label($PAGE));

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'helpmenu' => $helpmenu,
    'customalerts' => $custom_alerts,
    'custommenu' => $custom_menu,
    'custommenumobile' => $custom_menu_mobile,
    'headerbrand' => $headerbrand,
    'categorylabel' => $categorylabel,
];

$PAGE->requires->js('/theme/ucsf/js/custom_alerts.js');

echo $OUTPUT->render_from_template('theme_ucsf/columns1', $templatecontext);
