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
 * @package   theme_ucsf
 * @copyright 2018 The Regents of the University of California
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/theme/ucsf/locallib.php');

    $theme_config = get_config('theme_ucsf');

    $category_ids = array();
    if (property_exists($theme_config , 'all_categories')) {
        $category_ids = array_unique(array_filter(explode(",", trim($theme_config->all_categories))));
    }

    $queries[] = "SELECT cc.id, cc.name
        FROM {course_categories} cc
        WHERE cc.parent = 0
        ORDER BY cc.sortorder";
    $queries[] = "SELECT ccp.id, CONCAT(cc.name, ' / ', ccp.name) AS name
        FROM {course_categories} cc
        INNER JOIN {course_categories} ccp
        WHERE ccp.parent = cc.id
        ORDER BY cc.sortorder";

    $categories = array();
    foreach ($queries as $query) {
        $categories = array_merge($categories, $DB->get_records_sql($query));
    }

    $add_categories_list = array();
    $add_categories_list[0] = "None";

    $remove_categories_list = array();
    $remove_categories_list[0] = "Site wide";

    $alert_category_array = array();
    $alert_category_array[0] = "None";

    foreach ($categories as $cat) {
        $alert_category_array[$cat->id] = $cat->name;
        
        if (! in_array($cat->id, $category_ids)) {
            $add_categories_list[$cat->id] = $cat->name;
        } else {
            $remove_categories_list[$cat->id] = $cat->name;
        }
    }

    $settings = new theme_boost_admin_settingspage_tabs('themesettingucsf', get_string('configtitle', 'theme_ucsf'));

    // ----------------------------------------------------
    // General Settings
    // ----------------------------------------------------

    $page = new admin_settingpage('theme_ucsf_general', get_string('generalsettings', 'theme_ucsf'));

    //Enable category customizations
    $name = 'theme_ucsf/enablecustomization';
    $heading = get_string('enablecustomization', 'theme_ucsf');
    $information = get_string('enablecustomizationdesc', 'theme_ucsf');
    $default = '0';
    $setting = new admin_setting_configcheckbox($name, $heading, $information, $default);
    $page->add($setting);

    // Header Image
    $name = 'theme_ucsf/headerimage';
    $heading = get_string('headerimage', 'theme_ucsf');
    $information = get_string('headerimagedesc', 'theme_ucsf');
    $setting = new admin_setting_configstoredfile($name, $heading, $information, 'headerimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header Image Alt Text
    $name = 'theme_ucsf/headerimagealt';
    $heading = get_string('headerimagealt', 'theme_ucsf');
    $information = get_string('headerimagealtdesc', 'theme_ucsf');
    $default = 'UCSF | CLE ';
    $setting = new admin_setting_configtext($name, $heading, $information, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header Image Title
    $name = 'theme_ucsf/headerimagetitle';
    $heading = get_string('headerimagetitle', 'theme_ucsf');
    $information = get_string('headerimagetitledesc', 'theme_ucsf');
    $default = 'UCSF | CLE ';
    $setting = new admin_setting_configtext($name, $heading, $information, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header Image Link
    $name = 'theme_ucsf/headerimagelink';
    $heading = get_string('headerimagelink', 'theme_ucsf');
    $information = get_string('headerimagelinkdesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configtext($name, $heading, $information, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header Image Link Target
    $name = 'theme_ucsf/headerimagelinktarget';
    $heading = get_string('headerimagelinktarget', 'theme_ucsf');
    $information = get_string('headerimagelinktargetdesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, '_blank', '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header Label
    $name = 'theme_ucsf/headerlabel';
    $heading = get_string('headerlabel', 'theme_ucsf');
    $information = get_string('headerlabeldesc', 'theme_ucsf');
    $default = 'Collaborative Learning Environment';
    $setting = new admin_setting_configtext($name, $heading, $information, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    //Top-level category label
    $name = 'theme_ucsf/toplevelcategorylabel';
    $title = get_string('toplevelcategorylabel', 'theme_ucsf');
    $description = get_string('toplevelcategorylabeldesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Enable/Disable custom CSS.
    $name = 'theme_ucsf/customcssenabled';
    $title = get_string('enablecustomcss', 'theme_ucsf');
    $description = get_string('enablecustomcssdesc', 'theme_ucsf');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Custom CSS.
    $name = 'theme_ucsf/customcss';
    $title = get_string('customcss', 'theme_ucsf');
    $description = get_string('customcssdesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Copyright
    $name = 'theme_ucsf/copyright';
    $title = get_string('copyright', 'theme_ucsf');
    $description = get_string('copyrightdesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Footnote
    $name = 'theme_ucsf/footnote';
    $title = get_string('footnote', 'theme_ucsf');
    $description = get_string('footnotedesc', 'theme_ucsf');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset.
    $name = 'theme_ucsf/preset';
    $title = get_string('preset', 'theme_ucsf');
    $description = get_string('preset_desc', 'theme_ucsf');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_ucsf', 'preset', 0, 'itemid, filepath, filename', false);

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
    $name = 'theme_ucsf/presetfiles';
    $title = get_string('presetfiles', 'theme_ucsf');
    $description = get_string('presetfiles_desc', 'theme_ucsf');

    $setting = new admin_setting_configstoredfile(
        $name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss'))
    );
    $page->add($setting);

    // Background image setting.
    $name = 'theme_ucsf/backgroundimage';
    $title = get_string('backgroundimage', 'theme_ucsf');
    $description = get_string('backgroundimage_desc', 'theme_ucsf');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $body-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_ucsf/brandcolor';
    $title = get_string('brandcolor', 'theme_ucsf');
    $description = get_string('brandcolor_desc', 'theme_ucsf');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------
    // Advanced settings
    // ----------------------------------------------------

    $page = new admin_settingpage('theme_ucsf_advanced', get_string('advancedsettings', 'theme_ucsf'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode(
        'theme_ucsf/scsspre',
        get_string('rawscsspre', 'theme_ucsf'), get_string('rawscsspre_desc', 'theme_ucsf'), '', PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode(
        'theme_ucsf/scss', get_string('rawscss', 'theme_ucsf'),
        get_string('rawscss_desc', 'theme_ucsf'), '', PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // Helpmenu

    $page = new admin_settingpage('theme_ucsf_helpmenu', get_string('helpfeedbacksettings', 'theme_ucsf'));

    // Enable/Disable Help/Feedback links;.
    $name = 'theme_ucsf/helpfeedbackenabled';
    $title = get_string('helpfeedbackenabled', 'theme_ucsf');
    $description = get_string('helpfeedbackenableddesc', 'theme_ucsf');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Help/Feedback button title
    $name = 'theme_ucsf/helpfeedbacktitle';
    $title = get_string('helpfeedbacktitle', 'theme_ucsf');
    $description = get_string('helpfeedbacktitledesc', 'theme_ucsf');
    $default = 'Help/Feedback';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Select the number of links
    $name = 'theme_ucsf/numberoflinks';
    $title = get_string('helpfeedbacknumlinks', 'theme_ucsf');
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
    $numberoflinks = intval(get_config('theme_ucsf', 'numberoflinks'), 10);
    for ($i = 1; $i <= $numberoflinks; $i++) {

        $name = 'theme_ucsf/helpfeedback'.$i.'heading';
        $heading = get_string('helpfeedbackno', 'theme_ucsf', array('help' => $i));
        $information = "";
        $setting = new admin_setting_heading($name, $heading, $information);
        $page->add($setting);

        $name = 'theme_ucsf/helpfeedback'.$i.'link';
        $title = get_string('helpfeedbacklink', 'theme_ucsf');
        $description = get_string('helpfeedbacklinkdesc', 'theme_ucsf');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_ucsf/helpfeedback'.$i.'linklabel';
        $title = get_string('helpfeedbacklinklabel', 'theme_ucsf');
        $description = get_string('helpfeedbacklinklabeldesc', 'theme_ucsf');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Help/Feedback link target
        $name = 'theme_ucsf/helpfeedback'.$i.'linktarget';
        $title = get_string('helpfeedbacklinktarget', 'theme_ucsf');
        $description = get_string('helpfeedbacklinktargetdesc', 'theme_ucsf');
        $default = '0';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            array('0' => "No", '1' => 'Yes')
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    $settings->add($page);

    // ----------------------------------------------------
    // Alerts Settings
    // ----------------------------------------------------

    $page = new admin_settingpage('theme_ucsf_alerts', get_string('alertsheading', 'theme_ucsf'));

    $name = 'theme_ucsf/number_of_alerts';
    $title = get_string('number_of_alerts', 'theme_ucsf');
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
    $page->add($setting);


    $numberofalerts = get_config('theme_ucsf', 'number_of_alerts');
    for ($i = 1; $i <= $numberofalerts; $i++) {
        // This is the descriptor for Alert One
        $name = 'theme_ucsf/alert'.$i.'info';
        $heading = get_string('alert'.$i, 'theme_ucsf');
        $information = "";
        $setting = new admin_setting_heading($name, $heading, $information);
        $page->add($setting);

        // Enable Alert
        $name = 'theme_ucsf/enable'.$i.'alert';
        $title = get_string('enablealert', 'theme_ucsf');
        $description = get_string('enablealertdesc', 'theme_ucsf');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Recurring alerts.
        $name = 'theme_ucsf/recurring_alert'.$i;
        $title = get_string('recurring_alert', 'theme_ucsf');
        $description = get_string('recurring_alertdesc', 'theme_ucsf');
        $default = '1';
        $recurring_alerts = array(
            '1' => get_string('never_end', 'theme_ucsf'),
            '2' => get_string('one_time', 'theme_ucsf'),
            '3' => get_string('daily', 'theme_ucsf'),
            '4' => get_string('weekly', 'theme_ucsf')
        );
        $setting = new admin_setting_configselect($name, $title, $description, $default, $recurring_alerts);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $alert_settings = 'recurring_alert'.$i;

        if (isset($theme_config->$alert_settings)) {
            $alert_choice = $theme_config->$alert_settings;
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
            $setting = new theme_ucsf_datepicker_with_validation(
                $name,
                $date,
                $hour,
                $minute,
                $enddate,
                $endhour,
                $endminute,
                $title,
                $description,
                $default
            );
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

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
            $page->add($setting);

            //Start/end daily time picker
            $name = 'theme_ucsf/start_hour_and_minute_daily'.$i;
            $title = get_string('end_date_weekly', 'theme_ucsf');
            $hour_start = 'start_hour_daily'.$i;
            $minute_start = 'start_minute_daily'.$i;
            $hour_end = 'end_hour_daily'.$i;
            $minute_end = 'end_minute_daily'.$i;
            $description = get_string('start_hour_and_minute_dailydesc', 'theme_ucsf');
            $default = null;
            $setting = new theme_ucsf_datepicker_time(
                $name,
                $hour_start,
                $minute_start,
                $hour_end,
                $minute_end,
                $title,
                $description,
                $default
            );
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

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
            $page->add($setting);

            // Start/end weekly date picker
            $name = 'theme_ucsf/set_weekly_time'.$i;
            $title = get_string('end_date_weekly', 'theme_ucsf');
            $hour = 'end_hour_weekly'.$i;
            $minute = 'end_minute_weekly'.$i;
            $start_hour = 'start_hour_weekly'.$i;
            $start_minute = 'start_minute_weekly'.$i;
            $default = null;
            $description = get_string('end_weeklydesc', 'theme_ucsf');
            $setting = new theme_ucsf_datepicker_time(
                $name,
                $start_hour,
                $start_minute,
                $hour,
                $minute,
                $title,
                $description,
                $default
            );
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Select day in week to show an alert.
            $name = 'theme_ucsf/show_week_day'.$i;
            $title = get_string('show_week_day', 'theme_ucsf');
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
            $page->add($setting);
        }

        //Add category - list.
        $name = 'theme_ucsf/categories_list_alert'.$i;
        $title = get_string('categories_list_alert', 'theme_ucsf');
        $description = get_string('categories_list_alertdesc', 'theme_ucsf');
        $default = '0';
        $setting = new admin_setting_configselect($name, $title, $description, $default, $remove_categories_list);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Alert Type.
        $name = 'theme_ucsf/alert'.$i.'type';
        $title = get_string('alerttype', 'theme_ucsf');
        $description = get_string('alerttypedesc', 'theme_ucsf');
        $alert_info = get_string('alert_info', 'theme_ucsf');
        $alert_warning = get_string('alert_warning', 'theme_ucsf');
        $alert_general = get_string('alert_general', 'theme_ucsf');
        $default = 'info';
        $alert_choices = array('info' => $alert_info, 'error' => $alert_warning, 'success' => $alert_general);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $alert_choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Alert Text.
        $name = 'theme_ucsf/alert'.$i.'text';
        $title = get_string('alerttext', 'theme_ucsf');
        $description = get_string('alerttextdesc', 'theme_ucsf');
        $default = '';
        $setting = new admin_setting_configtextarea($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    $settings->add($page);

    // ----------------------------------------------------
    // Category Picker
    // ----------------------------------------------------

    $page = new admin_settingpage(
        'theme_ucsf_category_customizations',
        get_string('categorycustomizationheading', 'theme_ucsf')
    );

    //Add category customization
    $name = 'theme_ucsf/addcategorycustomizationheading';
    $heading = get_string('addcategorycustomizationheading', 'theme_ucsf');
    $information = get_string('addcategorycustomizationheadingdesc', 'theme_ucsf');
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);

    //Add category - list.
    $name = 'theme_ucsf/categorieslist';
    $title = get_string('categorieslist', 'theme_ucsf');
    $description = get_string('categorieslistdesc', 'theme_ucsf');
    $default = '0';
    $setting = new theme_ucsf_add_category_customization($name, $title, $description, $default, $add_categories_list);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    //Add category customization
    $name = 'theme_ucsf/removecategorycustomizationheading';
    $heading = get_string('removecategorycustomizationheading', 'theme_ucsf');
    $information = get_string('removecategorycustomizationheadingdesc', 'theme_ucsf');
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);

    //Add category - list.
    $name = 'theme_ucsf/removecategorieslist';
    $title = get_string('removecategorieslist', 'theme_ucsf');
    $description = get_string('removecategorieslistdesc', 'theme_ucsf');
    $default = '0';
    $setting = new theme_ucsf_remove_category_customization(
        $name,
        $title,
        $description,
        $default,
        $remove_categories_list
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------
    // Category Overrides
    // ----------------------------------------------------

    foreach ($categories as $cat) {
        if (in_array($cat->id, $category_ids)) {
            $page_title = 'Cat - ' . $cat->name;
            $page = new admin_settingpage('theme_ucsf_' . $cat->id, $page_title);

            $name = 'theme_ucsf/categorylabelsubsectionsection';
            $heading = get_string('categorylabelsubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading, '');
            $page->add($setting);

            //Category label
            $name = 'theme_ucsf/categorylabel' . $cat->id;
            $title = get_string('categorylabel', 'theme_ucsf');
            $description = get_string('categorylabeldesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            //Link label to category page
            $name = 'theme_ucsf/linklabeltocategorypage' . $cat->id;
            $heading = get_string('linklabeltocategorypage', 'theme_ucsf');
            $information = get_string('linklabeltocategorypagedesc', 'theme_ucsf');
            $default = "0";
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, 1, 0);
            $page->add($setting);

            $name = 'theme_ucsf/headerimagesubsection';
            $heading = get_string('headerimagesubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading, '');
            $page->add($setting);

            // Enable/Disable header image and label customizations
            $name = 'theme_ucsf/customheaderenabled' . $cat->id;
            $heading = get_string('customheaderenabled', 'theme_ucsf');
            $information = get_string('customheaderenableddesc', 'theme_ucsf');
            $default = false;
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, true, false);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Header Image
            $name = 'theme_ucsf/headerimage' . $cat->id;
            $heading = get_string('headerimage', 'theme_ucsf');
            $information = get_string('headerimagedesc', 'theme_ucsf');
            $setting = new admin_setting_configstoredfile($name, $heading, $information, 'headerimage' . $cat->id);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Header Image Alt Text
            $name = 'theme_ucsf/headerimagealt' . $cat->id;
            $heading = get_string('headerimagealt', 'theme_ucsf');
            $information = get_string('headerimagealtdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Header Image Title
            $name = 'theme_ucsf/headerimagetitle' . $cat->id;
            $heading = get_string('headerimagetitle', 'theme_ucsf');
            $information = get_string('headerimagetitledesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Header Image Link
            $name = 'theme_ucsf/headerimagelink' . $cat->id;
            $heading = get_string('headerimagelink', 'theme_ucsf');
            $information = get_string('headerimagelinkdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Header Image Link Target
            $name = 'theme_ucsf/headerimagelinktarget' . $cat->id;
            $heading = get_string('headerimagelinktarget', 'theme_ucsf');
            $information = get_string('headerimagelinktargetdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, '_blank', '');
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Header Label
            $name = 'theme_ucsf/headerlabel' . $cat->id;
            $heading = get_string('headerlabel', 'theme_ucsf');
            $information = get_string('headerlabeldesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtext($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_ucsf/custommenusubsection';
            $heading = get_string('custommenusubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading, '');
            $page->add($setting);

            //Custom menu
            $name = 'theme_ucsf/custommenu' . $cat->id;
            $heading = get_string('custommenu', 'theme_ucsf');
            $information = get_string('custommenudesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtextarea($name, $heading, $information, $default);
            $page->add($setting);

            // CSS CUSTOMIZATIONS
            $name = 'theme_ucsf/customcsssectionheading';
            $heading = get_string('customcsssubsectiontitle', 'theme_ucsf');
            $setting = new admin_setting_heading($name, $heading, '');
            $page->add($setting);

            // Enable/Disable custom CSS.
            $name = 'theme_ucsf/customcssenabled' . $cat->id;
            $heading = get_string('enablecustomcss', 'theme_ucsf');
            $information = get_string('enablecustomcatcssdesc', 'theme_ucsf');
            $default = false;
            $setting = new admin_setting_configcheckbox($name, $heading, $information, $default, true, false);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Custom CSS.
            $name = 'theme_ucsf/customcss' . $cat->id;
            $heading = get_string('customcss', 'theme_ucsf');
            $information = get_string('customcatcssdesc', 'theme_ucsf');
            $default = '';
            $setting = new admin_setting_configtextarea($name, $heading, $information, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $settings->add($page);
        }
    }
}
