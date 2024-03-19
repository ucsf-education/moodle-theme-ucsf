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

/**
 * Theme UCSF - General custom Behat rules
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../../lib/behat/behat_base.php');


/**
 * Steps definitions base class for the UCSF theme.
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_ucsf_behat_base extends behat_base {

    /**
     * Purges theme cache and reloads the theme
     * @copyright 2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
     * @see https://github.com/moodle-an-hochschulen/moodle-theme_boost_union
     * @Given /^the theme cache is purged and the theme is reloaded$/
     */
    public function purge_theme_cache_and_reload_theme() {
        theme_reset_all_caches();
    }

    /**
     * Scroll the page to a given coordinate.
     *
     * @copyright 2016 Shweta Sharma on https://stackoverflow.com/a/39613869.
     * @Then /^I scroll page to x "(?P<posx_number>\d+)" y "(?P<posy_number>\d+)"$/
     * @param string $posx The x coordinate to scroll to.
     * @param string $posy The y coordinate to scroll to.
     * @return void
     * @throws Exception
     */
    public function i_scroll_page_to_x_y_coordinates_of_page($posx, $posy) {
        try {
            $this->getSession()->executeScript("(function(){window.scrollTo($posx, $posy);})();");
        } catch (Exception) {
            throw new Exception("Scrolling the page to given coordinates failed");
        }
    }
}
