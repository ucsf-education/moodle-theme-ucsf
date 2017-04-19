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
     * @param array $menu An associative array containing the help menu data.
     * @return string The help menu HTML, or a blank string if the given menu data is empty.
     */
    public function help_menu($menu)
    {
        if (!empty($menu)) {
            return $this->render_from_template('theme_ucsf/helpmenu_popover', $menu);
        }

        return '';

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
}
