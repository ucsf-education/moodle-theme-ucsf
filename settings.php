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
 * Moodle's Clean theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package    theme
 * @subpackage UCSF
 * @author     Lambda Soulutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$settings=null;

require_once($CFG->dirroot.'/theme/ucsf/locallib.php');

defined('MOODLE_INTERNAL') || die;

// Get all categories
$categories = get_config('theme_ucsf');
$all_categories = '';
if(!empty($categories->all_categories))
    $all_categories = $categories->all_categories;
$all_categories_array = explode(",", $all_categories);

// Get all categories
$get_recurring_alerts = get_config('theme_ucsf');
$all_categories = '';
if(!empty($get_recurring_alerts->all_categories))
    $all_categories = $get_recurring_alerts->all_categories;
$all_categories_array = explode(",", $all_categories);

$sql = "SELECT cc.id, cc.name
        FROM {course_categories} cc
        WHERE cc.parent = 0
        ORDER BY cc.sortorder";
$course_categories =  $DB->get_records_sql($sql);

$sql2 = "SELECT ccp.id, cc.name, ccp.name as parentname
        FROM {course_categories} cc
        INNER JOIN {course_categories} ccp
        WHERE ccp.parent = cc.id
        ORDER BY cc.sortorder";
$course_subcategories =  $DB->get_records_sql($sql2);

$choices = array();
$choices[0]="None";

$remove_categories_list = array();
$remove_categories_list[0]="Site wide";

$alert_category_array = array();
$alert_category_array[0]="None";

foreach ($course_categories as $alert_category_arrays) {
    $alert_category_array[$alert_category_arrays->id]=$alert_category_arrays->name;
}

foreach ($course_subcategories as $alert_category_arrays) {
    foreach ($all_categories_array as $all_cats) {
        if ($all_cats == $alert_category_arrays->id) {
            $alert_category_array[$alert_category_arrays->id]=$alert_category_arrays->name . ' / ' .$alert_category_arrays->parentname ;
        }
    }
    if(!in_array($alert_category_arrays->id, $all_categories_array))
        $alert_category_array[$alert_category_arrays->id]=$alert_category_arrays->name . ' / ' .$alert_category_arrays->parentname;
}

foreach ($course_categories as $cat) {
    foreach ($all_categories_array as $all_cats) {
        if ($all_cats == $cat->id) {
            $remove_categories_list[$cat->id]=$cat->name;
        }
    }
    if(!in_array($cat->id, $all_categories_array))
        $choices[$cat->id]=$cat->name;
}

foreach ($course_subcategories as $cat) {
    foreach ($all_categories_array as $all_cats) {
        if ($all_cats == $cat->id) {
            $remove_categories_list[$cat->id]=$cat->name . ' / ' .$cat->parentname ;
        }
    }
    if(!in_array($cat->id, $all_categories_array))
        $choices[$cat->id]=$cat->name . ' / ' .$cat->parentname;
}

$ADMIN->add('themes', new admin_category('theme_ucsf', 'UCSF'));

$settings_lr = new admin_settingpage('theme_ucsf_helpfeedback_settings', get_string('helpfeedback', 'theme_ucsf'));

// Enable/Disable Help/Feedback links;.
$name = 'theme_ucsf/enablehelpfeedback';
$title = get_string('enablehelpfeedback', 'theme_ucsf');
$description = get_string('enablehelpfeedbackdesc', 'theme_ucsf');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Help/Feedback button title
$name = 'theme_ucsf/helpfeedbacktitle';
$title = get_string('helpfeedbacktitle', 'theme_ucsf');
$description = get_string('helpfeedbacktitledesc', 'theme_ucsf');
$default = 'Help/Feedback';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Select the number of links
$name = 'theme_ucsf/numberoflinks';
$title = get_string('numberoflinks', 'theme_ucsf');
$description = '';
$default = 0;
$helpfeedbackchoices = array(
    0 => '0',
    1 => '1',
    2 => '2',
    3 => '3',
    4 => '4',
    5 => '5',
    6 => '6',
    7 => '7',
    8 => '8',
    9 => '9',
    10 => '10'
);

$setting = new admin_setting_configselect($name, $title, $description, $default, $helpfeedbackchoices);
$settings_lr->add($setting);

// Draw input field for desired number of slides
$numberoflinks = get_config('theme_ucsf', 'numberoflinks');
for ($i = 1; $i <= $numberoflinks; $i++) {
    // Help/Feedback link One
    $name = 'theme_ucsf/helpfeedback' . $i . 'heading';
    $heading = get_string('helpfeedbackno', 'theme_ucsf', array('help' => $i));
    $information = "";
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings_lr->add($setting);

    $name = 'theme_ucsf/helpfeedback' . $i . 'link';
    $title = get_string('helpfeedbacklink', 'theme_ucsf');
    $description = get_string('helpfeedbacklinkdesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    $name = 'theme_ucsf/helpfeedback' . $i . 'linklabel';
    $title = get_string('helpfeedbacklinklabel', 'theme_ucsf');
    $description = get_string('helpfeedbacklinklabeldesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    // Help/Feedback link target
    $name = 'theme_ucsf/helpfeedback' . $i . 'linktarget';
    $title = get_string('helpfeedbacklinktarget' , 'theme_ucsf');
    $description = get_string('helpfeedbacklinktargetdesc', 'theme_ucsf');
    $default = '0';
    $helpfeedbac_choices = array('0'=>"No", '1'=>'Yes');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $helpfeedbac_choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);
}

$ADMIN->add('theme_ucsf', $settings_lr);

/* GENERAL SETTINGS
-------------------------------------------------------------------------------*/
$settings_lr = new admin_settingpage('theme_ucsf_general_settings', get_string('generalheading', 'theme_ucsf'));

//General settings
$name = 'theme_ucsf/generalsettings';
$heading = get_string('generalsettings', 'theme_ucsf');
$information = get_string('generalsettingsdesc', 'theme_ucsf');
$setting = new admin_setting_heading($name, $heading, $information);
$settings_lr->add($setting);

//Enable category customizations
$name = 'theme_ucsf/enablecustomization';
$heading = get_string('enablecustomization', 'theme_ucsf');
$information = get_string('enablecustomizationdesc', 'theme_ucsf');
$default = '0';
$setting = new admin_setting_configcheckbox($name, $heading, $information, $default);
$settings_lr->add($setting);

// Header Image
$name = 'theme_ucsf/headerimage';
$heading = get_string('headerimage', 'theme_ucsf');
$information = get_string('headerimagedesc', 'theme_ucsf');
$setting = new admin_setting_configstoredfile($name, $heading, $information, 'headerimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Header Image Alt Text
$name = 'theme_ucsf/headerimagealt';
$heading = get_string('headerimagealt', 'theme_ucsf');
$information = get_string('headerimagealtdesc', 'theme_ucsf');
$default = 'UCSF | CLE ';
$setting = new admin_setting_configtext($name, $heading, $information, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Header Image Title
$name = 'theme_ucsf/headerimagetitle';
$heading = get_string('headerimagetitle', 'theme_ucsf');
$information = get_string('headerimagetitledesc', 'theme_ucsf');
$default = 'UCSF | CLE ';
$setting = new admin_setting_configtext($name, $heading, $information, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Header Image Height
$name = 'theme_ucsf/headerimageheight';
$heading = get_string('headerimageheight', 'theme_ucsf');
$information = get_string('headerimageheightdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configtext($name, $heading, $information, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Header Image Width
$name = 'theme_ucsf/headerimagewidth';
$heading = get_string('headerimagewidth', 'theme_ucsf');
$information = get_string('headerimagewidthdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configtext($name, $heading, $information, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Header Image Link
$name = 'theme_ucsf/headerimagelink';
$heading = get_string('headerimagelink', 'theme_ucsf');
$information = get_string('headerimagelinkdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configtext($name, $heading, $information, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Header Image Link Target
$name = 'theme_ucsf/headerimagelinktarget';
$heading = get_string('headerimagelinktarget', 'theme_ucsf');
$information = get_string('headerimagelinktargetdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configcheckbox($name, $heading, $information, $default, '_blank', '');
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Header Label
$name = 'theme_ucsf/headerlabel';
$heading = get_string('headerlabel', 'theme_ucsf');
$information = get_string('headerlabeldesc', 'theme_ucsf');
$default = 'Collaborative Learning Environment';
$setting = new admin_setting_configtext($name, $heading, $information, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

//Top-level category label
$name = 'theme_ucsf/toplevelcategorylabel';
$title = get_string('toplevelcategorylabel', 'theme_ucsf');
$description = get_string('toplevelcategorylabeldesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

//Display course title
$name = 'theme_ucsf/displaycoursetitle';
$heading = get_string('displaycoursetitle', 'theme_ucsf');
$information = get_string('displaycoursetitledesc', 'theme_ucsf');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $heading, $information, $default, 1, 0);
$settings_lr->add($setting);

//Hide custom menu when logged out
$name = 'theme_ucsf/hidecustommenuwhenloggedout';
$heading = get_string('hidecustommenuwhenloggedout', 'theme_ucsf');
$information = get_string('hidecustommenuwhenloggedoutdesc', 'theme_ucsf');
$default = '0';
$setting = new admin_setting_configcheckbox($name, $heading, $information, $default);
$settings_lr->add($setting);

// Enable/Disable custom CSS.
$name = 'theme_ucsf/customcssenabled';
$title = get_string('enablecustomcss', 'theme_ucsf');
$description = get_string('enablecustomcssdesc', 'theme_ucsf');
$default = false;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Custom CSS.
$name = 'theme_ucsf/customcss';
$title = get_string('customcss', 'theme_ucsf');
$description = get_string('customcssdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Copyright
$name = 'theme_ucsf/copyright';
$title = get_string('copyright', 'theme_ucsf');
$description = get_string('copyrightdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Footnote
$name = 'theme_ucsf/footnote';
$title = get_string('footnote', 'theme_ucsf');
$description = get_string('footnotedesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

$ADMIN->add('theme_ucsf', $settings_lr);


/* BLOCK SETTINGS
-------------------------------------------------------------------------------*/

$settings_lr = new admin_settingpage('theme_ucsf_block_settings', get_string('blockheading', 'theme_ucsf'));

// Block width for large desktop
$name = 'theme_ucsf/block_width_desktop_heading';
$heading = get_string('block_width_desktop_heading', 'theme_ucsf');
$information = "";
$setting = new admin_setting_heading($name, $heading, $information);
$settings_lr->add($setting);

// Block width settings
$name = 'theme_ucsf/block_width_desktop';
$title = get_string('block_width_desktop', 'theme_ucsf');
$description = get_string('block_width_desktopdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

// Block width for large desktop
$name = 'theme_ucsf/block_width_tablet_heading';
$heading = get_string('block_width_tablet_heading', 'theme_ucsf');
$information = "";
$setting = new admin_setting_heading($name, $heading, $information);
$settings_lr->add($setting);

// Block width settings
$name = 'theme_ucsf/block_width_portrait_tablet';
$title = get_string('block_width_portrait_tablet', 'theme_ucsf');
$description = get_string('block_width_portrait_tabletdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

$ADMIN->add('theme_ucsf', $settings_lr);

/* ALERTS SETTINGS
-------------------------------------------------------------------------------*/

$settings_lr = new admin_settingpage('theme_ucsf_alerts', get_string('alertsheading', 'theme_ucsf'));

$name = 'theme_ucsf/number_of_alerts';
$title = get_string('number_of_alerts' , 'theme_ucsf');
$description = get_string('number_of_alertsdesc', 'theme_ucsf');
$default = '0';
$number_of_alerts = array(
    0 => '0',
    1 => '1',
    2 => '2',
    3 => '3',
    4 => '4',
    5 => '5',
    6 => '6',
    7 => '7',
    8 => '8',
    9 => '9',
    10 => '10'
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $number_of_alerts);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);


$numberofalerts = get_config('theme_ucsf', 'number_of_alerts');
for ($i = 1; $i <= $numberofalerts; $i++) {
    // This is the descriptor for Alert One
    $name = 'theme_ucsf/alert'.$i.'info';
    $heading = get_string('alert'.$i, 'theme_ucsf');
    $information = "";
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings_lr->add($setting);

    // Enable Alert
    $name = 'theme_ucsf/enable'.$i.'alert';
    $title = get_string('enablealert', 'theme_ucsf');
    $description = get_string('enablealertdesc', 'theme_ucsf');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    // Recurring alerts.
    $name = 'theme_ucsf/recurring_alert'.$i;
    $title = get_string('recurring_alert' , 'theme_ucsf');
    $description = get_string('recurring_alertdesc', 'theme_ucsf');
    $default = '1';
    $recurring_alerts = array(
    '1'=> get_string('never_end', 'theme_ucsf'),
    '2'=> get_string('one_time', 'theme_ucsf'),
    '3'=> get_string('daily', 'theme_ucsf'),
    '4'=> get_string('weekly', 'theme_ucsf'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $recurring_alerts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    $alert_settings = 'recurring_alert'.$i;

    if (isset($get_recurring_alerts->$alert_settings)) {
        $alert_choice = $get_recurring_alerts->$alert_settings;
    } else {
        $alert_choice = null;
    }
    if ($alert_choice == '1') {
        $null = '';

    } elseif ($alert_choice == '2') {

        // Start date.
        $name = 'theme_ucsf/start_date'.$i;
        $title = get_string('start_date', 'theme_ucsf');
        $date = 'start_date'.$i;
        $hour = 'start_hour'.$i;
        $minute = 'start_minute'.$i;
        $enddate = 'end_date'.$i;
        $endhour = 'end_hour'.$i;
        $endminute = 'end_minute'.$i;
        $description = get_string('start_datedesc', 'theme_ucsf');
        $default = null;
        $setting = new theme_ucsf_datepicker_with_validation($name, $date, $hour, $minute, $enddate, $endhour, $endminute, $title,  $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $settings_lr->add($setting);

    } elseif ($alert_choice == '3') {

        // Start/end daily date picker
        $name = 'theme_ucsf/start_date_daily'.$i;
        $title = get_string('start_date', 'theme_ucsf');
        $date_start = 'start_date_daily'.$i;
        $date_end = 'end_date_daily'.$i;
        $default = null;
        $description = get_string('start_datedesc', 'theme_ucsf');
        $setting = new theme_ucsf_datepicker($name, $date_start, $date_end, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $settings_lr->add($setting);

        //Start/end daily time picker
        $name = 'theme_ucsf/start_hour_and_minute_daily'.$i;
        $title = get_string('end_date_weekly', 'theme_ucsf');
        $hour_start = 'start_hour_daily'.$i;
        $minute_start = 'start_minute_daily'.$i;
        $hour_end = 'end_hour_daily'.$i;
        $minute_end = 'end_minute_daily'.$i;
        $description = get_string('start_hour_and_minute_dailydesc', 'theme_ucsf');
        $default = null;
        $setting = new theme_ucsf_datepicker_time($name, $hour_start, $minute_start, $hour_end, $minute_end, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $settings_lr->add($setting);

    } elseif ($alert_choice == '4') {

        // Start/end weekly date picker
        $name = 'theme_ucsf/start_date_weekly'.$i;
        $title = get_string('start_date', 'theme_ucsf');
        $date = 'start_date_weekly'.$i;
        $enddate = 'end_date_weekly'.$i;
        $default = null;
        $description = get_string('start_datedesc', 'theme_ucsf');
        $setting = new theme_ucsf_datepicker($name, $date, $enddate, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $settings_lr->add($setting);

        // Start/end weekly date picker
        $name = 'theme_ucsf/set_weekly_time'.$i;
        $title = get_string('end_date_weekly', 'theme_ucsf');
        $hour = 'end_hour_weekly'.$i;
        $minute = 'end_minute_weekly'.$i;
        $start_hour = 'start_hour_weekly'.$i;
        $start_minute = 'start_minute_weekly'.$i;
        $default = null;
        $description = get_string('end_weeklydesc', 'theme_ucsf');
        $setting = new theme_ucsf_datepicker_time($name, $start_hour, $start_minute, $hour, $minute, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $settings_lr->add($setting);

        // Select day in week to show an alert.
        $name = 'theme_ucsf/show_week_day'.$i;
        $title = get_string('show_week_day' , 'theme_ucsf');
        $description = get_string('show_week_daydesc', 'theme_ucsf');
        $default = '0';
        $weekdays = array(
            0 => new lang_string('sunday', 'calendar'),
            1 => new lang_string('monday', 'calendar'),
            2 => new lang_string('tuesday', 'calendar'),
            3 => new lang_string('wednesday', 'calendar'),
            4 => new lang_string('thursday', 'calendar'),
            5 => new lang_string('friday', 'calendar'),
            6 => new lang_string('saturday', 'calendar')
        );
        $setting = new admin_setting_configselect($name, $title, $description, $default, $weekdays);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $settings_lr->add($setting);

    }

    //Add category - list.
    $name = 'theme_ucsf/categories_list_alert'.$i;
    $title = get_string('categories_list_alert' , 'theme_ucsf');
    $description = get_string('categories_list_alertdesc', 'theme_ucsf');
    $default = '0';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $remove_categories_list);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    // Alert Type.
    $name = 'theme_ucsf/alert'.$i.'type';
    $title = get_string('alerttype' , 'theme_ucsf');
    $description = get_string('alerttypedesc', 'theme_ucsf');
    $alert_info = get_string('alert_info', 'theme_ucsf');
    $alert_warning = get_string('alert_warning', 'theme_ucsf');
    $alert_general = get_string('alert_general', 'theme_ucsf');
    $default = 'info';
    $alert_choices = array('info'=>$alert_info, 'error'=>$alert_warning, 'success'=>$alert_general);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $alert_choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    // Alert Title.
    $name = 'theme_ucsf/alert'.$i.'title';
    $title = get_string('alerttitle', 'theme_ucsf');
    $description = get_string('alerttitledesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    // Alert Text.
    $name = 'theme_ucsf/alert'.$i.'text';
    $title = get_string('alerttext', 'theme_ucsf');
    $description = get_string('alerttextdesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);
}

$ADMIN->add('theme_ucsf', $settings_lr);

/* TILES SETTINGS
-------------------------------------------------------------------------------*/

$settings_lr = new admin_settingpage('theme_ucsf_tiles', get_string('tileheading', 'theme_ucsf'));

// This is the descriptor for Tile
$name = 'theme_ucsf/tileheadingsub';
$heading = get_string('tileheadingsub', 'theme_ucsf');
$information = "";
$setting = new admin_setting_heading($name, $heading, $information);
$settings_lr->add($setting);

// This is the descriptor for Banner
$name = 'theme_ucsf/banner';
$title = get_string('banner', 'theme_ucsf');
$description = get_string('bannerdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

$name = 'theme_ucsf/bannerimage';
$title = get_string('bannerimage', 'theme_ucsf');
$description = get_string('bannerimagedesc', 'theme_ucsf');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'bannerimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

$name = 'theme_ucsf/bannerimagealt';
$title = get_string('bannerimagealt', 'theme_ucsf');
$description = get_string('bannerimagealtdesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

$name = 'theme_ucsf/bannerimagetitle';
$title = get_string('bannerimagetitle', 'theme_ucsf');
$description = get_string('bannerimagetitledesc', 'theme_ucsf');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);


$name = 'theme_ucsf/numberoftiles';
$title = get_string('numberoftiles', 'theme_ucsf');
$description = '';
$default = 0;
$tilenumber = array (
        0 => '0',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => '10',
    );
$setting = new admin_setting_configselect($name, $title, $description, $default, $tilenumber);
$settings_lr->add($setting);

$numberoftiles = get_config('theme_ucsf', 'numberoftiles');
for ($i = 1; $i <= $numberoftiles; $i++) {
    $name = 'theme_ucsf/tile' . $i . 'heading';
    $heading = get_string('tile', 'theme_ucsf', array('help' => $i));
    $information = "";
    $setting = new admin_setting_heading($name, $heading, $information);
    $settings_lr->add($setting);

    $name = 'theme_ucsf/tile' . $i . 'content';
    $title = get_string('tilecontent', 'theme_ucsf');
    $description = get_string('tilecontentdesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    $name = 'theme_ucsf/tile' . $i . 'image';
    $title = get_string('tileimage', 'theme_ucsf');
    $description = get_string('tileimagedesc', 'theme_ucsf');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'tile' . $i . 'image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    $name = 'theme_ucsf/tile' . $i . 'imagealt';
    $title = get_string('tileimagealt', 'theme_ucsf');
    $description = get_string('tileimagealtdesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    $name = 'theme_ucsf/tile' . $i . 'imagetitle';
    $title = get_string('tileimagetitle', 'theme_ucsf');
    $description = get_string('tileimagetitledesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    $name = 'theme_ucsf/tile' . $i . 'select';
    $title = get_string('tileselect', 'theme_ucsf');
    $description = get_string('tileselectdesc', 'theme_ucsf');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings_lr->add($setting);

    $name = 'theme_ucsf/positionoftile' . $i;
    $title = get_string('positionoftile', 'theme_ucsf');
    $description = get_string('positionoftiledesc', 'theme_ucsf');
    $default = 0;
    $tilenumber = array (
            0 => '',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6'
        );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $tilenumber);
    $settings_lr->add($setting);
    }

$ADMIN->add('theme_ucsf', $settings_lr);

/* CATEGORY CUSTOMIZATION
-------------------------------------------------------------------------------*/
$settings_lr = new admin_settingpage('theme_ucsf_category_customizations', get_string('categorycustomizationheading', 'theme_ucsf'));

//Add category customization
$name = 'theme_ucsf/addcategorycustomizationheading';
$heading = get_string('addcategorycustomizationheading', 'theme_ucsf');
$information = get_string('addcategorycustomizationheadingdesc', 'theme_ucsf');
$setting = new admin_setting_heading($name, $heading, $information);
$settings_lr->add($setting);

//Add category - list.
$name = 'theme_ucsf/categorieslist';
$title = get_string('categorieslist' , 'theme_ucsf');
$description = get_string('categorieslistdesc', 'theme_ucsf');
$default = '0';
$setting = new theme_ucsf_add_category_customization($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

//Add category customization
$name = 'theme_ucsf/removecategorycustomizationheading';
$heading = get_string('removecategorycustomizationheading', 'theme_ucsf');
$information = get_string('removecategorycustomizationheadingdesc', 'theme_ucsf');
$setting = new admin_setting_heading($name, $heading, $information);
$settings_lr->add($setting);

//Add category - list.
$name = 'theme_ucsf/removecategorieslist';
$title = get_string('removecategorieslist' , 'theme_ucsf');
$description = get_string('removecategorieslistdesc', 'theme_ucsf');
$default = '0';
$setting = new theme_ucsf_remove_category_customization($name, $title, $description, $default, $remove_categories_list);
$setting->set_updatedcallback('theme_reset_all_caches');
$settings_lr->add($setting);

$ADMIN->add('theme_ucsf', $settings_lr);

/* CATEGORIES & SUBCATEGORIES
-------------------------------------------------------------------------------*/

foreach ($all_categories_array as $allcats) {
    foreach ($course_categories as $cats) {
        if($allcats == $cats->id) {
            $settings_lr = new admin_settingpage('theme_ucsf_'.$cats->id, 'Cat - '. $cats->name);

            $name = 'theme_ucsf/categorylabelsubsectionsection';
            $heading = get_string('categorylabelsubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            //Category label
            $name = 'theme_ucsf/categorylabel'.$cats->id;
            $title = get_string('categorylabel', 'theme_ucsf');
            $description = get_string('categorylabeldesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Category label image
            $name = 'theme_ucsf/categorylabelimage'.$cats->id;
            $title = get_string('categorylabelimage', 'theme_ucsf');
            $description = get_string('categorylabelimagedesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $title, $description, 'categorylabelimage'.$cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Category label image height
            $name = 'theme_ucsf/categorylabelimageheight'.$cats->id;
            $title = get_string('categorylabelimageheight', 'theme_ucsf');
            $description = get_string('categorylabelimageheightdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Category label image alt
            $name = 'theme_ucsf/categorylabelimagealt'.$cats->id;
            $title = get_string('categorylabelimagealt', 'theme_ucsf');
            $description = get_string('categorylabelimagealtdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Category label image title
            $name = 'theme_ucsf/categorylabelimagetitle'.$cats->id;
            $title = get_string('categorylabelimagetitle', 'theme_ucsf');
            $description = get_string('categorylabelimagetitledesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Link label to category page
            $name = 'theme_ucsf/linklabeltocategorypage'.$cats->id;
            $heading = get_string('linklabeltocategorypage', 'theme_ucsf');
            $information = get_string('linklabeltocategorypagedesc', 'theme_ucsf');
            $default = "0";
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, 1, 0);
            $settings_lr->add($setting);

            //Display course title
            $name = 'theme_ucsf/displaycoursetitle'.$cats->id;
            $heading = get_string('displaycoursetitle', 'theme_ucsf');
            $information = get_string('displaycoursetitledesc', 'theme_ucsf');
            $default = 1;
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, 1, 0);
            $settings_lr->add($setting);

            $name = 'theme_ucsf/headerimagesubsection';
            $heading = get_string('headerimagesubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            // Enable/Disable header image and label customizations
            $name = 'theme_ucsf/customheaderenabled' . $cats->id;
            $heading = get_string('customheaderenabled', 'theme_ucsf');
            $information = get_string('customheaderenableddesc', 'theme_ucsf');
            $default = false;
            $setting = new admin_setting_configcheckbox($name, $heading, $information,$default, true, false);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image
            $name = 'theme_ucsf/headerimage' . $cats->id;
            $heading = get_string('headerimage', 'theme_ucsf');
            $information = get_string('headerimagedesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'headerimage' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Alt Text
            $name = 'theme_ucsf/headerimagealt' . $cats->id;
            $heading = get_string('headerimagealt', 'theme_ucsf');
            $information = get_string('headerimagealtdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Title
            $name = 'theme_ucsf/headerimagetitle' . $cats->id;
            $heading = get_string('headerimagetitle', 'theme_ucsf');
            $information = get_string('headerimagetitledesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Height
            $name = 'theme_ucsf/headerimageheight' . $cats->id;
            $heading = get_string('headerimageheight', 'theme_ucsf');
            $information = get_string('headerimageheightdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Width
            $name = 'theme_ucsf/headerimagewidth' . $cats->id;
            $heading = get_string('headerimagewidth', 'theme_ucsf');
            $information = get_string('headerimagewidthdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Link
            $name = 'theme_ucsf/headerimagelink' . $cats->id;
            $heading = get_string('headerimagelink', 'theme_ucsf');
            $information = get_string('headerimagelinkdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Link Target
            $name = 'theme_ucsf/headerimagelinktarget' . $cats->id;
            $heading = get_string('headerimagelinktarget', 'theme_ucsf');
            $information = get_string('headerimagelinktargetdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, '_blank', '');
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Label
            $name = 'theme_ucsf/headerlabel' . $cats->id;
            $heading = get_string('headerlabel', 'theme_ucsf');
            $information = get_string('headerlabeldesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            $name = 'theme_ucsf/custommenusubsection';
            $heading = get_string('custommenusubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            //Custom menu
            $name = 'theme_ucsf/custommenu'.$cats->id;
            $heading = get_string('custommenu', 'theme_ucsf');
            $information = get_string('custommenudesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtextarea($name, $heading, $information, $default);
            $settings_lr->add($setting);

            // CSS CUSTOMIZATIONS
            $name = 'theme_ucsf/customcsssectionheading';
            $heading = get_string('customcsssubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            // Enable/Disable custom CSS.
            $name = 'theme_ucsf/customcssenabled'.$cats->id;
            $heading = get_string('enablecustomcss', 'theme_ucsf');
            $information = get_string('enablecustomcatcssdesc', 'theme_ucsf');
            $default = false;
            $setting = new admin_setting_configcheckbox($name, $heading, $information,$default, true, false);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Custom CSS.
            $name = 'theme_ucsf/customcss'.$cats->id;
            $heading = get_string('customcss', 'theme_ucsf');
            $information = get_string('customcatcssdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtextarea($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Menu background
            $name = 'theme_ucsf/menubackground' . $cats->id;
            $heading = get_string('menubackground', 'theme_ucsf');
            $information = get_string('menubackgrounddesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'menubackground' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Menu divider
            $name = 'theme_ucsf/menudivider' . $cats->id;
            $heading = get_string('menudivider', 'theme_ucsf');
            $information = get_string('menudividerdesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'menudivider' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Menu divider mobile
            $name = 'theme_ucsf/menudividermobile' . $cats->id;
            $heading = get_string('menudividermobile', 'theme_ucsf');
            $information = get_string('menudividermobiledesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'menudividermobile' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Menu item divider
            $name = 'theme_ucsf/menuitemdivider' . $cats->id;
            $heading = get_string('menuitemdivider', 'theme_ucsf');
            $information = get_string('menuitemdividerdesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'menuitemdivider' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // HELP/FEEDBACK LINKS

            $name = 'theme_ucsf/helpfeedbacksubsection';
            $heading = get_string('helpfeedbacksubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            // Enable/Disable Help/Feedback links;.
            $name = 'theme_ucsf/catenablehelpfeedback'.$cats->id;
            $title = get_string('enablehelpfeedback', 'theme_ucsf');
            $description = get_string('enablehelpfeedbackdesc', 'theme_ucsf');
            $default = false;
            $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Help/Feedback button title
            $name = 'theme_ucsf/cathelpfeedbacktitle'.$cats->id;
            $title = get_string('helpfeedbacktitle', 'theme_ucsf');
            $description = get_string('helpfeedbacktitledesc', 'theme_ucsf');
            $default = 'Help/Feedback';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Select the number of links
            $name = 'theme_ucsf/catnumberoflinks'.$cats->id;
            $title = get_string('numberoflinks', 'theme_ucsf');
            $description = '';
            $default = 0;
            $helpfeedbackchoices = array(
                0 => '0',
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5',
                6 => '6',
                7 => '7',
                8 => '8',
                9 => '9',
                10 => '10'
            );

            $setting = new admin_setting_configselect($name, $title, $description, $default, $helpfeedbackchoices);
            $settings_lr->add($setting);

            // Draw input field for desired number of slides
            $numberoflinks = get_config('theme_ucsf', 'catnumberoflinks'.$cats->id);
            for ($i = 1; $i <= $numberoflinks; $i++) {

                $name = 'theme_ucsf/cathelpfeedback' . $i . 'heading' . $cats->id;
                $heading = get_string('helpfeedbackno', 'theme_ucsf', array('help' => $i));
                $information = "";
                $setting = new admin_setting_heading($name, $heading, $information);
                $settings_lr->add($setting);

                $name = 'theme_ucsf/cathelpfeedback' . $i . 'link' . $cats->id;
                $title = get_string('helpfeedbacklink', 'theme_ucsf');
                $description = get_string('helpfeedbacklinkdesc', 'theme_ucsf');
                $default = '';
                $setting = new admin_setting_configtext($name, $title, $description, $default);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $settings_lr->add($setting);

                $name = 'theme_ucsf/cathelpfeedback' . $i . 'linklabel' . $cats->id;
                $title = get_string('helpfeedbacklinklabel', 'theme_ucsf');
                $description = get_string('helpfeedbacklinklabeldesc', 'theme_ucsf');
                $default = '';
                $setting = new admin_setting_configtext($name, $title, $description, $default);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $settings_lr->add($setting);

                // Help/Feedback link target
                $name = 'theme_ucsf/cathelpfeedback' . $i . 'linktarget' . $cats->id;
                $title = get_string('helpfeedbacklinktarget' , 'theme_ucsf');
                $description = get_string('helpfeedbacklinktargetdesc', 'theme_ucsf');
                $default = '0';
                $helpfeedbac_choices = array('0'=>"No", '1'=>'Yes');
                $setting = new admin_setting_configselect($name, $title, $description, $default, $helpfeedbac_choices);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $settings_lr->add($setting);
            }

            $ADMIN->add('theme_ucsf', $settings_lr);
        }
    }

    foreach ($course_subcategories as $cats) {
        if($allcats == $cats->id) {
            $settings_lr = new admin_settingpage('theme_ucsf_'.$cats->id, 'Cat - '. $cats->name . ' / ' .$cats->parentname);

            $name = 'theme_ucsf/categorylabelsubsectionsection';
            $heading = get_string('categorylabelsubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            //Category label
            $name = 'theme_ucsf/categorylabel'.$cats->id;
            $title = get_string('categorylabel', 'theme_ucsf');
            $description = get_string('categorylabeldesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Category label image
            $name = 'theme_ucsf/categorylabelimage'.$cats->id;
            $title = get_string('categorylabelimage', 'theme_ucsf');
            $description = get_string('categorylabelimagedesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $title, $description, 'categorylabelimage'.$cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Category label image height
            $name = 'theme_ucsf/categorylabelimageheight'.$cats->id;
            $title = get_string('categorylabelimageheight', 'theme_ucsf');
            $description = get_string('categorylabelimageheightdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Category label image alt
            $name = 'theme_ucsf/categorylabelimagealt'.$cats->id;
            $title = get_string('categorylabelimagealt', 'theme_ucsf');
            $description = get_string('categorylabelimagealtdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Category label image title
            $name = 'theme_ucsf/categorylabelimagetitle'.$cats->id;
            $title = get_string('categorylabelimagetitle', 'theme_ucsf');
            $description = get_string('categorylabelimagetitledesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            //Link label to category page
            $name = 'theme_ucsf/linklabeltocategorypage'.$cats->id;
            $heading = get_string('linklabeltocategorypage', 'theme_ucsf');
            $information = get_string('linklabeltocategorypagedesc', 'theme_ucsf');
            $default = "0";
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, 1, 0);
            $settings_lr->add($setting);

            //Display course title
            $name = 'theme_ucsf/displaycoursetitle'.$cats->id;
            $heading = get_string('displaycoursetitle', 'theme_ucsf');
            $information = get_string('displaycoursetitledesc', 'theme_ucsf');
            $default = 1;
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, 1, 0);
            $settings_lr->add($setting);

            $name = 'theme_ucsf/headerimagesubsection';
            $heading = get_string('headerimagesubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            // Enable/Disable header image and label customizations
            $name = 'theme_ucsf/customheaderenabled' . $cats->id;
            $heading = get_string('customheaderenabled', 'theme_ucsf');
            $information = get_string('customheaderenableddesc', 'theme_ucsf');
            $default = false;
            $setting = new admin_setting_configcheckbox($name, $heading, $information,$default, true, false);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image
            $name = 'theme_ucsf/headerimage' . $cats->id;
            $heading = get_string('headerimage', 'theme_ucsf');
            $information = get_string('headerimagedesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'headerimage' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Alt Text
            $name = 'theme_ucsf/headerimagealt' . $cats->id;
            $heading = get_string('headerimagealt', 'theme_ucsf');
            $information = get_string('headerimagealtdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Title
            $name = 'theme_ucsf/headerimagetitle' . $cats->id;
            $heading = get_string('headerimagetitle', 'theme_ucsf');
            $information = get_string('headerimagetitledesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Height
            $name = 'theme_ucsf/headerimageheight' . $cats->id;
            $heading = get_string('headerimageheight', 'theme_ucsf');
            $information = get_string('headerimageheightdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Width
            $name = 'theme_ucsf/headerimagewidth' . $cats->id;
            $heading = get_string('headerimagewidth', 'theme_ucsf');
            $information = get_string('headerimagewidthdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Link
            $name = 'theme_ucsf/headerimagelink' . $cats->id;
            $heading = get_string('headerimagelink', 'theme_ucsf');
            $information = get_string('headerimagelinkdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Image Link Target
            $name = 'theme_ucsf/headerimagelinktarget' . $cats->id;
            $heading = get_string('headerimagelinktarget', 'theme_ucsf');
            $information = get_string('headerimagelinktargetdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, '_blank', '');
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Header Label
            $name = 'theme_ucsf/headerlabel' . $cats->id;
            $heading = get_string('headerlabel', 'theme_ucsf');
            $information = get_string('headerlabeldesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            $name = 'theme_ucsf/custommenusubsection';
            $heading = get_string('custommenusubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            //Custom menu
            $name = 'theme_ucsf/custommenu'.$cats->id;
            $heading = get_string('custommenu', 'theme_ucsf');
            $information = get_string('custommenudesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtextarea($name, $heading, $information,$default);
            $settings_lr->add($setting);

            // CSS CUSTOMIZATIONS
            $name = 'theme_ucsf/customcsssectionheading';
            $heading = get_string('customcsssubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            // Enable/Disable custom CSS.
            $name = 'theme_ucsf/customcssenabled'.$cats->id;
            $heading = get_string('enablecustomcss', 'theme_ucsf');
            $information = get_string('enablecustomcatcssdesc', 'theme_ucsf');
            $default = false;
            $setting = new admin_setting_configcheckbox($name, $heading, $information,$default, true, false);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Custom CSS.
            $name = 'theme_ucsf/customcss'.$cats->id;
            $heading = get_string('customcss', 'theme_ucsf');
            $information = get_string('customcatcssdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtextarea($name, $heading, $information,$default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Menu background
            $name = 'theme_ucsf/menubackground' . $cats->id;
            $heading = get_string('menubackground', 'theme_ucsf');
            $information = get_string('menubackgrounddesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'menubackground' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Menu divider
            $name = 'theme_ucsf/menudivider' . $cats->id;
            $heading = get_string('menudivider', 'theme_ucsf');
            $information = get_string('menudividerdesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'menudivider' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Menu divider mobile
            $name = 'theme_ucsf/menudividermobile' . $cats->id;
            $heading = get_string('menudividermobile', 'theme_ucsf');
            $information = get_string('menudividermobiledesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'menudividermobile' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Menu item divider
            $name = 'theme_ucsf/menuitemdivider' . $cats->id;
            $heading = get_string('menuitemdivider', 'theme_ucsf');
            $information = get_string('menuitemdividerdesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'menuitemdivider' . $cats->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            $name = 'theme_ucsf/helpfeedbacksubsection';
            $heading = get_string('helpfeedbacksubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading,'');
            $settings_lr->add($setting);

            // HELP/FEEDBACK LINKS
            // Enable/Disable Help/Feedback links;.
            $name = 'theme_ucsf/catenablehelpfeedback'.$cats->id;
            $title = get_string('enablehelpfeedback', 'theme_ucsf');
            $description = get_string('enablehelpfeedbackdesc', 'theme_ucsf');
            $default = false;
            $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Help/Feedback button title
            $name = 'theme_ucsf/cathelpfeedbacktitle'.$cats->id;
            $title = get_string('helpfeedbacktitle', 'theme_ucsf');
            $description = get_string('helpfeedbacktitledesc', 'theme_ucsf');
            $default = 'Help/Feedback';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $settings_lr->add($setting);

            // Select the number of links
            $name = 'theme_ucsf/catnumberoflinks'.$cats->id;
            $title = get_string('numberoflinks', 'theme_ucsf');
            $description = '';
            $default = 0;
            $helpfeedbackchoices = array(
                0 => '0',
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5',
                6 => '6',
                7 => '7',
                8 => '8',
                9 => '9',
                10 => '10'
            );

            $setting = new admin_setting_configselect($name, $title, $description, $default, $helpfeedbackchoices);
            $settings_lr->add($setting);

            // Draw input field for desired number of slides
            $numberoflinks = get_config('theme_ucsf', 'catnumberoflinks'.$cats->id);
            for ($i = 1; $i <= $numberoflinks; $i++) {

                $name = 'theme_ucsf/cathelpfeedback' . $i . 'heading' . $cats->id;
                $heading = get_string('helpfeedbackno', 'theme_ucsf', array('help' => $i));
                $information = "";
                $setting = new admin_setting_heading($name, $heading, $information);
                $settings_lr->add($setting);

                $name = 'theme_ucsf/cathelpfeedback' . $i . 'link' . $cats->id;
                $title = get_string('helpfeedbacklink', 'theme_ucsf');
                $description = get_string('helpfeedbacklinkdesc', 'theme_ucsf');
                $default = '';
                $setting = new admin_setting_configtext($name, $title, $description, $default);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $settings_lr->add($setting);

                $name = 'theme_ucsf/cathelpfeedback' . $i . 'linklabel' . $cats->id;
                $title = get_string('helpfeedbacklinklabel', 'theme_ucsf');
                $description = get_string('helpfeedbacklinklabeldesc', 'theme_ucsf');
                $default = '';
                $setting = new admin_setting_configtext($name, $title, $description, $default);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $settings_lr->add($setting);

                // Help/Feedback link target
                $name = 'theme_ucsf/cathelpfeedback' . $i . 'linktarget' . $cats->id;
                $title = get_string('helpfeedbacklinktarget' , 'theme_ucsf');
                $description = get_string('helpfeedbacklinktargetdesc', 'theme_ucsf');
                $default = '0';
                $helpfeedbac_choices = array('0'=>"No", '1'=>'Yes');
                $setting = new admin_setting_configselect($name, $title, $description, $default, $helpfeedbac_choices);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $settings_lr->add($setting);
            }

            $ADMIN->add('theme_ucsf', $settings_lr);

        }
    }
}
