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
 * Strings for component 'theme_ucsf', language 'en'
 *
 * @package    theme
 * @subpackage UCSF
 * @author     Lambda Soulutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['choosereadme'] = '
<div class="clearfix">
<div class="well">
<h2>UCSF</h2>
<p><img class=img-polaroid src="ucsf/pix/screenshot.jpg" /></p>
</div>
<div class="well">
<h3>About</h3>
<p>UCSF is a modified Moodle bootstrap theme which inherits styles and renderers from its parent theme.</p>
<h3>Theme Credits</h3>
<p>Lambdasolutions</p>
</div></div>';

$string['configtitle'] = 'UCSF';

$string['customcss'] = 'Custom CSS';
$string['customcssdesc'] = 'Whatever CSS rules you add to this textarea will be reflected in every page, making for easier customization of this theme.';
$string['enablecustomcss'] = 'Enable CSS customisations';
$string['enablecustomcssdesc'] = 'This allows CSS customizations to be saved but not applied to the site unless checked.';

$string['footnote'] = 'Footnote';
$string['footnotedesc'] = 'Whatever you add to this textarea will be displayed in the footer throughout your Moodle site.';

$string['invert'] = 'Invert navbar';
$string['invertdesc'] = 'Swaps text and background for the navbar at the top of the page between black and white.';

$string['logo'] = 'Logo';
$string['logodesc'] = 'Please upload your custom logo here if you want to add it to the header.<br>
If the height of your logo is more than 75px add the following CSS rule to the Custom CSS box below.<br>
a.logo {height: 100px;} or whatever height in pixels the logo is.';

$string['pluginname'] = 'UCSF';

$string['region-side-post'] = 'Right';
$string['region-side-pre'] = 'Left';



$string['theme_ucsf'] = 'UCSF';

/* General settings */
$string['generalheading'] = 'General settings';
$string['generalsettings'] = 'General settings';
$string['generalsettingsdesc'] = '';

$string['categorycustomizationheading'] = 'Category Customizations';
$string['enablecustomization'] = 'Enable category customizations';
$string['enablecustomizationdesc'] = 'If "Enable category customizations" is checked, then Moodle will use these customizations. If it is NOT checked, then the regular site-level theme settings are used and all customizations here are ignored (though they remain). It is possible to configure custom categories yet not use them (if the enable checkbox is unchecked).';

$string['toplevelcategorylabel'] = 'Top-level category label';
$string['toplevelcategorylabeldesc'] = 'Top-level = Default. All other categories get this label and menu, unless customizations are specified below.';

$string['displaycoursetitle'] = 'Display course title';
$string['displaycoursetitledesc'] = 'If checked, the course title will appear on the course page for all courses, unless the course title is set NOT to display on configured categories.';

$string['hidecustommenuwhenloggedout'] = 'Hide custom menu when logged out';
$string['hidecustommenuwhenloggedoutdesc'] = 'Hide custom menu when logged out.';


/* Help/Feedback */

$string['helpfeedback'] = 'Help/Feedback';

$string['helpfeedbacktitle'] = 'Enter Help/Feedback title';
$string['helpfeedbacktitledesc'] = 'Enter desired text for Help/Feedback button';

$string['numberoflinks'] = 'Select number of links';

$string['enablehelpfeedback'] = 'Enable Help/Feedback menu';
$string['enablehelpfeedbackdesc'] = ' Click to enable Help/Feedback menu';

$string['helpfeedbackno'] = 'Help/Feedback {$a->help}';
$string['cathelpfeedbackno'] = 'Category Help/Feedback {$a->cathelp}';

$string['helpfeedbacklinklabel'] = "Enter label name";
$string['helpfeedbacklinklabeldesc'] = "Custom label name";

$string['helpfeedbacklink'] = 'Help/Feedback link to a static page';
$string['helpfeedbacklinkdesc'] = 'Help/Feedback link to a static page. Example: http://www.ucsf.edu/static/help.html';
$string['helpfeedbacklinktarget'] = 'Open Help/Feedback link in new window';
$string['helpfeedbacklinktargetdesc'] = 'Should Help/Feedback link opens in a new window';

$string['copyright'] = 'Copyright';
$string['copyrightdesc'] = 'UCSF Copyright.';


/* General settings */
$string['blockheading'] = 'Block settings';
$string['block_width_desktop_heading'] = 'Block width settings for large desktop.';
$string['block_width_desktop'] = 'Desktop';
$string['block_width_desktopdesc'] = 'Set the width of block for screens over 1200px width. Please enter only width number.';
$string['block_width_tablet_heading'] = 'Block width for portrait tablet to landscape and desktop.';
$string['block_width_portrait_tablet'] = 'Tablet';
$string['block_width_portrait_tabletdesc'] = 'Set the width of block for tablet screen resolution (770px - 1199px). Please enter only width number.';

/* Category Customizations */
$string['addcategorycustomizationheading'] = 'Add category';
$string['addcategorycustomizationheadingdesc'] = '';

$string['categorieslist'] = 'Add category / subcategory';
$string['categorieslistdesc'] = "Adds category for customization. If category is already added to customization, it won't be displayed in the list.";

$string['removecategorycustomizationheading'] = 'Remove category';
$string['removecategorycustomizationheadingdesc'] = '';

$string['removecategorieslist'] = 'Remove category / subcategory';
$string['removecategorieslistdesc'] = 'List of all categories / subcategories currently available for customization / removal';


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
/* end of alerts */


/* Tiles & Banner*/
$string['tileheading'] = 'Tiles';
$string['tileheadingsub'] = 'Six locations on the front page to add information and links';

$string['tilecontent'] = 'Content';
$string['tilecontentdesc'] = 'Content to display in the marketing box. Keep it short and sweet.';

$string['tile'] = 'Tile {$a->help}';
$string['numberoftiles'] = 'Select number of tiles';

$string['positionoftile'] = 'Select Tile Position';
$string['positionoftiledesc'] = 'Put tile to desirable spot';

$string['tileselect'] = 'Show this tile';
$string['tileselectdesc'] = 'When selected, tile is going to show';

$string['tileimage'] = 'Tile image';
$string['tileimagedesc'] = 'Tile image to display in the tile box at the homepage';

$string['tileimagealt'] = 'Tile image alt';
$string['tileimagealtdesc'] = 'HTML image alt Attribute';

$string['tileimagetitle'] = 'Tile image title';
$string['tileimagetitledesc'] = 'HTML image title Attribute';

$string['banner'] = 'Banner';
$string['bannerdesc'] = 'Content to display in the banner box at the homepage';

$string['bannerimage'] = 'Banner image';
$string['bannerimagedesc'] = 'Banner image to display in the banner box at the homepage';

$string['bannerimagealt'] = 'Banner image alt';
$string['bannerimagealtdesc'] = 'HTML image alt Attribute';

$string['bannerimagetitle'] = 'Banner image title';
$string['bannerimagetitledesc'] = 'HTML image title Attribute';
/* end of tiles */



$string['categorylabel'] = 'Category label';
$string['categorylabeldesc'] = 'Category label';

$string['categorylabelimage'] = 'Category label image';
$string['categorylabelimagedesc'] = 'Category label image to be displayed in the category label box';

$string['categorylabelimageheight'] = 'Category label image height';
$string['categorylabelimageheightdesc'] = 'Category label image height in pixels, eg.: 40px.';

$string['categorylabelimagealt'] = 'Category label image alt';
$string['categorylabelimagealtdesc'] = 'Category label image HTML alt Attribute';

$string['categorylabelimagetitle'] = 'Category label image title';
$string['categorylabelimagetitledesc'] = 'Category label image HTML title Attribute';

$string['linklabeltocategorypage'] = 'Link label to category page';
$string['linklabeltocategorypagedesc'] = 'Link label to category page';

$string['custommenu'] = 'Custom menu';
$string['custommenudesc'] = 'Custom menu';

$string['logoinfo'] = 'Logo';
$string['logoinfodesc'] = 'Logo desc';
