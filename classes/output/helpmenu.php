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

use dml_exception;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use theme_ucsf\constants;
use theme_ucsf\utils\config;

/**
 * Help menu output component.
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helpmenu implements renderable, templatable {

    /**
     * Retrieve menu items.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws dml_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        $menu = new stdClass();
        $menu->items = [];

        if (!$this->show_menu()) {
            return $menu;
        }

        for ($i = 1; $i <= constants::HELPMENU_ITEMS_COUNT; $i++) {
            $url = trim(config::get_setting('helpfeedback' . $i . 'link', ''));
            $title = trim(config::get_setting('helpfeedback' . $i . 'linklabel', ''));
            $target = (bool) config::get_setting('helpfeedback' . $i . 'linktarget');
            if ('' !== $url && '' !== $title) {
                $menu->items[] = [
                        'url' => $url,
                        'title' => $title,
                        'target' => $target ? '_blank' : '_self',
                ];
            }
        }

        return $menu;
    }

    /**
     * Determine if the help menu should be shown.
     *
     * @return bool
     * @throws dml_exception
     */
    protected function show_menu(): bool {
        if (!config::get_setting('helpfeedbackenabled')) {
            return false;
        }

        // Check if at least one of the menu items actually contains a link.
        for ($i = 1; $i <= constants::HELPMENU_ITEMS_COUNT; $i++) {
            $url = config::get_setting('helpfeedback' . $i . 'link', '');
            if (!empty($url)) {
                return true;
            }
        }

        return false;
    }
}
