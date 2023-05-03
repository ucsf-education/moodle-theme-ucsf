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

use coding_exception;
use context_coursecat;
use context_system;
use core\navigation\output\primary as core_primary;
use custom_menu;
use dml_exception;
use renderer_base;
use stdClass;
use theme_ucsf\utils\config;
use theme_ucsf\utils\coursecategory;

/**
 * Primary navigation renderable.
 *
 * @package theme_ucsf
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary extends core_primary {
    /**
     * Get the custom menu from category customizations (instead of the site-wide config settings, which is the default behavior).
     *
     * @param renderer_base $output
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @global stdClass $PAGE
     */
    protected function get_custom_menu(renderer_base $output): array {
        $nodes = [];
        $currentlang = current_language();

        $custommenuitems = trim(
            $this->get_custom_menu_nodes_for_course_catalog()
            . PHP_EOL
            . $this->get_custom_menu_nodes_by_category()
        );

        $custommenunodes = custom_menu::convert_text_to_menu_nodes($custommenuitems, $currentlang);
        foreach ($custommenunodes as $node) {
            $nodes[] = $node->export_for_template($output);
        }
        return $nodes;
    }

    protected function get_custom_menu_nodes_by_category(): string {
        // skip altogether if customizations are turned off
        if ('0' === config::get_setting('enablecustomization', '0')) {
            return '';
        }

        $current_category_id = coursecategory::get_current_category_id();
        $applicable_course_category_id = coursecategory::find_category_id_by_config_setting(
            $current_category_id,
            'custommenu',
        );

        // skip if no custom menu could be found at any level in the category hierarchy
        if ('' === $applicable_course_category_id) {
            return '';
        }

        // get the menu items from the theme settings
        return trim(config::get_setting('custommenu' . $applicable_course_category_id, ''));
    }

    protected function get_custom_menu_nodes_for_course_catalog(): string {
        global $COURSE, $PAGE;

        // abort early if no global course object exists
        if (!$COURSE) {
            return '';
        }

        // skip altogether if feature flag is turned off
        if (!config::get_setting('enablecoursecatalognavlink')) {
            return '';
        }

        // assemble and return the menu item
        $label = get_string('coursecatalog', 'theme_ucsf');
        $link = '/course/index.php?categoryid=' . coursecategory::get_current_category_id();
        $menuitem = $label . '|' . $link;

        // KLUDGE!!
        // if we're already on the coursecategory page, then we inject the link regardless.
        // @todo revisit if that's really what we want here. [ST 2023/05/03]
        if ('coursecategory' === $PAGE->pagelayout) {
            return $menuitem;
        }

        // only users with "category management" permissions in a various contexts
        // are recognized as Category Managers and will get this nav item.
        $can_manage_category = false;

        $systemcontext = context_system::instance();
        // check the system-wide context
        if (has_capability('moodle/category:manage', $systemcontext)) {
            $can_manage_category = true;
        }
        if (! $can_manage_category) {
            // check the coursecategory context for the given course
            $categorycontext = context_coursecat::instance($COURSE->category, IGNORE_MISSING);
            if ($categorycontext && has_capability('moodle/category:manage', $categorycontext)) {
                $can_manage_category = true;
            }
        }
        return $can_manage_category ? $menuitem : '';
    }
}
