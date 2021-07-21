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

$theme_settings = $PAGE->theme->settings;

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;
$helpmenu = $OUTPUT->help_menu(theme_ucsf_get_helpmenu($PAGE));
$custom_alerts = $OUTPUT->custom_alerts(theme_ucsf_get_custom_alerts($PAGE));
$custom_menu_items = theme_ucsf_get_custom_menu($PAGE);
$custom_menu = $OUTPUT->custom_menu($custom_menu_items);
$custom_menu_mobile = $OUTPUT->custom_menu_mobile($custom_menu_items);
$copyright = property_exists($theme_settings, 'copyright') ? $theme_settings->copyright : '';
$footnote = property_exists($theme_settings, 'footnote') ? $theme_settings->footnote : '';
$headerbrand = $OUTPUT->header_brand(theme_ucsf_get_header_brand($PAGE));
$categorylabel = $OUTPUT->category_label(theme_ucsf_get_category_label($PAGE));
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
    'customalerts' => $custom_alerts,
    'custommenu' => $custom_menu,
    'custommenumobile' => $custom_menu_mobile,
    'footnote' => $footnote,
    'copyright' => $copyright,
    'headerbrand' => $headerbrand,
    'categorylabel' => $categorylabel,
    'islandingpage' => false !== strpos($bodyattributes, 'page-site-index')
];

$nav = $PAGE->flatnav;
$templatecontext['flatnavigation'] = $nav;
$templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();

$PAGE->requires->js('/theme/ucsf/js/custom_alerts.js');
$PAGE->requires->js('/theme/ucsf/js/datepicker.js');
$PAGE->requires->js('/theme/ucsf/js/usereditform.js');

echo $OUTPUT->render_from_template('theme_ucsf/columns2', $templatecontext);

