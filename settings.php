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
 * @package   theme_ucsfx
 * @copyright 2018 The Regents of the University of California
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/theme/ucsfx/locallib.php');

    // Get all categories
    $categories = get_config('theme_ucsfx');
    $all_categories = '';
    if(!empty($categories->all_categories))
        $all_categories = $categories->all_categories;
    $all_categories_array = explode(",", $all_categories);

    // Get all categories
    $get_recurring_alerts = get_config('theme_ucsfx');
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

    $settings = new theme_boost_admin_settingspage_tabs('themesettingucsfx', get_string('configtitle', 'theme_ucsfx'));
    $page = new admin_settingpage('theme_ucsfx_general', get_string('generalsettings', 'theme_ucsfx'));

    // Preset.
    $name = 'theme_ucsfx/preset';
    $title = get_string('preset', 'theme_ucsfx');
    $description = get_string('preset_desc', 'theme_ucsfx');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_ucsfx', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_ucsfx/presetfiles';
    $title = get_string('presetfiles','theme_ucsfx');
    $description = get_string('presetfiles_desc', 'theme_ucsfx');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Background image setting.
    $name = 'theme_ucsfx/backgroundimage';
    $title = get_string('backgroundimage', 'theme_ucsfx');
    $description = get_string('backgroundimage_desc', 'theme_ucsfx');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $body-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_ucsfx/brandcolor';
    $title = get_string('brandcolor', 'theme_ucsfx');
    $description = get_string('brandcolor_desc', 'theme_ucsfx');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_ucsfx_advanced', get_string('advancedsettings', 'theme_ucsfx'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_ucsfx/scsspre',
        get_string('rawscsspre', 'theme_ucsfx'), get_string('rawscsspre_desc', 'theme_ucsfx'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_ucsfx/scss', get_string('rawscss', 'theme_ucsfx'),
        get_string('rawscss_desc', 'theme_ucsfx'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // Helpmenu

    $page = new admin_settingpage('theme_ucsfx_helpmenu', get_string('helpfeedbacksettings', 'theme_ucsfx'));

    // Enable/Disable Help/Feedback links;.
    $name = 'theme_ucsfx/helpfeedbackenabled';
    $title = get_string('helpfeedbackenabled', 'theme_ucsfx');
    $description = get_string('helpfeedbackenableddesc', 'theme_ucsfx');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Help/Feedback button title
    $name = 'theme_ucsfx/helpfeedbacktitle';
    $title = get_string('helpfeedbacktitle', 'theme_ucsfx');
    $description = get_string('helpfeedbacktitledesc', 'theme_ucsfx');
    $default = 'Help/Feedback';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Select the number of links
    $name = 'theme_ucsfx/numberoflinks';
    $title = get_string('helpfeedbacknumlinks', 'theme_ucsfx');
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
    $page->add($setting);

    // Draw input field for desired number of slides
    $numberoflinks = intval(get_config('theme_ucsfx', 'numberoflinks'), 10);
    for ($i = 1; $i <= $numberoflinks; $i++) {

        $name = 'theme_ucsfx/helpfeedback' . $i . 'heading';
        $heading = get_string('helpfeedbackno', 'theme_ucsfx', array('help' => $i));
        $information = "";
        $setting = new admin_setting_heading($name, $heading, $information);
        $page->add($setting);

        $name = 'theme_ucsfx/helpfeedback' . $i . 'link';
        $title = get_string('helpfeedbacklink', 'theme_ucsfx');
        $description = get_string('helpfeedbacklinkdesc', 'theme_ucsfx');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_ucsfx/helpfeedback' . $i . 'linklabel';
        $title = get_string('helpfeedbacklinklabel', 'theme_ucsfx');
        $description = get_string('helpfeedbacklinklabeldesc', 'theme_ucsfx');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Help/Feedback link target
        $name = 'theme_ucsfx/helpfeedback' . $i . 'linktarget';
        $title = get_string('helpfeedbacklinktarget' , 'theme_ucsfx');
        $description = get_string('helpfeedbacklinktargetdesc', 'theme_ucsfx');
        $default = '0';
        $setting = new admin_setting_configselect($name, $title, $description, $default, array('0' => "No", '1' => 'Yes'));
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    $settings->add($page);

    /* ALERTS SETTINGS
    -------------------------------------------------------------------------------*/

    $page = new admin_settingpage('theme_ucsfx_alerts', get_string('alertsheading', 'theme_ucsfx'));

    $name = 'theme_ucsfx/number_of_alerts';
    $title = get_string('number_of_alerts' , 'theme_ucsfx');
    $description = get_string('number_of_alertsdesc', 'theme_ucsfx');
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
    $page->add($setting);


    $numberofalerts = get_config('theme_ucsfx', 'number_of_alerts');
    for ($i = 1; $i <= $numberofalerts; $i++) {
        // This is the descriptor for Alert One
        $name = 'theme_ucsfx/alert'.$i.'info';
        $heading = get_string('alert'.$i, 'theme_ucsfx');
        $information = "";
        $setting = new admin_setting_heading($name, $heading, $information);
        $page->add($setting);

        // Enable Alert
        $name = 'theme_ucsfx/enable'.$i.'alert';
        $title = get_string('enablealert', 'theme_ucsfx');
        $description = get_string('enablealertdesc', 'theme_ucsfx');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Recurring alerts.
        $name = 'theme_ucsfx/recurring_alert'.$i;
        $title = get_string('recurring_alert' , 'theme_ucsfx');
        $description = get_string('recurring_alertdesc', 'theme_ucsfx');
        $default = '1';
        $recurring_alerts = array(
            '1'=> get_string('never_end', 'theme_ucsfx'),
            '2'=> get_string('one_time', 'theme_ucsfx'),
            '3'=> get_string('daily', 'theme_ucsfx'),
            '4'=> get_string('weekly', 'theme_ucsfx'));
        $setting = new admin_setting_configselect($name, $title, $description, $default, $recurring_alerts);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

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
            $name = 'theme_ucsfx/start_date'.$i;
            $title = get_string('start_date', 'theme_ucsfx');
            $date = 'start_date'.$i;
            $hour = 'start_hour'.$i;
            $minute = 'start_minute'.$i;
            $enddate = 'end_date'.$i;
            $endhour = 'end_hour'.$i;
            $endminute = 'end_minute'.$i;
            $description = get_string('start_datedesc', 'theme_ucsfx');
            $default = null;
            $setting = new theme_ucsfx_datepicker_with_validation($name, $date, $hour, $minute, $enddate, $endhour, $endminute, $title,  $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

        } elseif ($alert_choice == '3') {

            // Start/end daily date picker
            $name = 'theme_ucsfx/start_date_daily'.$i;
            $title = get_string('start_date', 'theme_ucsfx');
            $date_start = 'start_date_daily'.$i;
            $date_end = 'end_date_daily'.$i;
            $default = null;
            $description = get_string('start_datedesc', 'theme_ucsfx');
            $setting = new theme_ucsfx_datepicker($name, $date_start, $date_end, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            //Start/end daily time picker
            $name = 'theme_ucsfx/start_hour_and_minute_daily'.$i;
            $title = get_string('end_date_weekly', 'theme_ucsfx');
            $hour_start = 'start_hour_daily'.$i;
            $minute_start = 'start_minute_daily'.$i;
            $hour_end = 'end_hour_daily'.$i;
            $minute_end = 'end_minute_daily'.$i;
            $description = get_string('start_hour_and_minute_dailydesc', 'theme_ucsfx');
            $default = null;
            $setting = new theme_ucsfx_datepicker_time($name, $hour_start, $minute_start, $hour_end, $minute_end, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

        } elseif ($alert_choice == '4') {

            // Start/end weekly date picker
            $name = 'theme_ucsfx/start_date_weekly'.$i;
            $title = get_string('start_date', 'theme_ucsfx');
            $date = 'start_date_weekly'.$i;
            $enddate = 'end_date_weekly'.$i;
            $default = null;
            $description = get_string('start_datedesc', 'theme_ucsfx');
            $setting = new theme_ucsfx_datepicker($name, $date, $enddate, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Start/end weekly date picker
            $name = 'theme_ucsfx/set_weekly_time'.$i;
            $title = get_string('end_date_weekly', 'theme_ucsfx');
            $hour = 'end_hour_weekly'.$i;
            $minute = 'end_minute_weekly'.$i;
            $start_hour = 'start_hour_weekly'.$i;
            $start_minute = 'start_minute_weekly'.$i;
            $default = null;
            $description = get_string('end_weeklydesc', 'theme_ucsfx');
            $setting = new theme_ucsfx_datepicker_time($name, $start_hour, $start_minute, $hour, $minute, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Select day in week to show an alert.
            $name = 'theme_ucsfx/show_week_day'.$i;
            $title = get_string('show_week_day' , 'theme_ucsfx');
            $description = get_string('show_week_daydesc', 'theme_ucsfx');
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
            $page->add($setting);
        }

        //Add category - list.
        $name = 'theme_ucsfx/categories_list_alert'.$i;
        $title = get_string('categories_list_alert' , 'theme_ucsfx');
        $description = get_string('categories_list_alertdesc', 'theme_ucsfx');
        $default = '0';
        $setting = new admin_setting_configselect($name, $title, $description, $default, $remove_categories_list);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Alert Type.
        $name = 'theme_ucsfx/alert'.$i.'type';
        $title = get_string('alerttype' , 'theme_ucsfx');
        $description = get_string('alerttypedesc', 'theme_ucsfx');
        $alert_info = get_string('alert_info', 'theme_ucsfx');
        $alert_warning = get_string('alert_warning', 'theme_ucsfx');
        $alert_general = get_string('alert_general', 'theme_ucsfx');
        $default = 'info';
        $alert_choices = array('info'=>$alert_info, 'error'=>$alert_warning, 'success'=>$alert_general);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $alert_choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Alert Title.
        $name = 'theme_ucsfx/alert'.$i.'title';
        $title = get_string('alerttitle', 'theme_ucsfx');
        $description = get_string('alerttitledesc', 'theme_ucsfx');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Alert Text.
        $name = 'theme_ucsfx/alert'.$i.'text';
        $title = get_string('alerttext', 'theme_ucsfx');
        $description = get_string('alerttextdesc', 'theme_ucsfx');
        $default = '';
        $setting = new admin_setting_configtextarea($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    $settings->add($page);
}
