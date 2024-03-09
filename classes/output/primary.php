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
use core\navigation\output\primary as core_primary;
use custom_menu;
use dml_exception;
use renderer_base;
use theme_ucsf\utils\config;
use theme_ucsf\utils\coursecategory;

/**
 * Primary navigation renderable.
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
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
     */
    protected function get_custom_menu(renderer_base $output): array {
        // Skip altogether if customizations are turned off.
        if ('0' === config::get_setting('enablecustomization', '0')) {
            return [];
        }

        $currentcategoryid = coursecategory::get_current_category_id();
        $applicablecoursecategoryid = coursecategory::find_category_id_by_config_setting(
                $currentcategoryid,
                'custommenu',
        );

        // Skip if no custom menu could be found at any level in the category hierarchy.
        if ('' === $applicablecoursecategoryid) {
            return [];
        }

        // Get the menu items from the theme settings.
        $custommenuitems = trim(config::get_setting('custommenu' . $applicablecoursecategoryid, ''));

        $currentlang = current_language();
        $custommenunodes = custom_menu::convert_text_to_menu_nodes($custommenuitems, $currentlang);
        $nodes = [];
        foreach ($custommenunodes as $node) {
            $nodes[] = $node->export_for_template($output);
        }

        return $nodes;
    }
}
