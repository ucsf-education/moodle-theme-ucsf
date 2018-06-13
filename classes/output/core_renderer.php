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

use custom_menu;
use moodle_url;
use html_writer;

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
                'logo hidden-xs-down'
            );
        }

        $out .= html_writer::span($title, 'site-name hidden-sm-down');

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
}
