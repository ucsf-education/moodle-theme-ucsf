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

namespace theme_ucsf\utils;

use dml_exception;

/**
 * Utility class for accessing theme configuration settings.
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config {

    /**
     * Retrieves a theme config setting.
     *
     * @param string $name The name of the setting.
     * @param mixed $default A default value.
     * @return mixed The requested config setting, or the given default if the setting cannot be found.
     * @see get_config()
     * @throws dml_exception
     */
    public static function get_setting(string $name, $default = false) {
        $config = get_config('theme_ucsf', $name);

        if (! isset($config)) {
            return $default;
        }
        return $config;
    }
}
