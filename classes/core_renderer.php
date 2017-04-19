<?php

/**
 * UCSF theme core renderer.
 *
 * @package theme_ucsf
 */

require_once($CFG->dirroot . '/theme/clean/classes/core_renderer.php');
require_once($CFG->dirroot . '/theme/ucsf/lib.php');

class theme_ucsf_core_renderer extends theme_clean_core_renderer
{
    /**
     * Returns a help menu.
     *
     * @return string The help menu HTML, or a blank string.
     */
    public function help_menu()
    {
        if (!isloggedin()) {
            return '';
        }

        $theme_settings = $this->page->theme->settings;

        $menu = false;

        if ($theme_settings->enablecustomization) {
            $current_category = $this->get_current_course_category();

            if (!empty($current_category)) {
                $parent_categories = $this->get_category_roots($current_category);
                while (!$menu && !empty($parent_categories)) {
                    $category = array_shift($parent_categories);
                    $menu = $this->get_category_helpmenu($category);
                }
            }

            if (!$menu) {
                $menu = $this->get_default_helpmenu();
            }
        } else {
            $menu = $this->get_default_helpmenu();
        }


        if (!empty($menu)) {
            return $this->render_from_template('theme_ucsf/helpmenu_popover', $menu);
        }

        return '';

    }

    /**
     * Returns the default help menu data.
     *
     * @return array|bool
     */
    protected function get_default_helpmenu()
    {
        $theme_settings = $this->page->theme->settings;
        if (!$theme_settings->enablehelpfeedback) {
            return false;
        }

        $menu = array();

        $title = $theme_settings->helpfeedbacktitle;
        $menu['title'] = empty($title) ? get_string('helpmenutitle', 'theme_ucsf') : $title;

        $menu['items'] = array();
        $number_of_links = (int)$theme_settings->numberoflinks;
        for ($i = 1; $i <= $number_of_links; $i++) {
            $url = $this->get_theme_setting('helpfeedback' . $i . 'link');
            $title = $this->get_theme_setting('helpfeedback' . $i . 'linklabel');
            $target = $this->get_theme_setting('helpfeedback' . $i . 'linktarget');

            if (!empty($url)) {
                $menu['items'][] = array(
                    'url' => $url,
                    'title' => empty($title) ? '' : $title,
                    'options' => array(
                        'target' => empty($target) ? '_self' : '_blank'
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
     * @param int $category The course category id.
     * @return array|bool
     */
    protected function get_category_helpmenu($category)
    {
        if (!$this->get_theme_setting('catenablehelpfeedback' . $category)) {
            return false;
        }

        $menu = array();

        $title = $this->get_theme_setting('cathelpfeedbacktitle' . $category);
        $menu['title'] = empty($title) ? get_string('helpmenutitle', 'theme_ucsf') : $title;

        $menu['items'] = array();
        $number_of_links = (int)$this->get_theme_setting('catnumberoflinks' . $category);
        for ($i = 1; $i <= $number_of_links; $i++) {
            $url = $this->get_theme_setting('cathelpfeedback' . $i . 'link' . $category);
            $title = $this->get_theme_setting('cathelpfeedback' . $i . 'linklabel' . $category);
            $target = $this->get_theme_setting('cathelpfeedback' . $i . 'linktarget' . $category);

            if (!empty($url)) {
                $menu['items'][] = array(
                    'url' => $url,
                    'title' => empty($title) ? '' : $title,
                    'options' => array(
                        'target' => empty($target) ? '_self' : '_blank'
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
     * @inheritdoc
     */
    protected function get_home_ref($returnlink = true)
    {

        $theme_settings = $this->page->theme->settings;

        // @todo integrate category overrides [ST 2017/04/18]

        $sitename = format_string($theme_settings->headerlabel, true, array('context' => context_course::instance(SITEID)));

        if ($returnlink) {
            return html_writer::link(new moodle_url('/'), $sitename, array('class' => 'brand', 'title' => get_string('home')));
        }

        return html_writer::tag('span', $sitename, array('class' => 'brand'));
    }

    /**
     * @param string $setting
     * @return mixed
     *
     * @see theme_ucsf_get_setting()
     */
    protected function get_theme_setting($setting)
    {
        return theme_ucsf_get_setting($setting);
    }

    /**
     * @return int
     *
     * @see theme_ucsf_get_current_course_category()
     */
    protected function get_current_course_category()
    {
        global $COURSE;
        return theme_ucsf_get_current_course_category($this->page, $COURSE);
    }


    /**
     * @param int $id
     * @return array
     *
     * @see theme_ucsf_get_category_roots()
     */
    protected function get_category_roots($id)
    {
        return theme_ucsf_get_category_roots($id);
    }

    /**
     * @param array $category_hierarchy
     * @param string $config_key_prefix
     * @return int
     *
     * @see theme_ucsf_find_first_configured_category()
     */
    protected function find_first_configured_category(array $category_hierarchy, $config_key_prefix)
    {
        $theme_settings = $this->page->theme->settings;
        return theme_ucsf_find_first_configured_category($theme_settings, $category_hierarchy, $config_key_prefix);
    }
}
