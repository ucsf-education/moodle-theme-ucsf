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
namespace theme_ucsf\utils;

use coding_exception;
use dml_exception;
use moodle_database;
use moodle_page;
use stdClass;

/**
 * Utility class for handling course-category specific configurations.
 *
 * @package theme_ucsf
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecategory {

    /**
     * Retrieves the current course category id from either the current page, the current course, or the HTTP request.
     * This is hinky AF. [ST, since 2016]
     *
     * @return string The course category id.
     * @throws coding_exception
     * @global moodle_page $PAGE The current page object
     */
    public static function get_current_category_id(): string {
        global $PAGE;
        // For course category pages, peel the category id out of the HTTP request.
        // In all other cases, take it from the current course.
        if ('coursecategory' === $PAGE->pagelayout) {
            $categoryid = optional_param('categoryid', 0, PARAM_INT);
            if (0 !== $categoryid) {
                return (string) $categoryid; // Cast it back to string.
            }
        }
        return $PAGE->course->category;
    }

    /**
     * Returns a reversed hierarchy of course categories, starting from the given category going up to the top-level category.
     * The first element in that list is the given course category id itself, followed by its parent,
     * the parent's parent, and so on.
     *
     * @global moodle_database $DB
     * @param string $id The category id.
     * @return array A list of category ids, will be empty if the given category cannot be found.
     * @throws dml_exception
     */
    public static function get_reverse_category_hierarchy(string $id): array {
        global $DB;
        static $cache = null;

        if (! isset($cache)) {
            $cache = [];
        }

        if (! array_key_exists($id, $cache)) {
            $category = $DB->get_record('course_categories', ['id' => $id]);
            if (false === $category) {
                return [];
            }
            $ids = array_reverse(explode('/', trim( $category->path, '/')));
            $cache[$id] = $ids;
        }

        return $cache[$id];
    }

    /**
     * Find and returns the first ancestor in the ancestry hierarchy of a given course category
     * that has a theme setting with starting with the given theme.
     *
     * Example:
     *  1. The category hierarchy for the given category (id = "7") is "7" > "6" > "5" > "1". (from bottom- to the top-category).
     *  2. We're searching the theme settings for all entries pertaining to custom labels
     *     (all config settings starting with "customlabel").
     *  3. The theme settings contains entries with the names "customlabel5" and "customlabel1".
     *  4. This method will return "5", since "customlabel5" is the first matching setting from the bottom of the hierarchy.
     *
     * @param string $category_id The course category id
     * @param string $setting_name_prefix Settings name prefix
     * @return string The first matching category id, or an empty string if no matching setting could be found.
     * @see coursecategory::get_reverse_category_hierarchy()
     * @throws dml_exception
     */
    public static function find_category_id_by_config_setting(string $categoryid, string $settingnameprefix): string {
        // Get the reverse course category tree for the given category.
        $categoryhierarchy = self::get_reverse_category_hierarchy($categoryid);

        if (empty($categoryhierarchy)) {
            return ''; // Abort mission if there's no hierarchy, most likely b/c the category itself cannot be found.
        }

        // Get a list of course category IDs that have been configured for customizations.
        // Note that this isn't the same as *enabled* customizations, it just means that they've been flagged as "customizable".
        $customizedcategories = trim(config::get_setting('all_categories', ''));
        if ('' === $customizedcategories) {
            return '';
        }
        $customizedcategories = explode(',', $customizedcategories);

        // Find first matching config setting and return the id of the category that it matched with.
        foreach ($categoryhierarchy as $categoryid) {
            if (in_array($categoryid, $customizedcategories)) {
                $settingname = $settingnameprefix . $categoryid;
                if ('' !== trim(config::get_setting($settingname, ''))) {
                    return $categoryid;
                }
            }
        }

        return '';
    }
}
