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
 * Callback script to flag banner alerts as "seen" in the user session.
 *
 * @package theme_ucsf
 * @copyright 2023 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php'); // Ensures that the user session is initialized.
require_once(__DIR__ . '/classes/constants.php');

require_login();

use theme_ucsf\constants;

// Get the alert id from URL parameters.
$id = required_param('id', PARAM_INT);

// Check if the id is within range.
if (1 > $id || $id > constants::BANNERALERT_ITEMS_COUNT) {
    exit;
}

// Flag the alert as "seen" by adding it to the user session.
// This flag will be checked when alerts are being considered for display, suppressing it until this session is terminated.
if (!array_key_exists(constants::BANNERALERT_SESSION_KEY, $_SESSION)) {
    $_SESSION[constants::BANNERALERT_SESSION_KEY] = [];
}
$_SESSION[constants::BANNERALERT_SESSION_KEY][$id] = true;
