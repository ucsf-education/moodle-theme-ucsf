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
 * @package theme_ucsf
 * @copyright 2022 The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['advancedsettings'] = 'Advanced settings';
$string['alertnumber'] = 'Alert {$a}';
$string['alert_general'] = 'Announcement';
$string['alert_info'] = 'Information';
$string['alert_warning'] = 'Warning';
$string['alertsdesc'] = 'This will display an alert (or multiple) in three different styles to your users on configured target pages. Please remember to disable these when no longer needed.';
$string['alertsheading'] = 'Banner Alerts';
$string['alertsheadingsub'] = 'Display important messages to your users as banner alerts on given pages or in given course categories.';
$string['alerttext'] = 'Message';
$string['alerttextdesc'] = 'The message that this alert will display.';
$string['alerttype'] = 'Level';
$string['alerttypedesc'] = 'Set the appropriate alert level.';
$string['backgroundimage'] = 'Background image';
$string['backgroundimage_desc'] = 'The image to display as a background of the site. The background image you upload here will override the background image in your theme preset files.';
$string['brandcolor'] = 'Brand color';
$string['brandcolor_desc'] = 'The accent color.';
$string['categories_list_alert'] = 'Target';
$string['categories_list_alertdesc'] = 'Select the page or course category where the alert should appear. If you chose a course category, the alert will appear on all pages in that category and all pages of its subcategories.';
$string['categorycustomizations'] = 'Course categories';
$string['categorycustomizationsdesc'] = 'Select course categories to customize.';
$string['categorycustomizationsheading'] = 'Category Customizations';
$string['choosereadme'] = 'UCSF is a Boost-based Moodle theme.';
$string['configtitle'] = 'UCSF';
$string['daily'] = 'Daily';
$string['emptyenddate'] = 'Could not update the alert! The end date field cannot be empty!';
$string['emptystartandenddate'] = 'Could not update the alert! The start and end date fields cannot be empty!';
$string['emptystartdate'] = 'Could not update the alert! The start date field cannot be empty!';
$string['enablealert'] = 'Enable alert';
$string['enablealertdesc'] = 'Enable or disable this alert.';
$string['end_date_daily'] = 'End date';
$string['end_date_dailydesc'] = 'Set time when you want your daily alert to end.';
$string['end_date_monthly'] = 'End monthly alert';
$string['end_date_weekly'] = 'Start/End time';
$string['end_datedesc'] = 'Set end date when you want this alert to stop showing.';
$string['end_hour'] = 'End hour';
$string['end_hour_and_minute_daily'] = 'End time';
$string['end_hour_and_minute_dailydesc'] = 'Set hour and minutes when you want your daily alert to end.';
$string['end_minute'] = 'End minute';
$string['end_monthlydesc'] = 'Set time when you want your monthly alert to end.';
$string['end_weeklydesc'] = 'Set time when you want your weekly alert to start/end.';
$string['enddate'] = 'End date';
$string['generalsettings'] = 'General settings';
$string['helpfeedback'] = 'Help/Feedback';
$string['helpfeedbackenabled'] = 'Enable Help/Feedback menu';
$string['helpfeedbackenableddesc'] = ' Click to enable Help/Feedback menu';
$string['helpfeedbacklink'] = 'Help/Feedback link to a static page';
$string['helpfeedbacklinkdesc'] = 'Help/Feedback link to a static page. Example: http://www.ucsf.edu/static/help.html';
$string['helpfeedbacklinklabel'] = "Enter label name";
$string['helpfeedbacklinklabeldesc'] = "Custom label name";
$string['helpfeedbacklinktarget'] = 'Open Help/Feedback link in new window';
$string['helpfeedbacklinktargetdesc'] = 'Should Help/Feedback link opens in a new window';
$string['helpfeedbackno'] = 'Help/Feedback {$a->help}';
$string['helpfeedbacknumlinks'] = 'Select number of links';
$string['helpfeedbacksettings'] = 'Help/Feedback settings';
$string['helpfeedbacksubsectiontitle'] = 'Help and Feedback';
$string['helpmenu'] = 'Help menu';
$string['invalidenddate'] = 'Invalid end date.';
$string['invalidstartandenddate'] = 'Invalid start date and end date.';
$string['invalidstartdate'] = 'Invalid start date.';
$string['loginbackgroundimage'] = 'Login page background image';
$string['loginbackgroundimage_desc'] = 'The image to display as a background for the login page.';
$string['monthly'] = 'Monthly';
$string['never_end'] = 'Never end';
$string['none'] = 'None';
$string['one_time'] = 'One time';
$string['pluginname'] = 'UCSF';
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href="https://docs.moodle.org/dev/Boost_Presets">Boost presets</a> for information on creating and sharing your own preset files, and see the <a href="https://archive.moodle.net/boost">Presets repository</a> for presets that others have shared.';
$string['privacy:metadata'] = 'The UCSF theme does not store any personal data about any user.';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
$string['recurring_alert'] = 'Recurrence';
$string['recurring_alertdesc'] = 'Select the recurrence pattern for this alert.<br><em>Important:</em> After changing your selection, you must save this form before you can set the date- and time-specific parameters that are corresponding with your selection.';
$string['region-side-pre'] = 'Right';
$string['show_month'] = 'Set month';
$string['show_monthdesc'] = 'Set month that you want your alert to show.';
$string['show_week_day'] = 'Set day';
$string['show_week_daydesc'] = 'Set day in a week when you want your alert to show.';
$string['start_date_monthly'] = 'Set monthly alert';
$string['start_date_weekly'] = 'Start date';
$string['start_datedailydesc'] = 'Set start date when you want your alert to show up once a day.';
$string['start_datemonthlydesc'] = 'Set time when you want your monthly alert to start.';
$string['start_dateweeklydesc'] = 'Set date and time when you want your weekly alert to start.';
$string['start_hour'] = 'Start hour';
$string['start_hour_and_minute_daily'] = 'Start time';
$string['start_hour_and_minute_dailydesc'] = 'Set hour and minutes when you want your daily alert to start/end.';
$string['start_minute'] = 'Start minute';
$string['startdate'] = 'Start date';
$string['startenddate'] = 'Start/End date';
$string['startenddatedesc'] = 'Set start date when you want this alert to show and end date when you want this alert to stop.';
$string['startsbeforeitends'] = 'Could not update the alert! It is set to end before it starts!';
$string['timezone_alerts'] = 'Timezone settings';
$string['timezone_alertsdesc'] = 'Alerts are using UTC time. Set the time that you need for your alert settings.';
$string['togglehelpmenu'] = 'Toggle help menu';
$string['unaddableblocks'] = 'Unneeded blocks';
$string['unaddableblocks_desc'] = 'The blocks specified are not needed when using this theme and will not be listed in the \'Add a block\' menu.';
$string['weekly'] = 'Weekly';