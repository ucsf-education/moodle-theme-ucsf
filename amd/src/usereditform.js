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
//

/**
 * Kludges a link and some helpful text into the two user profile edit forms
 * that provide the user with some relevant information as to how/where
 * they can properly update their name information.
 *
 * @module theme_ucsf
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import jQuery from 'jquery';

export const init = () => {
    let $elem = jQuery('#page-user-editadvanced #fitem_id_firstname'); // Admin user profile form

    if (!$elem.length) {
        $elem = jQuery('#page-user-edit #fitem_id_firstname'); // User profile form
    }
    if ($elem.length) {
        $elem.prepend('<div class="col-md-3"></div><div class="col-md-9">'
            + '<a href="https://it.ucsf.edu/how-to/how-update-your-name" target="_blank">How to update name information.</a>'
            + '</div>');
    }
};