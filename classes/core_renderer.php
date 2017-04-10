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
 * UCSF theme with the underlying Bootstrap theme.
 *
 * @package    theme
 * @subpackage UCSF
 * @author     Lambda Soulutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/theme/clean/classes/core_renderer.php');
require_once($CFG->dirroot . '/theme/ucsf/lib.php');


class theme_ucsf_core_renderer extends theme_clean_core_renderer {

    // custom menu override
    public function custom_menu($custommenuitems = '') {

        global $CFG, $COURSE, $PAGE;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        if (empty($custommenuitems)) {
            return '';
        }
        $custommenu = new custom_menu($custommenuitems, current_language());

        $categories = theme_ucsf_get_category_roots(theme_ucsf_get_current_course_category($PAGE, $COURSE));

        $coursecategory = theme_ucsf_find_first_configured_category($PAGE->theme->settings, $categories, 'custommenu');

        $themeconfig = get_config("theme_ucsf");
        $customizedmenu = "custommenu" . $coursecategory;
        $enablecustomization = $themeconfig->enablecustomization;


        if ($enablecustomization && isset($themeconfig->$customizedmenu) && !empty($themeconfig->$customizedmenu)) {
            $custommenuitems = $themeconfig->$customizedmenu;
            $custommenu = new custom_menu($custommenuitems, current_language());
        }

        return $this->render($custommenu);
    }
}
