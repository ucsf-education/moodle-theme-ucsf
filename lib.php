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
 * @param $theme
 *
 * @return string
 * @throws dml_exception
 */
function theme_ucsfx_get_main_scss_content($theme) {
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

    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_ucsfx', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_ucsfx and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/ucsfx/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/ucsfx/scss/post.scss');

    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post;
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
function theme_ucsfx_get_helpmenu(moodle_page $page)
{
    $theme_settings = $page->theme->settings;

    if (! _theme_ucsfx_get_setting($theme_settings, 'helpfeedbackenabled')) {
        return null;
    }

    $menu = new \stdClass();

    $title = _theme_ucsfx_get_setting($theme_settings, 'helpfeedbacktitle', '');
    $menu->title = empty($title) ? get_string('helpmenutitle', 'theme_ucsfx') : $title;

    $menu->items = array();
    $number_of_links = (int) _theme_ucsfx_get_setting($theme_settings, 'numberoflinks', 0);
    for ($i = 1; $i <= $number_of_links; $i++) {
        $url = _theme_ucsfx_get_setting($theme_settings, 'helpfeedback' . $i . 'link', '');
        $title = _theme_ucsfx_get_setting($theme_settings, 'helpfeedback' . $i . 'linklabel', '');
        $target = _theme_ucsfx_get_setting($theme_settings, 'helpfeedback' . $i . 'linktarget');

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
function theme_ucsfx_get_custom_alerts(moodle_page $page)
{
    global $COURSE;

    $theme_settings = $page->theme->settings;

    $number_of_alerts = (int) _theme_ucsfx_get_setting($theme_settings, 'number_of_alerts');

    if (! $number_of_alerts) {
        return array();
    }

    $current_course_category = _theme_ucsfx_get_current_course_category($page, $COURSE);
    $parent_categories = _theme_ucsfx_get_category_roots($current_course_category);

    $categories = _theme_ucsfx_get_setting($theme_settings, 'all_categories', '');
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
        $alert_enabled = _theme_ucsfx_get_setting($theme_settings, 'enable' . $n . 'alert');
        if (! $alert_enabled) {
            continue;
        }

        $alert_category = _theme_ucsfx_get_setting($theme_settings, 'categories_list_alert' . $n);
        $alert_type = _theme_ucsfx_get_setting($theme_settings, 'recurring_alert' . $n);
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
                $start_date = _theme_ucsfx_get_setting($theme_settings, 'start_date' . $n);
                $start_hour = (int) _theme_ucsfx_get_setting($theme_settings, 'start_hour' . $n);
                $start_minute = (int) _theme_ucsfx_get_setting($theme_settings, 'start_minute' . $n);

                $end_date = _theme_ucsfx_get_setting($theme_settings, 'end_date' . $n);
                $end_hour = (int) _theme_ucsfx_get_setting($theme_settings, 'end_hour' . $n);
                $end_minute = (int) _theme_ucsfx_get_setting($theme_settings, 'end_minute' . $n);

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
                $start_date = _theme_ucsfx_get_setting($theme_settings, 'start_date_daily' . $n);
                $start_hour = (int) _theme_ucsfx_get_setting($theme_settings, 'start_hour_daily' . $n);
                $start_minute = (int) _theme_ucsfx_get_setting($theme_settings, 'start_minute_daily' . $n);

                $end_date = _theme_ucsfx_get_setting($theme_settings, 'end_date_daily' . $n);
                $end_hour = (int) _theme_ucsfx_get_setting($theme_settings, 'end_hour_daily' . $n);
                $end_minute = (int) _theme_ucsfx_get_setting($theme_settings, 'end_minute_daily' . $n);

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

                $week_day = (int) _theme_ucsfx_get_setting($theme_settings, 'show_week_day' . $n);

                if ($week_day !== (int) date('w', $now)) {
                    break;
                }

                $start_date = _theme_ucsfx_get_setting($theme_settings, 'start_date_weekly' . $n);
                $start_hour = (int) _theme_ucsfx_get_setting($theme_settings, 'start_hour_weekly' . $n);
                $start_minute = (int) _theme_ucsfx_get_setting($theme_settings, 'start_minute_weekly' . $n);

                $end_date = _theme_ucsfx_get_setting($theme_settings, 'end_date_weekly' . $n);
                $end_hour = (int) _theme_ucsfx_get_setting($theme_settings, 'end_hour_weekly' . $n);
                $end_minute = (int) _theme_ucsfx_get_setting($theme_settings, 'end_minute_weekly' . $n);

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
            $alert['type'] = _theme_ucsfx_get_setting($theme_settings, "alert{$id}type", 'info');
            $alert['title'] = _theme_ucsfx_get_setting($theme_settings, "alert{$id}title", '');
            $alert['text'] = _theme_ucsfx_get_setting($theme_settings, "alert{$id}text", '');
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
 * Retrieves a theme setting.
 *
 * @param stdClass $theme_settings The theme settings object
 * @param string   $setting        The name of the setting.
 * @param mixed    $default        A default value, to be used as fallback if the setting is not defined.
 *
 * @return mixed The setting's value, or the given default if the setting has not been defined.
 */
function _theme_ucsfx_get_setting($theme_settings, $setting, $default = false)
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
function _theme_ucsfx_recursively_get_category_roots($id, $categories = array())
{
    global $DB;

    $sql = "SELECT cc.parent, cc.name FROM {course_categories} cc WHERE cc.id = ?";
    $cats = $DB->get_records_sql($sql, array($id));

    if (empty($cats)) {
        return $categories;
    }

    $categories[] = $id;
    $cat = array_shift($cats);

    return _theme_ucsfx_recursively_get_category_roots($cat->parent, $categories);
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
function _theme_ucsfx_get_category_roots($id)
{
    static $cache = null;

    if (! isset($cache)) {
        $cache = array();
    }

    if (! array_key_exists($id, $cache)) {
        $ids = _theme_ucsfx_recursively_get_category_roots($id);
        $cache[$id] = _theme_ucsfx_recursively_get_category_roots($id);
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
function _theme_ucsfx_get_current_course_category(moodle_page $page, $course)
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
