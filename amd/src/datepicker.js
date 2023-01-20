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
 * Attaches a date picker to form input fields.
 *
 * @module theme_ucsf/datepicker
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import jQuery from 'jquery';
import Pikaday from 'theme_ucsf/pikaday';

export const init = () => {
    jQuery('.ucsf-datepicker').each(function(i, elem) {
        new Pikaday({
            field: elem,
            toString: function(date) {
                // @link https://stackoverflow.com/a/15764763
                const year = date.getFullYear();
                let month = (1 + date.getMonth()).toString();
                month = month.length > 1 ? month : '0' + month;
                let day = date.getDate().toString();
                day = day.length > 1 ? day : '0' + day;
                return month + '/' + day + '/' + year;
            }
        });
    });
};
