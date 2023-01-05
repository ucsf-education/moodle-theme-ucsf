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

namespace theme_ucsf;

use moodle_page;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Help menu.
 *
 * @package theme_ucsf
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helpmenu implements renderable, templatable {

    protected stdClass $theme_settings;

    /**
     * @param moodle_page $page
     */
    public function __construct(moodle_page $page) {
        $this->theme_settings = $page->theme->settings;
    }

    /**
     * Retrieve menu items.
     *
     * @return array menu items. Empty if no menu items are configured or if the menu is disabled.
     */
    public function export_for_template(renderer_base $output): stdClass {
        if (!$this->show_menu()) {
            return array();
        }

        $menu = new stdClass();
        $menu->items = [];

        $number_of_items = $this->get_number_of_items();

        for ($i = 1; $i <= $number_of_items; $i++) {
            $url = _theme_ucsf_get_setting($this->theme_settings, 'helpfeedback' . $i . 'link', '');
            $title = _theme_ucsf_get_setting($this->theme_settings, 'helpfeedback' . $i . 'linklabel', '');
            $target = _theme_ucsf_get_setting($this->theme_settings, 'helpfeedback' . $i . 'linktarget');
            if (! empty($url)) {
                $menu->items[] = array(
                        'url' => $url,
                        'title' => $title,
                        'target' => empty($target) ? '_self' : '_blank',
                );
            }
        }

        return $menu;
    }

    /**
     * Retrieves the number of configured help menu items.
     * @return int
     */
    protected function get_number_of_items(): int {
        return (int) _theme_ucsf_get_setting($this->theme_settings, 'numberoflinks', 0);
    }

    /**
     * Determine if the help menu should be shown.
     * @return bool
     */
    protected function show_menu(): bool {
        if (! _theme_ucsf_get_setting($this->theme_settings, 'helpfeedbackenabled')) {
            return false;
        }
        $number_of_items = $this->get_number_of_items();

        if (! $number_of_items) {
            return false;
        }

        // check if at least one of the menu items actually contains a link
        for ($i = 1; $i <= $number_of_items; $i++) {
            $url = _theme_ucsf_get_setting($this->theme_settings, 'helpfeedback' . $i . 'link', '');
            if (!empty($url)) {
                return true;
            }
        }

        return false;
    }
}
