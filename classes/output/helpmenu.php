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

use moodle_page;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_ucsf\constants;

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
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $menu = new stdClass();
        $menu->items = [];

        if (!$this->show_menu()) {
            return $menu;
        }

        for ($i = 1; $i <= constants::THEME_UCSF_SETTING_HELPMENU_ITEMS_COUNT; $i++) {
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
     * Determine if the help menu should be shown.
     * @return bool
     */
    protected function show_menu(): bool {
        if (! _theme_ucsf_get_setting($this->theme_settings, 'helpfeedbackenabled')) {
            return false;
        }

        // check if at least one of the menu items actually contains a link
        for ($i = 1; $i <= constants::THEME_UCSF_SETTING_HELPMENU_ITEMS_COUNT; $i++) {
            $url = _theme_ucsf_get_setting($this->theme_settings, 'helpfeedback' . $i . 'link', '');
            if (!empty($url)) {
                return true;
            }
        }

        return false;
    }
}
