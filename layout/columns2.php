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
 * A two column layout for the ucsf theme.
 *
 * @package   theme_ucsf
 * @copyright 2018 The Regents of the University of California
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$helpmenu = $OUTPUT->help_menu(theme_ucsf_get_helpmenu($PAGE));
$custom_alerts = $OUTPUT->custom_alerts(theme_ucsf_get_custom_alerts($PAGE));
$custom_menu_items = theme_ucsf_get_custom_menu($PAGE);
$custom_menu = $OUTPUT->custom_menu($custom_menu_items);
$custom_menu_mobile = $OUTPUT->custom_menu_mobile($custom_menu_items);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'helpmenu' => $helpmenu,
    'hashelpmenu' => !empty($helpmenu),
    'customalerts' => $custom_alerts,
    'hascustommenu' => !empty($custom_menu),
    'custommenu' => $custom_menu,
    'custommenumobile' => $custom_menu_mobile,
];

$templatecontext['flatnavigation'] = $PAGE->flatnav;

$PAGE->requires->js('/theme/ucsf/javascript/datepicker.js');
$PAGE->requires->js('/theme/ucsf/javascript/custom_alerts.js');

echo $OUTPUT->render_from_template('theme_ucsf/columns2', $templatecontext);

