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

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();


/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 *
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function theme_ucsf_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array())
{
    global $DB;
    $whitelist = array('logo', 'headerimage');

    $sql = "SELECT cc.id FROM {course_categories} cc";
    $course_categories = $DB->get_records_sql($sql);
    $prefixes = array(
        'headerimage',
    );
    foreach ($course_categories as $cat) {
        foreach ($prefixes as $prefix) {
            $whitelist[] = $prefix . $cat->id;
        }
    }

    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $theme = theme_config::load('ucsf');
        if (in_array($filearea, $whitelist)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/**
 * @param $theme
 *
 * @return string
 * @throws dml_exception
 */
function theme_ucsf_get_main_scss_content($theme) {
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

    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_ucsf', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_ucsf and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/ucsf/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/ucsf/scss/post.scss');


    // get the ids of all course categories
    $all_category_ids = _theme_ucsf_get_all_category_ids();

    // get all categories that are configured for customizations
    $theme_settings = $theme->settings;
    if (empty($theme_settings->all_categories)) {
        return '';
    }
    $customized_category_ids = explode(',', $theme_settings->all_categories);
    // filter out any categories that don't have CSS customizations turned on
    $customized_category_ids = array_filter(
        $customized_category_ids,
        function ($id) use ($theme_settings) {
            $enabled_key = 'customcssenabled' . (int) $id;

            return ! empty($theme_settings->$enabled_key);
        }
    );

    $customized_category_ids = array_values($customized_category_ids);

    // generate SCSS rules by category
    $categories_scss = array();
    foreach ($all_category_ids as $category_id) {
        $category_scss = [];

        // get parent categories that are enabled for css customization
        $ids = array_values(
            array_filter(
                _theme_ucsf_get_category_roots($category_id),
                function ($id) use ($customized_category_ids) {
                    return in_array($id, $customized_category_ids);
                }
            )
        );

        // Generic custom CSS
        //
        // "inherit" any rules that may have been defined/enabled by parent categories.
        foreach ($ids as $id) {
            $scss_key = 'customcss' . (int) $id;
            if (property_exists($theme_settings, $scss_key)) {
                $custom_scss = $theme_settings->$scss_key;
                if (trim($custom_scss)) {
                    $category_scss[] = $custom_scss;
                }
            }
        }

        // Finally, scope category specific rules with a class selector anchored of the <body> tag.
        if (! empty($category_scss)) {
            $category_scss = implode("\n", array_reverse($category_scss));
            $categories_scss[] = "body.category-{$category_id} {\n{$category_scss}\n}";
        }
    }

    $categories_scss = implode("\n", $categories_scss);


    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post . "\n" . $categories_scss;
}


/**
 * Returns the help menu data as an object, or NULL if none could be if the help menu has been disabled.
 *
 * @param moodle_page $page
 *
 * @return \stdClass|null
 *
 * @throws coding_exception
 */
function theme_ucsf_get_helpmenu(moodle_page $page)
{
    $theme_settings = $page->theme->settings;

    if (! _theme_ucsf_get_setting($theme_settings, 'helpfeedbackenabled')) {
        return null;
    }

    $menu = new \stdClass();

    $title = _theme_ucsf_get_setting($theme_settings, 'helpfeedbacktitle', '');
    $menu->title = empty($title) ? get_string('helpmenutitle', 'theme_ucsf') : $title;

    $menu->items = array();
    $number_of_links = (int) _theme_ucsf_get_setting($theme_settings, 'numberoflinks', 0);
    for ($i = 1; $i <= $number_of_links; $i++) {
        $url = _theme_ucsf_get_setting($theme_settings, 'helpfeedback' . $i . 'link', '');
        $title = _theme_ucsf_get_setting($theme_settings, 'helpfeedback' . $i . 'linklabel', '');
        $target = _theme_ucsf_get_setting($theme_settings, 'helpfeedback' . $i . 'linktarget');

        if (! empty($url)) {
            $menu->items[] = array(
                'url'     => $url,
                'title'   => $title,
                'options' => array(
                    'target' => empty($target) ? '_self' : '_blank',
                ),
            );
        }
    }

    if (empty($menu->items)) {
        return null;
    }

    return $menu;
}

/**
 * Returns all applicable custom alerts.
 *
 * @param moodle_page $page The current page
 *
 * @return array
 * @throws dml_exception
 */
function theme_ucsf_get_custom_alerts(moodle_page $page)
{
    global $COURSE;

    $theme_settings = $page->theme->settings;

    $number_of_alerts = (int) _theme_ucsf_get_setting($theme_settings, 'number_of_alerts');

    if (! $number_of_alerts) {
        return array();
    }

    $current_course_category = _theme_ucsf_get_current_course_category($page, $COURSE);
    $parent_categories = _theme_ucsf_get_category_roots($current_course_category);

    $categories = _theme_ucsf_get_setting($theme_settings, 'all_categories', '');
    $categories = explode(',', $categories);

    $filtered_categories = array_values(
        array_filter(
            $categories,
            function ($category) use ($parent_categories) {
                return in_array($category, $parent_categories);
            }
        )
    );

    $now = time();
    $current_day_timestamp = strtotime("midnight", $now);

    $has_alert = array_fill(0, 10, false);

    for ($i = 0; $i < $number_of_alerts; $i++) {
        $n = $i + 1;

        // skip if alert has already been flagged as seen in this user's session.
        if (! empty($_SESSION["alerts"]["alert" . $n])) {
            continue;
        }

        // skip if this alert is not enabled
        $alert_enabled = _theme_ucsf_get_setting($theme_settings, 'enable' . $n . 'alert');
        if (! $alert_enabled) {
            continue;
        }

        $alert_category = _theme_ucsf_get_setting($theme_settings, 'categories_list_alert' . $n);
        $alert_type = _theme_ucsf_get_setting($theme_settings, 'recurring_alert' . $n);
        // check if this alert is applies site-wide,
        // or if it matches this page's course category or any of its parent categories.
        // if none of these apply, then skip this alert.
        if (! empty($alert_category)
            && (int) $current_course_category !== (int) $alert_category
            && ! in_array($alert_category, $filtered_categories)
        ) {
            continue;
        }

        // process alerts based on their type
        switch ($alert_type) {
            case '1': // never-ending alert
                $has_alert[$i] = true;
                break;
            case '2': // one-time alert
                $start_date = _theme_ucsf_get_setting($theme_settings, 'start_date' . $n);
                $start_hour = (int) _theme_ucsf_get_setting($theme_settings, 'start_hour' . $n);
                $start_minute = (int) _theme_ucsf_get_setting($theme_settings, 'start_minute' . $n);

                $end_date = _theme_ucsf_get_setting($theme_settings, 'end_date' . $n);
                $end_hour = (int) _theme_ucsf_get_setting($theme_settings, 'end_hour' . $n);
                $end_minute = (int) _theme_ucsf_get_setting($theme_settings, 'end_minute' . $n);

                if (empty($start_date || empty($end_date))) {
                    break;
                }

                $start_date = date_create($start_date);
                $end_date = date_create($end_date);

                // check again
                if (empty($start_date || empty($end_date))) {
                    break;
                }

                // set hours and minutes
                $start_date->setTime($start_hour, $start_minute);
                $end_date->setTime($end_hour, $end_minute);

                if ($start_date->getTimestamp() > $now || $end_date->getTimestamp() < $now) {
                    break;
                }

                $has_alert[$i] = true;

                break;
            case '3': // daily alert
                $start_date = _theme_ucsf_get_setting($theme_settings, 'start_date_daily' . $n);
                $start_hour = (int) _theme_ucsf_get_setting($theme_settings, 'start_hour_daily' . $n);
                $start_minute = (int) _theme_ucsf_get_setting($theme_settings, 'start_minute_daily' . $n);

                $end_date = _theme_ucsf_get_setting($theme_settings, 'end_date_daily' . $n);
                $end_hour = (int) _theme_ucsf_get_setting($theme_settings, 'end_hour_daily' . $n);
                $end_minute = (int) _theme_ucsf_get_setting($theme_settings, 'end_minute_daily' . $n);

                if (empty($start_date || empty($end_date))) {
                    break;
                }

                $start_date = date_create($start_date);
                $end_date = date_create($end_date);

                // check again
                if (empty($start_date || empty($end_date))) {
                    break;
                }

                if ($start_date->getTimestamp() > $current_day_timestamp || $end_date->getTimestamp() < $current_day_timestamp) {
                    break;
                }

                $today_start_date = new \DateTime();
                $today_start_date->setTimestamp($now);
                $today_start_date->setTime($start_hour, $start_minute);

                $today_end_date = new \DateTime();
                $today_end_date->setTimestamp($now);
                $today_end_date->setTime($end_hour, $end_minute);

                if ($today_start_date->getTimestamp() > $now || $today_end_date->getTimestamp() < $now) {
                    break;
                }

                $has_alert[$i] = true;

                break;
            case '4': // weekly alert

                $week_day = (int) _theme_ucsf_get_setting($theme_settings, 'show_week_day' . $n);

                if ($week_day !== (int) date('w', $now)) {
                    break;
                }

                $start_date = _theme_ucsf_get_setting($theme_settings, 'start_date_weekly' . $n);
                $start_hour = (int) _theme_ucsf_get_setting($theme_settings, 'start_hour_weekly' . $n);
                $start_minute = (int) _theme_ucsf_get_setting($theme_settings, 'start_minute_weekly' . $n);

                $end_date = _theme_ucsf_get_setting($theme_settings, 'end_date_weekly' . $n);
                $end_hour = (int) _theme_ucsf_get_setting($theme_settings, 'end_hour_weekly' . $n);
                $end_minute = (int) _theme_ucsf_get_setting($theme_settings, 'end_minute_weekly' . $n);

                if (empty($start_date || empty($end_date))) {
                    break;
                }

                $start_date = date_create($start_date);
                $end_date = date_create($end_date);

                // check again
                if (empty($start_date || empty($end_date))) {
                    break;
                }

                if ($start_date->getTimestamp() > $current_day_timestamp || $end_date->getTimestamp() < $current_day_timestamp) {
                    break;
                }

                $today_start_date = new \DateTime();
                $today_start_date->setTimestamp($now);
                $today_start_date->setTime($start_hour, $start_minute);

                $today_end_date = new \DateTime();
                $today_end_date->setTimestamp($now);
                $today_end_date->setTime($end_hour, $end_minute);

                if ($today_start_date->getTimestamp() > $now || $today_end_date->getTimestamp() <= $now) {
                    break;
                }

                $has_alert[$i] = true;

                break;
            default:
                // do nothing
        }
    }

    $alerts = array();
    for ($i = 0; $i < $number_of_alerts; $i++) {
        if ($has_alert[$i]) {
            $id = $i + 1;
            $alert = array();
            $alert['id'] = $id;
            $alert['type'] = _theme_ucsf_get_setting($theme_settings, "alert{$id}type", 'info');
            $alert['title'] = _theme_ucsf_get_setting($theme_settings, "alert{$id}title", '');
            $alert['text'] = _theme_ucsf_get_setting($theme_settings, "alert{$id}text", '');
            $alerts[] = $alert;
        }
    }

    // flag all applicable alerts as unseen in the user session.
    foreach ($alerts as $alert) {
        $_SESSION["alerts"]["alert" . $alert['id']] = false;
    }

    return $alerts;
}

/**
 * Returns the custom menu for a given page.
 * @param moodle_page $page
 * @return string
 * @throws dml_exception
 */
function theme_ucsf_get_custom_menu(moodle_page $page)
{
    global $COURSE;

    $theme_settings = $page->theme->settings;

    $menu_items = '';

    if (_theme_ucsf_get_setting($theme_settings, 'enablecustomization')) {
        $categories = _theme_ucsf_get_category_roots(_theme_ucsf_get_current_course_category($page, $COURSE));
        $course_category = _theme_ucsf_find_first_configured_category($theme_settings, $categories, 'custommenu');
        $menu_items = _theme_ucsf_get_setting($theme_settings, "custommenu" . $course_category, '');
    }

    return $menu_items;
}

/**
 * Returns the branding logo and title for the primary header.
 *
 * @param moodle_page $page
 *
 * @return array an associative array, containing the logo, title and link for the header brand.
 * @throws dml_exception
 */
function theme_ucsf_get_header_brand(moodle_page $page) {
    global $CFG, $COURSE;

    $theme_settings = $page->theme->settings;

    $rhett = array();

    // defaults
    $rhett['logo'] = array();
    $rhett['link'] = $CFG->wwwroot;
    $rhett['title']  = _theme_ucsf_get_setting($theme_settings, "headerlabel", '');


    if (_theme_ucsf_get_setting($theme_settings, 'enablecustomization')) {
        // category-specific settings
        $current_category = _theme_ucsf_get_current_course_category($page, $COURSE);
        if ($current_category) {
            $parent_categories = _theme_ucsf_get_category_roots($current_category);
            $category = _theme_ucsf_find_first_configured_category($theme_settings, $parent_categories, 'customheaderenabled');
            if ($category) {
                $logo = _theme_ucsf_get_setting($theme_settings, "headerimage{$category}");
                if ($logo) {
                    $rhett['logo']['src'] = $page->theme->setting_file_url("headerimage{$category}", "headerimage{$category}");
                    $rhett['logo']['alt'] = _theme_ucsf_get_setting($theme_settings, "headerimagealt{$category}", '');
                    $rhett['logo']['title'] = _theme_ucsf_get_setting($theme_settings, "headerimagetitle{$category}", '');
                    $rhett['logo']['target'] = _theme_ucsf_get_setting($theme_settings, "headerimagelinktarget{$category}") ? '_blank' : '_self';
                    $rhett['link'] = _theme_ucsf_get_setting($theme_settings, "headerimagelink{$category}", $CFG->wwwroot);

                }
                $rhett['title'] = _theme_ucsf_get_setting($theme_settings, "headerlabel{$category}", '');
            }
        }
    }

    // fallback to site-wide settings
    if (empty($rhett['logo'])) {
        $logo = _theme_ucsf_get_setting($theme_settings, "headerimage");
        if ($logo) {
            $rhett['logo']['src'] = $page->theme->setting_file_url("headerimage","headerimage");
            $rhett['logo']['alt'] = _theme_ucsf_get_setting($theme_settings, "headerimagealt", '');
            $rhett['logo']['title'] = _theme_ucsf_get_setting($theme_settings, "headerimagetitle", '');
            $rhett['logo']['target'] = _theme_ucsf_get_setting($theme_settings, "headerimagelinktarget") ? '_blank' : '_self';
            $rhett['link'] = _theme_ucsf_get_setting($theme_settings, "headerimagelink", $CFG->wwwroot);
        }
    }

    return $rhett;
}

/**
 * Returns the category label for the custom navigation.
 * @param moodle_page $page
 * @return array An associative array, holding the category label title and link. Empty array if n/a.
 * @throws dml_exception
 */
function theme_ucsf_get_category_label(moodle_page $page)
{
    global $COURSE, $CFG;

    $theme_settings = $page->theme->settings;

    $rhett = array(
        'title' => '',
        'link' => '',
    );

    if (! _theme_ucsf_get_setting($theme_settings, 'enablecustomization')) {
        return $rhett;
    }

    // category-specific label
    $current_category = _theme_ucsf_get_current_course_category($page, $COURSE);
    if ($current_category) {
        $parent_categories = _theme_ucsf_get_category_roots($current_category);
        $category = _theme_ucsf_find_first_configured_category($theme_settings, $parent_categories, 'categorylabel');
        if ($category) {
            $title = _theme_ucsf_get_setting($theme_settings, "categorylabel{$category}", '');
            $link = _theme_ucsf_get_setting($theme_settings, "linklabeltocategorypage{$category}");
            if ($link) {
                $link = $CFG->wwwroot . '/course/index.php?categoryid=' . $category;
            }

            $rhett['title'] = $title;
            $rhett['link'] = $link;
            return $rhett;
        }
    }

    // fallback to site-wide category label
    $title = _theme_ucsf_get_setting($theme_settings, 'toplevelcategorylabel');
    if ($title) {
        $rhett['title'] = $title;
    }

    return $rhett;
}

/**
 * Retrieves a theme setting.
 *
 * @param stdClass $theme_settings The theme settings object
 * @param string   $setting        The name of the setting.
 * @param mixed    $default        A default value, to be used as fallback if the setting is not defined.
 *
 * @return mixed The setting's value, or the given default if the setting has not been defined.
 */
function _theme_ucsf_get_setting($theme_settings, $setting, $default = false)
{
    if (! isset($theme_settings->$setting)) {
        return $default;
    }

    return $theme_settings->$setting;
}

/**
 * Recursively retrieve all ancestral categories for a given category, including the category itself.
 *
 * @param int $id The category id.
 * @param array $categories A partial list of ancestral category ids.
 *
 * @return array A list full list of ancestral category ids, including the given id itself.
 * @throws dml_exception
 */
function _theme_ucsf_recursively_get_category_roots($id, $categories = array())
{
    global $DB;

    $sql = "SELECT cc.parent, cc.name FROM {course_categories} cc WHERE cc.id = ?";
    $cats = $DB->get_records_sql($sql, array($id));

    if (empty($cats)) {
        return $categories;
    }

    $categories[] = $id;
    $cat = array_shift($cats);

    return _theme_ucsf_recursively_get_category_roots($cat->parent, $categories);
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
    static $cache = null;

    if (! isset($cache)) {
        $cache = array();
    }

    if (! array_key_exists($id, $cache)) {
        $ids = _theme_ucsf_recursively_get_category_roots($id);
        $cache[$id] = _theme_ucsf_recursively_get_category_roots($id);
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
