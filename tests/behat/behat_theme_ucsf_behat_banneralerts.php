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
 * Theme UCSF - custom Behat rules for banner alerts.
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Step\Then;
use Behat\Transformation\Transform;

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Steps definitions for testing banner alerts.
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_ucsf_behat_banneralerts extends behat_base {
    /**
     * Asserts that a banner alert of a given type with a given text is present on the page.
     *
     * @param bool $isdismissible Whether the given alert is dismissible or not.
     * @param string $text The alert text.
     * @param string $typeclass The alert type.
     */
    #[Then('/^I should see the (dismissible|non-dismissible) "([^"]+)" (announcement|info|warning) banner$/')]
    public function i_should_see_the_banner(bool $isdismissible, string $text, string $typeclass): void {
        $xpath = $this->get_xpath_to_banner($isdismissible, $text, $typeclass);
        $this->execute("behat_general::should_exist", [$this->escape($xpath), "xpath_element"]);
    }

    /**
     * Asserts that a banner alert of a given type with the given text is not present on the page.
     *
     * @param bool $isdismissible Whether the given alert is dismissible or not.
     * @param string $text The alert text.
     * @param string $typeclass The alert type.
     */
    #[Then("/^I shouldn't see the (dismissible|non-dismissible) \"([^\"]+)\" (announcement|info|warning) banner$/")]
    public function i_shouldnt_see_the_banner(bool $isdismissible, string $text, string $typeclass): void {
        $xpath = $this->get_xpath_to_banner($isdismissible, $text, $typeclass);
        $this->execute("behat_general::should_not_exist", [$this->escape($xpath), "xpath_element"]);
    }

    /**
     * Transforms the 'dismissible' keyword into TRUE and 'non-dismissible' into FALSE.
     *
     * @param string $str The keyword
     * @return bool TRUE if the given word was 'dismissible', otherwise FALSE.
     */
    #[Transform('/^(dismissible|non-dismissible)$/')]
    public function transform_dismissible_keywords_to_boolean(string $str): bool {
        return 'dismissible' === $str;
    }

    /**
     * Maps the given alert type to a CSS class.
     *
     * @param string $str The alert type.
     * @return string The mapped CSS class.
     */
    #[Transform('/^(announcement|info|warning)$/')]
    public function transform_alert_types_to_css_class_names(string $str): string {
        return match ($str) {
            'announcement' => 'alert-warning',
            'info' => 'alert-info',
            'warning' => 'alert-danger',
        };
    }

    /**
     * Dismisses a banner alert with the given text.
     * @param string $text The alert text.
     */
    #[Then('/^I dismiss the "([^"]+)" banner$/')]
    public function i_dismiss_the_banner(string $text): void {
        $xpath = "//div[contains(@class, 'ucsf-banneralerts-alert') and text()[normalize-space()='{$text}']]";
        $xpath .= '/button';
        $node = $this->find("xpath", $this->escape($xpath));
        if (!$node) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), selector: $xpath);
        }
        $node->click();
    }

    /**
     * Builds a xpath expression for finding a banner alert on the page by the given parameters.
     *
     * @param bool $isdismissible Whether the alert is dismissible or not.
     * @param string $text The alert text.
     * @param string $typeclass The alert type CSS class.
     * @return string The generated xpath expression.
     */
    protected function get_xpath_to_banner(bool $isdismissible, string $text, string $typeclass): string {
        $rhett = "//div[contains(@class, 'ucsf-banneralerts-alert')";
        $rhett .= " and contains(@class, '{$typeclass}')";
        if ($isdismissible) {
            $rhett .= " and contains(@class, 'alert-dismissible')";
        } else {
            $rhett .= " and not(contains(@class, 'alert-dismissible'))";
        }
        // Since the alert element may contain a button (if its dismissible),
        // we'll have to ignore the button's text when filtering for the alert's actual text.
        // We also have to trim whitespace in order to get an exact match on it.
        $rhett .= " and text()[normalize-space()='{$text}']";
        $rhett .= ']';
        return $rhett;
    }
}
