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
 * Finds and attaches an event handler to our banner alerts
 * that fires when the alert is dismissed by the user.
 *
 * @module theme_ucsf/banneralerts
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import jQuery from 'jquery';

export const init = () => {
    // @link https://getbootstrap.com/docs/4.0/components/alerts/#events
    jQuery('.ucsf-banneralerts-alert').on('closed.bs.alert', function(event) {
        const $elem = jQuery(event.target);
        const url = $elem.data('ucsf-banneralert-dismiss-callback-url');
        const id = $elem.data('ucsf-banneralerts-alert-id');
        jQuery.get(url, {id});
    });
};