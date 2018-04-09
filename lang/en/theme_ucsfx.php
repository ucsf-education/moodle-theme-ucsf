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
 * Language file.
 *
 * @package   theme_ucsfx
 * @copyright 2018 The Regents of the University of California
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['advancedsettings'] = 'Advanced settings';
$string['backgroundimage'] = 'Background image';
$string['backgroundimage_desc'] = 'The image to display as a background of the site. The background image you upload here will override the background image in your theme preset files.';
$string['brandcolor'] = 'Brand colour';
$string['brandcolor_desc'] = 'The accent colour.';
$string['choosereadme'] = 'UCSFX is a Boost-based Moodle theme.';
$string['currentinparentheses'] = '(current)';
$string['configtitle'] = 'UCSFX';
$string['generalsettings'] = 'General settings';
$string['pluginname'] = 'UCSFX';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files, and see the <a href=http://moodle.net/boost>Presets repository</a> for presets that others have shared.';
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
$string['privacy:metadata'] = 'The UCSFX theme does not store any personal data about any user.';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
$string['region-side-pre'] = 'Right';

/* Help/Feedback */
$string['helpfeedbacksettings'] = 'Help/Feedback settings';
$string['helpfeedback'] = 'Help/Feedback';
$string['helpfeedbacktitle'] = 'Enter Help/Feedback title';
$string['helpfeedbacktitledesc'] = 'Enter desired text for Help/Feedback button';
$string['helpfeedbacknumlinks'] = 'Select number of links';
$string['helpfeedbackenabled'] = 'Enable Help/Feedback menu';
$string['helpfeedbackenableddesc'] = ' Click to enable Help/Feedback menu';
$string['helpfeedbackno'] = 'Help/Feedback {$a->help}';
$string['helpfeedbacklinklabel'] = "Enter label name";
$string['helpfeedbacklinklabeldesc'] = "Custom label name";
$string['helpfeedbacklink'] = 'Help/Feedback link to a static page';
$string['helpfeedbacklinkdesc'] = 'Help/Feedback link to a static page. Example: http://www.ucsf.edu/static/help.html';
$string['helpfeedbacklinktarget'] = 'Open Help/Feedback link in new window';
$string['helpfeedbacklinktargetdesc'] = 'Should Help/Feedback link opens in a new window';

// Help menu popup
$string['showhelpmenuwindow'] = 'Show help menu window';
$string['hidehelpmenuwindow'] = 'Hide help menu window';
$string['togglehelpmenu'] = 'Toggle help menu';
$string['helpmenuwindow'] = 'Help menu window';
$string['helpmenu'] = 'Help menu';
$string['helpmenutitle'] = 'Help/Feedback';

/* Alerts */
$string['alertsheading'] = 'User Alerts';
$string['alertsheadingsub'] = 'Display important messages to your users on the frontpage';
$string['alertsdesc'] = 'This will display an alert (or multiple) in three different styles to your users on the Moodle frontpage. Please remember to disable these when no longer needed.';

$string['enablealert'] = 'Enable Alert';
$string['enablealertdesc'] = 'Enable or disable alerts';

$string['oneTimeStartEndDateError'] = 'Could not update the alert! It is set to end before it starts!';
$string['oneTimeStartEndTimeError'] = 'Could not update the alert! It is set to end before it starts!';
$string['empdyDateFieldError'] = 'Could not update the alert! The date field cannot be empty!';


$string['alert1'] = 'Alert One';
$string['alert2'] = 'Alert Two';
$string['alert3'] = 'Alert Three';
$string['alert4'] = 'Alert Four';
$string['alert5'] = 'Alert Five';
$string['alert6'] = 'Alert Six';
$string['alert7'] = 'Alert Seven';
$string['alert8'] = 'Alert Eight';
$string['alert9'] = 'Alert Nine';
$string['alert10'] = 'Alert Ten';

$string['alerttitle'] = 'Title';
$string['alerttitledesc'] = 'Main title/heading for your alert';

$string['alerttype'] = 'Level';
$string['alerttypedesc'] = 'Set the appropriate alert level/type.';

$string['alerttext'] = 'Alert Text';
$string['alerttextdesc'] = 'What is the text you wish to display in your alert';

$string['alert_info'] = 'Information';
$string['alert_warning'] = 'Warning';
$string['alert_general'] = 'Announcement';

$string['alertbgcolor'] = 'Alert Background Color';
$string['alertbgcolordesc'] = 'Set the custom alert background color';
$string['categories_list_alert'] = 'Category';
$string['categories_list_alertdesc'] = 'Select the category/subcategory where the alert should appear.';

$string['start_date'] = 'Start/End date';
$string['start_datedesc'] = 'Set start date when you want this alert to show and end date when you want this alert to stop.';
$string['end_date'] = 'End date';
$string['end_datedesc'] = 'Set end date when you want this alert to stop showing.';

$string['recurring_alert'] = 'Recurring alert';
$string['recurring_alertdesc'] = 'Select which alert type you want to use. IMPORTANT: You must click SAVE CHANGES after changing the alert type to view the settings for the newly selected alert type.';
$string['none'] = 'None';
$string['one_time'] = 'One time';
$string['daily'] = 'Daily';
$string['weekly'] = 'Weekly';
$string['monthly'] = 'Monthly';
$string['timezone_alerts'] = 'Timezone settings';
$string['timezone_alertsdesc'] = 'Alerts are using UTC time. Set the time that you need for your alert settings.';


$string['start_date_daily'] = 'Start date: ';
$string['start_datedailydesc'] = 'Set start date when you want your alert to show up once a day.';

$string['end_date_daily'] = 'End date: ';
$string['end_date_dailydesc'] = 'Set time when you want your daily alert to end.';

$string['start_date_weekly'] = 'Start date: ';
$string['end_date_weekly'] = 'Start/End time: ';
$string['start_dateweeklydesc'] = 'Set date and time when you want your weekly alert to start.';
$string['end_weeklydesc'] = 'Set time when you want your weekly alert to start/end.';
$string['start_date_monthly'] = 'Set monthly alert: ';
$string['end_date_monthly'] = 'End monthly alert: ';
$string['start_datemonthlydesc'] = 'Set time when you want your monthly alert to start.';
$string['end_monthlydesc'] = 'Set time when you want your monthly alert to end.';

$string['start_hour_and_minute_daily'] = 'Start time: ';
$string['start_hour_and_minute_dailydesc'] = 'Set hour and minutes when you want your daily alert to start/end.';
$string['end_hour_and_minute_daily'] = 'End time: ';
$string['end_hour_and_minute_dailydesc'] = 'Set hour and minutes when you want your daily alert to end.';

$string['start_hour'] = 'Hour';
$string['start_minute'] = 'Minute';
$string['end_hour'] = 'End hour';
$string['end_minute'] = 'End minute';

$string['show_week_day'] = 'Set day';
$string['show_week_daydesc'] = 'Set day in a week when you want your alert to show.';
$string['show_month'] = 'Set month';
$string['show_monthdesc'] = 'Set month that you want your alert to show.';


$string['never_end'] = 'Never end';
$string['number_of_alerts'] = 'Number of alerts';
$string['number_of_alertsdesc'] = 'Set number of alert you want to have.';
