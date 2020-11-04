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

use context_course;
use custom_menu;
use moodle_url;
use html_writer;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * @package   theme_ucsf
 * @copyright 2018 The Regents of the University of California
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer
{
    /**
     * Returns a help menu.
     *
     * @param \stdClass $menu
     *
     * @return string The help menu HTML, or a blank string if the given menu data is empty.
     * @throws \moodle_exception
     */
    public function help_menu(\stdClass $menu = null)
    {
        if (empty($menu) || empty($menu->items)) {
            return '';
        }

        return $this->render_from_template('theme_ucsf/helpmenu_popover', $menu);
    }

    /**
     * Returns the custom alerts.
     *
     * @param array $alerts
     *
     * @return string The custom alerts HTML, or a blank string if no alerts were given.
     * @throws \moodle_exception
     */
    public function custom_alerts($alerts = array())
    {
        global $CFG;

        if (empty($alerts)) {
            return '';
        }

        $context         = new \stdClass();
        $context->alerts = $alerts;
        $context->url    = $CFG->wwwroot.'/theme/ucsf/alert.php';

        return $this->render_from_template('theme_ucsf/custom_alerts', $context);
    }

    /**
     * @inheritdoc
     */
    public function custom_menu($custommenuitems = '') {

        if (empty($custommenuitems)) {
            return '';
        }

        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    /**
     * We want to show the custom menus as a list of links for mobile devices/smaller screens.
     * Just return the menu object exported so we can render it differently.
     * @param string $custommenuitems
     * @return \stdClass|null
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function custom_menu_mobile($custommenuitems = '') {
        if (empty($custommenuitems)) {
            return null;
        }

        $custommenu = new custom_menu($custommenuitems, current_language());
        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $custommenu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        return $custommenu->export_for_template($this);
    }

    /**
     * Returns the rendered markup for the page header brand. This includes the header title and logo.
     * @param array $brand assoc. array with items 'logo', 'link' and 'title'.
     * @return string
     */
    public function header_brand(array $brand) {

        $logo = $brand['logo'];
        $title = $brand['title'];
        $link = $brand['link'];

        $out = '';
        if (! empty($logo['src'])) {
            $out .= html_writer::span(
                html_writer::img($logo['src'], $title, array('title' => $logo['title'])),
                'logo'
            );
        }

        $out .= html_writer::span($title, 'site-name d-none d-lg-inline');

        $classes = array('navbar-brand');
        if (! empty($logo)) {
            $classes[] = 'has-logo';
        }

        $linktarget = ! empty($logo['target']) ? $logo['target'] : '_self';

        $out = html_writer::link($link, $out, array(
            'class' => implode(' ', $classes),
            'target' => $linktarget
        ));

        return $out;
    }

    /**
     * Renders a given category label.
     *
     * @param array $label
     * @return string The rendered markup.
     */
    public function category_label(array $label) {
        $out = '';
        $title = $label['title'];
        $link = $label['link'];

        if (empty($title)) {
            return $out;
        }

        $out = $title;

        if (!empty($link)) {
            $out = html_writer::link($link, $out);
        }

        return html_writer::div($out, 'category_label pull-left');
    }

    /**
     * @inheritdoc
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function full_header() {
        global $CFG, $COURSE, $USER, $PAGE;

        /* copy/pasted from parent function */
        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($PAGE->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();

        // use overriden template instead
        $html = $this->render_from_template('theme_ucsf/header', $header);

        // Show a hint for users that view the course with guest access.
        // We also check that the user did not switch the role. This is a special case for roles that can fully access the course
        // without being enrolled. A role switch would show the guest access hint additionally in that case and this is not
        // intended
        if (is_guest(\context_course::instance($COURSE->id), $USER->id)
            && $PAGE->has_set_url()
            && $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)
            && !is_role_switched($COURSE->id)
        ) {
            $html .= html_writer::start_tag('div', array('class' => 'course-guestaccess-infobox alert alert-warning'));
            $html .= html_writer::tag('i', null, array('class' => 'fa fa-exclamation-circle fa-3x fa-pull-left'));
            $html .= get_string('showhintcourseguestaccessgeneral', 'theme_ucsf',
                array('role' => role_get_name(get_guest_role())));
            $html .= $this->get_course_guest_access_hint($COURSE->id);
            $html .= html_writer::end_tag('div');
        }


        // Check if the user did a role switch.
        // If not, adding this section would make no sense and, even worse,
        // user_get_user_navigation_info() will throw an exception due to the missing user object.
        if (is_role_switched($COURSE->id)) {
            // Get the role name switched to.
            $opts = \user_get_user_navigation_info($USER, $this->page);
            $role = $opts->metadata['rolename'];
            // Get the URL to switch back (normal role).
            $url = new moodle_url('/course/switchrole.php',
                array('id'        => $COURSE->id, 'sesskey' => sesskey(), 'switchrole' => 0,
                      'returnurl' => $this->page->url->out_as_local_url(false)));
            $html .= html_writer::start_tag('div', array('class' => 'switched-role-infobox alert alert-info'));
            $html .= html_writer::tag('i', null, array('class' => 'fa fa-user-circle fa-3x fa-pull-left'));
            $html .= html_writer::start_tag('div');
            $html .= get_string('switchedroleto', 'theme_ucsf');
            // Give this a span to be able to address via CSS.
            $html .= html_writer::tag('span', $role, array('class' => 'switched-role'));
            $html .= html_writer::end_tag('div');
            // Return to normal role link.
            $html .= html_writer::start_tag('div');
            $html .= html_writer::tag('a', get_string('switchrolereturn', 'core'),
                array('class' => 'switched-role-backlink', 'href' => $url));
            $html .= html_writer::end_tag('div'); // Return to normal role link: end div.
            $html .= html_writer::end_tag('div');

        }

        // If the visibility of the course is hidden, a hint for the visibility will be shown.
        if ($COURSE->visible == false
            && $PAGE->has_set_url()
            && $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)
        ) {
            $html .= html_writer::start_tag('div', array('class' => 'course-hidden-infobox alert alert-warning'));
            $html .= html_writer::tag('i', null, array('class' => 'fa fa-exclamation-circle fa-3x fa-pull-left'));
            $html .= get_string('showhintcoursehiddengeneral', 'theme_ucsf', $COURSE->id);
            // If the user has the capability to change the course settings, an additional link to the course settings is shown.
            if (has_capability('moodle/course:update', context_course::instance($COURSE->id))) {
                $html .= html_writer::tag('div', get_string('showhintcoursehiddensettingslink',
                    'theme_ucsf', array('url' => $CFG->wwwroot.'/course/edit.php?id='. $COURSE->id)));
            }
            $html .= html_writer::end_tag('div');
        }

        return $html;
    }

    /**
     * Build the guest access hint HTML code.
     *
     * @param int $courseid The course ID.
     *
     * @return string.
     * @throws \coding_exception
     */
    protected function get_course_guest_access_hint($courseid) {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/self/lib.php');

        $html = '';
        $instances = enrol_get_instances($courseid, true);
        $plugins = enrol_get_plugins(true);
        foreach ($instances as $instance) {
            if (!isset($plugins[$instance->enrol])) {
                continue;
            }
            $plugin = $plugins[$instance->enrol];
            if ($plugin->show_enrolme_link($instance)) {
                $html = html_writer::tag('div', get_string('showhintcourseguestaccesslink',
                    'theme_ucsf', array('url' => $CFG->wwwroot . '/enrol/index.php?id=' . $courseid)));
                break;
            }
        }

        return $html;
    }
}
