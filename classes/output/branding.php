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
use context_system;
use dml_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_ucsf\utils\config;
use theme_ucsf\utils\coursecategory;

/**
 * Category branding.
 *
 * @package theme_ucsf
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class branding implements renderable, templatable {

    /**
     * Retrieves branding option applicable to the current course category.
     *
     * @param renderer_base $output
     * @return stdClass|bool an object containing the various branding options, or FALSE if not applicable.
     * @throws coding_exception
     * @throws dml_exception
     */
    public function export_for_template(renderer_base $output): stdClass|bool {
        $current_category_id = coursecategory::get_current_category_id();

        // get the first applicable category that has branding customizations turned on
        $applicable_course_category_id = coursecategory::find_category_id_by_config_setting(
                $current_category_id,
                'customheaderenabled',
        );

        // skip if no category can be found with enabled branding customizations
        if ('' === $applicable_course_category_id) {
            return false;
        }

        // get the name of the custom logo for the configured category
        $logo = trim(config::get_setting('headerimage' . $applicable_course_category_id, ''));

        // skip if no logo can be found for the configured category
        if ('' === $logo) {
            return false;
        }

        $brand = new stdClass();
        // @link https://moodledev.io/docs/apis/subsystems/files#generating-a-url-to-your-files
        $brand->logo = moodle_url::make_pluginfile_url(
                context_system::instance()->id,
                'theme_ucsf',
                'headerimage' . $applicable_course_category_id,
                0,
                '/',
                $logo
        );
        $brand->name = trim(config::get_setting('headerimagealt' . $applicable_course_category_id, ''));
        $brand->link = trim(config::get_setting('headerimagelink' . $applicable_course_category_id, ''));
        $brand->linktarget = trim(config::get_setting('headerimagelinktarget' . $applicable_course_category_id, '_self'));
        return $brand;
    }
}