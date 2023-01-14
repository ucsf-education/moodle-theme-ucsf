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
 * @package theme_ucsf
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use theme_ucsf\output\helpmenu;
use theme_ucsf\utils\config;

/**
 * Serves any files associated with the theme settings.
 * @link https://moodledev.io/docs/apis/subsystems/files
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_ucsf_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logo' || $filearea === 'backgroundimage' ||
                    $filearea === 'loginbackgroundimage')) {
        $theme = theme_config::load('ucsf');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @link https://docs.moodle.org/dev/Creating_a_theme_based_on_boost
 * @return string
 */
function theme_ucsf_get_main_scss_content(theme_config $theme): string {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');

    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_photo', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_photo and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/ucsf/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/ucsf/scss/post.scss');

    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post;
}

/**
 * Output callback for injecting our help menu into the nav bar.
 *
 * @link https://docs.moodle.org/dev/Output_callbacks#render_navbar_output
 * @param renderer_base $renderer
 * @return string
 * @throws coding_exception
 */
function theme_ucsf_render_navbar_output(renderer_base $renderer): string {
    global $PAGE;
    $helpmenu = new helpmenu($PAGE);
    return $renderer->render($helpmenu);
}


/**
 * Output callback for injecting custom JS into each page.
 * @link https://docs.moodle.org/dev/Output_callbacks#before_footer
 */
function theme_ucsf_before_footer(): void {
    global $PAGE;
    $PAGE->requires->js('/theme/ucsf/js/datepicker.js');
    $PAGE->requires->js('/theme/ucsf/js/usereditform.js');
    $PAGE->requires->js('/theme/ucsf/js/banneralerts.js');
}

/**
 * Returns a list of all ancestral categories of a given category.
 * The first element in that list is the given category itself, followed by its parent, the parent's parent and so on.
 *
 * @param int $id The category id.
 *
 * @return array A list of category ids, will be empty if the given category is bogus.
 * @throws dml_exception
 */
function _theme_ucsf_get_category_roots($id)
{
    global $DB;
    static $cache = null;

    if (! isset($cache)) {
        $cache = array();
    }

    if (! array_key_exists($id, $cache)) {
        $category = $DB->get_record('course_categories', array("id" => $id));
        if (false === $category) {
            return array();
        }
        $ids = array_reverse(explode("/",trim( $category->path, "/")));
        $cache[$id] = $ids;
        array_shift($ids);
        // cache category roots of all ancestors in that category hierarchy while at it.
        for ($i = 0, $n = count($ids); $i < $n; $i++) {
            $parent_id = $ids[$i];
            if (array_key_exists($parent_id, $cache)) {
                break;
            }
            $cache[$parent_id] = array_slice($ids, $i);
        }
    }

    return $cache[$id];
}

/**
 * Retrieves the current course category id.
 *
 * @param moodle_page $page   The current page object.
 * @param stdClass    $course The current course object.
 *
 * @return string The course category id.
 */
function _theme_ucsf_get_current_course_category(moodle_page $page, $course)
{
    // ACHTUNG!
    // Unbelievably crappy code to follow.
    // For course category pages, peel the category out of the URL request parameter.
    // In all other cases, take it from the current course.
    // @todo Clean this horrid mess up [ST 2016/03/24]
    if ($page->pagelayout == "coursecategory" && isset($_REQUEST["categoryid"])) {
        return $_REQUEST["categoryid"];
    }

    return $course->category;
}

/**
 * Retrieve a list of all course category ids,
 * since Moodle's course API does not appear to provide such a method.
 *
 * @return array A list course ids, sorted by ID in descending order (newest first).
 *
 * @throws dml_exception
 */
function _theme_ucsf_get_all_category_ids()
{
    global $DB;

    $sql = "SELECT cc.id FROM {course_categories} cc ORDER BY cc.id DESC";
    $categories = array_keys($DB->get_records_sql($sql));

    return $categories;
}

/**
 * Find and returns the first category (from the bottom) in a given category hierarchy
 * that has a customized setting in a given theme.
 *
 * Example:
 *  1. The category hierarchy is (top) id = 1 >> id = 2 >> id = 5 >> id = 7 (bottom).
 *  2. We're searching the theme settings for all entries pertaining to custom labels (all config keys starting with "customlabel").
 *  3. The theme settings contains entries keyed of by 'customlabel1' an 'customlabel5'.
 *  4. This method will return 5, since 'customlabel5' matches the lowest category id = 5 in the hierarchy.
 *
 * @param object $theme_settings     The theme settings.
 * @param array  $category_hierarchy A hierarchy of category ids, sorted bottom to top.
 * @param string $config_key_prefix  Configuration settings key prefix.
 *
 * @return int The first matching category id. 0 if no matching category can be found.
 * @see _theme_ucsf_get_category_roots()
 */
function _theme_ucsf_find_first_configured_category($theme_settings, array $category_hierarchy, $config_key_prefix)
{
    // get a list of all categories that have customizations enabled.
    $enabled_categories = array();
    if (! empty($theme_settings->all_categories)) {
        $enabled_categories = explode(",", $theme_settings->all_categories);
    }

    // find first matching
    foreach ($category_hierarchy as $category_id) {
        if (in_array($category_id, $enabled_categories)) {
            $config_key = $config_key_prefix . $category_id;
            if (! empty($theme_settings->$config_key)) {
                return $category_id;
            }
        }
    }

    return 0;
}
