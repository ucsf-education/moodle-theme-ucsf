<?php

/**
 * Special theming functions.
 *
 * @package theme_ucsf
 */

/**
 * Extra LESS code to inject.
 *
 * This will generate some LESS code from the settings used by the user.
 *
 * @param theme_config $theme The theme config object.
 *
 * @return string Raw LESS code.
 */
function theme_ucsf_extra_less($theme)
{

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
    if (empty($customized_category_ids)) {
        return '';
    }

    // generate LESS rules by category
    $contents = array();
    foreach ($all_category_ids as $category_id) {
        $category_css = [];

        // get parent categories that are enabled for css customization
        $ids = array_values(
            array_filter(
                _theme_ucsf_get_category_roots($category_id),
                function ($id) use ($customized_category_ids) {
                    return in_array($id, $customized_category_ids);
                }
            )
        );

        // Category-specific menu-style customizations.
        //
        // ACHTUNG - MINEN!
        // Keep these styles in sync with the ones defined in "style/custom.css".
        $category = _theme_ucsf_find_first_configured_category($theme_settings, $ids, 'menudivider');
        if ($category) {
            $menudivider = $theme->setting_file_url('menudivider' . $category, 'menudivider' . $category);
            $category_css[] = ".ucsf-custom-menu .category-label { background-image: url({$menudivider}); }";
        }

        // Generic custom CSS
        //
        // "inherit" any rules that may have been defined/enabled by parent categories.
        $ids = _theme_ucsf_get_category_roots($category_id);
        foreach ($ids as $id) {
            $css_key = 'customcss' . (int) $id;
            $custom_css = $theme_settings->$css_key;
            if (trim($custom_css)) {
                $category_css[] = $custom_css;
            }
        }

        // Finally, scope category specific rules with a class selector anchored of the <body> tag.
        if (! empty($category_css)) {
            $category_css = implode("\n", array_reverse($category_css));
            $contents[] = "body.category-{$category_id} {\n{$category_css}\n}";
        }
    }

    return implode("\n", $contents);
}

/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string       $css   The CSS
 * @param theme_config $theme The theme config object.
 *
 * @return string The parsed CSS The parsed CSS.
 */
function theme_ucsf_process_css($css, $theme)
{

    $replacements = array();

    $theme_settings = $theme->settings;

    // Set the background image for the logo.
    $replacements['[[setting:logo]]'] = $theme->setting_file_url('logo', 'logo');

    // Set custom CSS.
    $customcss = '';
    if ($theme_settings->customcssenabled && ! empty($theme_settings->customcss)) {
        $customcss = $theme_settings->customcss;
    }
    $replacements['[[setting:customcss]]'] = $customcss;

    // substitute placeholders
    $css = str_replace(array_keys($replacements), array_values($replacements), $css);

    return $css;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context  $context
 * @param string   $filearea
 * @param array    $args
 * @param bool     $forcedownload
 * @param array    $options
 *
 * @return bool
 */
function theme_ucsf_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array())
{
    global $DB;
    $whitelist = array('logo', 'headerimage', 'logo');

    $sql = "SELECT cc.id FROM {course_categories} cc";
    $course_categories = $DB->get_records_sql($sql);
    $prefixes = array(
        'headerimage',
        'menudivider',
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
 * Returns an object containing HTML for the areas affected by settings.
 *
 * @param theme_ucsf_core_renderer $output Pass in $OUTPUT.
 * @param moodle_page              $page   Pass in $PAGE.
 *
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 *      - copyright The copyright notice.
 *      - custom_alerts Markup containing custom alerts
 *      - custom_menu Markup containing the custom menu.
 *      - category_label Markup containing a category label, to be displayed left of the custom nav.
 *      - navbar_home Markup containing the title and logo, to be displayed in the primary header.
 */
function theme_ucsf_get_html_for_settings(theme_ucsf_core_renderer $output, moodle_page $page)
{
    global $CFG;
    $return = new stdClass();

    $return->navbarclass = '';
    if (! empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    if (! empty($page->theme->settings->logo)) {
        $return->heading = html_writer::link(
            $CFG->wwwroot,
            '',
            array('title' => get_string('home'), 'class' => 'logo')
        );
    } else {
        $return->heading = $output->page_heading();
    }

    $return->copyright = '';
    if (! empty($page->theme->settings->copyright)) {
        $return->copyright = $page->theme->settings->copyright;
    }

    $return->footnote = '';
    if (! empty($page->theme->settings->footnote)) {
        $return->footnote = $page->theme->settings->footnote;
    }

    $return->custom_alerts = _theme_ucsf_get_custom_alerts($output, $page);

    $return->custom_menu = _theme_ucsf_get_custom_menu($output, $page);

    $return->help_menu = _theme_ucsf_get_help_menu($output, $page);

    $return->category_label = _theme_ucsf_get_category_label($output, $page);

    $return->navbar_home = _theme_ucsf_get_navbar_home($output, $page);

    return $return;
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
 * Returns a list of all ancestral categories of a given category.
 * The first element in that list is the given category itself, followed by its parent, the parent's parent and so on.
 *
 * @param int $id The category id.
 *
 * @return array A list of category ids, will be empty if the given category is bogus.
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
 * Returns the fully rendered custom menu.
 *
 * @param theme_ucsf_core_renderer $output
 * @param moodle_page              $page
 *
 * @return string
 */
function _theme_ucsf_get_custom_menu(theme_ucsf_core_renderer $output, moodle_page $page)
{
    global $COURSE;

    $theme_settings = $page->theme->settings;

    $menu_items = '';

    if (_theme_ucsf_get_setting($theme_settings, 'enablecustomization')) {
        $categories = _theme_ucsf_get_category_roots(_theme_ucsf_get_current_course_category($page, $COURSE));
        $course_category = _theme_ucsf_find_first_configured_category($theme_settings, $categories, 'custommenu');
        $menu_items = _theme_ucsf_get_setting($theme_settings, "custommenu" . $course_category, '');
    }

    return $output->custom_menu($menu_items);
}

/**
 * Returns the branding logo and title for the primary header.
 *
 * @param theme_ucsf_core_renderer $output
 * @param moodle_page              $page
 *
 * @return string The rendered markup.
 */
function _theme_ucsf_get_navbar_home(theme_ucsf_core_renderer $output, moodle_page $page) {
    global $COURSE;

    $theme_settings = $page->theme->settings;

    $html = '';

    if (_theme_ucsf_get_setting($theme_settings, 'enablecustomization')) {

        // category-specific settings
        $current_category = _theme_ucsf_get_current_course_category($page, $COURSE);
        if ($current_category) {
            $parent_categories = _theme_ucsf_get_category_roots($current_category);
            $category = _theme_ucsf_find_first_configured_category($theme_settings, $parent_categories, 'customheaderenabled');
            if ($category) {
                $logo = _theme_ucsf_get_setting($theme_settings, "headerimage{$category}");
                if ($logo) {
                    $logo_image_url = $page->theme->setting_file_url("headerimage{$category}", "headerimage{$category}");
                    $logo_image_alt = _theme_ucsf_get_setting($theme_settings, "headerimagealt{$category}", '');
                    $logo_link_url = _theme_ucsf_get_setting($theme_settings, "headerimagelink{$category}", '');
                    $logo_link_title = _theme_ucsf_get_setting($theme_settings, "headerimagetitle{$category}", '');
                    $logo_link_target = _theme_ucsf_get_setting($theme_settings, "headerimagelinktarget{$category}") ? '_blank' : '_self';
                    $html = $output->navbar_home_logo($logo_image_url, $logo_image_alt, $logo_link_url, $logo_link_title, $logo_link_target);
                }
                $title = _theme_ucsf_get_setting($theme_settings, "headerlabel{$category}", '');
                $html .= $output->navbar_home_title($title);
            }
        }
    }
    // fallback to site-wide settings
    if (empty($html)) {
        $logo = _theme_ucsf_get_setting($theme_settings, "headerimage");
        if ($logo) {
            $logo_image_url = $page->theme->setting_file_url("headerimage","headerimage");
            $logo_image_alt = _theme_ucsf_get_setting($theme_settings, "headerimagealt", '');
            $logo_link_url = _theme_ucsf_get_setting($theme_settings, "headerimagelink", '');
            $logo_link_title = _theme_ucsf_get_setting($theme_settings, "headerimagetitle", '');
            $logo_link_target = _theme_ucsf_get_setting($theme_settings, "headerimagelinktarget") ? '_blank' : '_self';
            $html = $output->navbar_home_logo($logo_image_url, $logo_image_alt, $logo_link_url, $logo_link_title, $logo_link_target);
        }

        $title = _theme_ucsf_get_setting($theme_settings, "headerlabel", '');
        $html .= $output->navbar_home_title($title);
    }

    return $html;
}

/**
 * Returns a help menu.
 *
 * @param theme_ucsf_core_renderer $output
 * @param moodle_page              $page
 *
 * @return string The help menu HTML, or a blank string.
 *
 */
function _theme_ucsf_get_help_menu(theme_ucsf_core_renderer $output, moodle_page $page)
{
    global $COURSE;

    if (! isloggedin()) {
        return '';
    }

    $theme_settings = $page->theme->settings;

    $menu = false;

    if ($theme_settings->enablecustomization) {
        $current_category = _theme_ucsf_get_current_course_category($page, $COURSE);

        if (! empty($current_category)) {
            $parent_categories = _theme_ucsf_get_category_roots($current_category);
            while (! $menu && ! empty($parent_categories)) {
                $category = array_shift($parent_categories);
                $menu = _theme_ucsf_get_category_helpmenu($theme_settings, $category);
            }
        }

        if (! $menu) {
            $menu = _theme_ucsf_get_default_helpmenu($theme_settings);
        }
    } else {
        $menu = _theme_ucsf_get_default_helpmenu($theme_settings);
    }


    if (! empty($menu)) {
        return $output->help_menu($menu['items'], $menu['title']);
    }

    return '';

}

/**
 * Returns the default help menu data.
 *
 * @param stdClass $theme_settings
 *
 * @return array|bool
 */
function _theme_ucsf_get_default_helpmenu($theme_settings)
{
    if (! _theme_ucsf_get_setting($theme_settings, 'enablehelpfeedback')) {
        return false;
    }

    $menu = array();

    $title = _theme_ucsf_get_setting($theme_settings, 'helpfeedbacktitle', '');
    $menu['title'] = empty($title) ? get_string('helpmenutitle', 'theme_ucsf') : $title;

    $menu['items'] = array();
    $number_of_links = (int) _theme_ucsf_get_setting($theme_settings, 'numberoflinks', 0);
    for ($i = 1; $i <= $number_of_links; $i++) {
        $url = _theme_ucsf_get_setting($theme_settings, 'helpfeedback' . $i . 'link', '');
        $title = _theme_ucsf_get_setting($theme_settings, 'helpfeedback' . $i . 'linklabel', '');
        $target = _theme_ucsf_get_setting($theme_settings, 'helpfeedback' . $i . 'linktarget');

        if (! empty($url)) {
            $menu['items'][] = array(
                'url'     => $url,
                'title'   => $title,
                'options' => array(
                    'target' => empty($target) ? '_self' : '_blank',
                ),
            );
        }
    }

    if (empty($menu['items'])) {
        return false;
    }

    return $menu;
}

/**
 * Returns the category help menu data.
 *
 * @param stdClass $theme_settings
 * @param int      $category The course category id.
 *
 * @return array|bool
 */
function _theme_ucsf_get_category_helpmenu($theme_settings, $category)
{
    if (! _theme_ucsf_get_setting($theme_settings, 'catenablehelpfeedback' . $category)) {
        return false;
    }

    $menu = array();

    $title = _theme_ucsf_get_setting($theme_settings, 'cathelpfeedbacktitle' . $category, '');
    $menu['title'] = empty($title) ? get_string('helpmenutitle', 'theme_ucsf') : $title;

    $menu['items'] = array();
    $number_of_links = (int) _theme_ucsf_get_setting($theme_settings, 'catnumberoflinks' . $category, 0);
    for ($i = 1; $i <= $number_of_links; $i++) {
        $url = _theme_ucsf_get_setting($theme_settings, 'cathelpfeedback' . $i . 'link' . $category, '');
        $title = _theme_ucsf_get_setting($theme_settings, 'cathelpfeedback' . $i . 'linklabel' . $category, '');
        $target = _theme_ucsf_get_setting($theme_settings, 'cathelpfeedback' . $i . 'linktarget' . $category);

        if (! empty($url)) {
            $menu['items'][] = array(
                'url'     => $url,
                'title'   => $title,
                'options' => array(
                    'target' => empty($target) ? '_self' : '_blank',
                ),
            );
        }
    }

    if (empty($menu['items'])) {
        return false;
    }

    return $menu;
}

/**
 * Returns all applicable custom alerts.
 *
 * @param theme_ucsf_core_renderer $output The output renderer
 * @param moodle_page              $page   The current page
 *
 * @return string
 */
function _theme_ucsf_get_custom_alerts(theme_ucsf_core_renderer $output, moodle_page $page)
{
    global $CFG, $COURSE;

    $theme_settings = $page->theme->settings;

    $number_of_alerts = (int) _theme_ucsf_get_setting($theme_settings, 'number_of_alerts');

    if (! $number_of_alerts) {
        return '';
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

    return $output->custom_alerts($CFG->wwwroot . '/theme/ucsf/alert.php', $alerts);
}

/**
 * Retrieve a list of all course category ids,
 * since Moodle's course API does not appear to provide such a method.
 *
 * @return array A list course ids, sorted by ID in descending order (newest first).
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

/**
 * Returns the category label for the custom navigation.
 *
 * @param theme_ucsf_core_renderer $output
 * @param moodle_page              $page
 *
 * @return string The rendered label, or an empty string if n/a.
 */
function _theme_ucsf_get_category_label(theme_ucsf_core_renderer $output, moodle_page $page)
{
    global $COURSE, $CFG;

    $theme_settings = $page->theme->settings;

    if (! _theme_ucsf_get_setting($theme_settings, 'enablecustomization')) {
        return '';
    }

    $html = '';

    // category-specific label
    $current_category = _theme_ucsf_get_current_course_category($page, $COURSE);
    if ($current_category) {
        $parent_categories = _theme_ucsf_get_category_roots($current_category);
        $category = _theme_ucsf_find_first_configured_category($theme_settings, $parent_categories, 'categorylabel');
        if ($category) {
            $label_text = _theme_ucsf_get_setting($theme_settings, "categorylabel{$category}", '');
            $link_to_category = _theme_ucsf_get_setting($theme_settings, "linklabeltocategorypage{$category}");
            if ($link_to_category) {
                $link_to_category = $CFG->wwwroot . '/course/index.php?categoryid=' . $category;
            }
            $html = $output->category_label($label_text, $link_to_category);
        }
    }

    // fallback to site-wide category label
    if (empty($html)) {
        $label_text = _theme_ucsf_get_setting($theme_settings, 'toplevelcategorylabel');
        if ($label_text) {
            $html = $output->category_label($label_text);
        }
    }

    return $html;

}

/**
 * Recursively retrieve all ancestral categories for a given category, including the category itself.
 *
 * @param int   $id         The category id.
 * @param array $categories A partial list of ancestral category ids.
 *
 * @return array A list full list of ancestral category ids, including the given id itself.
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
