<?php

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

    /**
     * @inheritdoc
     */
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

    /**
     * Returns a help menu.
     *
     * @return string HTML The help menu, or a blank string.
     */
    public function help_menu()
    {
        global $PAGE, $COURSE;

        if (! isloggedin()) {
            return '';
        }

        $menu = theme_ucsf_get_helpmenu($PAGE, $COURSE);

        if (! empty($menu)) {
            return $this->render_from_template('theme_ucsf/helpmenu_popover', $menu);
        }

        return '';

    }
}
