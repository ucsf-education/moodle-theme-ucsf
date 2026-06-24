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

/**
 * Core renderer for the UCSF theme.
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
    /**
     * {@inheritDoc}
     */
    public function main_content() {
        if ('frontpage' === $this->page->pagelayout) {
            $context = [
                'frontpagehero' => $this->image_url('frontpage-hero', 'theme_ucsf'),
            ];
            $contents = $this->render_from_template('theme_ucsf/frontpage-main', $context);
            return '<div role="main">' . $this->unique_main_content_token . $contents . '</div>';
        }
        return parent::main_content();
    }
}
