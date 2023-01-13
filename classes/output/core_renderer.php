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

use coding_exception;
use moodle_exception;
use theme_boost\output\core_renderer as boost_core_renderer;

/**
 * Theme renderer.
 *
 * @package   theme_ucsf
 * @copyright 2023 The Regents of the University of California
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends boost_core_renderer {

    /**
     * Renders the given help menu.
     *
     * @param helpmenu $menu
     * @return string The help menu HTML, or a blank string if the given menu is empty.
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function render_helpmenu(helpmenu $menu): string {
        $obj = $menu->export_for_template($this);
        if (empty($obj->items)) {
            return '';
        }
        return $this->render_from_template('theme_ucsf/helpmenu', $obj);
    }

    /**
     * Renders the given help menu.
     *
     * @param banneralerts $banneralerts
     * @return string The banner alerts HTML, or a blank string if no alerts are given.
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function render_banneralerts(banneralerts $banneralerts): string {
        global $CFG;
        $obj = $banneralerts->export_for_template($this);
        if (empty($obj->alerts)) {
            return '';
        }

        // bolt the callback URL on to the output object
        $obj->url = $CFG->wwwroot.'/theme/ucsf/banneralerts.php';

        return $this->render_from_template('theme_ucsf/banneralerts', $obj);
    }
}
