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

namespace theme_ucsfx\output;

defined('MOODLE_INTERNAL') || die;

/**
 * @package   theme_ucsfx
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

        return $this->render_from_template('theme_ucsfx/helpmenu_popover', $menu);
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
        $context->url    = $CFG->wwwroot.'/theme/ucsfx/alert.php';

        return $this->render_from_template('theme_ucsfx/custom_alerts', $context);
    }
}
