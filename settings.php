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

use theme_ucsf\constants;

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    require_once($CFG->dirroot . '/theme/ucsf/locallib.php');

    $theme_config = get_config('theme_ucsf');
    $settings = new theme_boost_admin_settingspage_tabs('themesettingucsf', get_string('configtitle', 'theme_ucsf'));
    $page = new admin_settingpage('theme_ucsf_general', get_string('generalsettings', 'theme_ucsf'));

    $categories = core_course_category::make_categories_list('', 0, ' | ');

    // Unaddable blocks.
    // Blocks to be excluded when this theme is enabled in the "Add a block" list: Administration, Navigation, Courses and
    // Section links.
    $default = 'navigation,settings,course_list,section_links';
    $setting = new admin_setting_configtext('theme_ucsf/unaddableblocks',
            get_string('unaddableblocks', 'theme_ucsf'), get_string('unaddableblocks_desc', 'theme_ucsf'), $default, PARAM_TEXT);
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

    $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'ucsf');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_ucsf/presetfiles';
    $title = get_string('presetfiles', 'theme_ucsf');
    $description = get_string('presetfiles_desc', 'theme_ucsf');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
            array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Background image setting.
    $name = 'theme_ucsf/backgroundimage';
    $title = get_string('backgroundimage', 'theme_ucsf');
    $description = get_string('backgroundimage_desc', 'theme_ucsf');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login Background image setting.
    $name = 'theme_ucsf/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_ucsf');
    $description = get_string('loginbackgroundimage_desc', 'theme_ucsf');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
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

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_ucsf_advanced', get_string('advancedsettings', 'theme_ucsf'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_ucsf/scsspre',
            get_string('rawscsspre', 'theme_ucsf'), get_string('rawscsspre_desc', 'theme_ucsf'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_ucsf/scss', get_string('rawscss', 'theme_ucsf'),
            get_string('rawscss_desc', 'theme_ucsf'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // ----------------------------------------------------
    // Helpmenu
    // ----------------------------------------------------
    $page = new admin_settingpage('theme_ucsf_helpmenu', get_string('helpfeedbacksettings', 'theme_ucsf'));

    // Enable/Disable Help/Feedback links;.
    $name = 'theme_ucsf/helpfeedbackenabled';
    $title = get_string('helpfeedbackenabled', 'theme_ucsf');
    $description = get_string('helpfeedbackenableddesc', 'theme_ucsf');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    for ($i = 1; $i <= constants::THEME_UCSF_SETTING_HELPMENU_ITEMS_COUNT; $i++) {
        $name = 'theme_ucsf/helpfeedback' . $i . 'heading';
        $heading = get_string('helpfeedbackno', 'theme_ucsf', $i);
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        $name = 'theme_ucsf/helpfeedback' . $i . 'link';
        $title = get_string('helpfeedbacklink', 'theme_ucsf');
        $description = get_string('helpfeedbacklinkdesc', 'theme_ucsf');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_ucsf/helpfeedback' . $i . 'linklabel';
        $title = get_string('helpfeedbacklinklabel', 'theme_ucsf');
        $description = get_string('helpfeedbacklinklabeldesc', 'theme_ucsf');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_ucsf/helpfeedback' . $i . 'linktarget';
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
    // Banner Alerts
    // ----------------------------------------------------

    $page = new admin_settingpage('theme_ucsf_alerts', get_string('alertsheading', 'theme_ucsf'));
    for ($i = 1; $i <= 10; $i++) {
        // This is the descriptor for Alert One
        $name = 'theme_ucsf/alert' . $i . 'info';
        $heading = get_string('alertnumber', 'theme_ucsf', $i);
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Enable Alert
        $name = 'theme_ucsf/enable' . $i . 'alert';
        $title = get_string('enablealert', 'theme_ucsf');
        $description = get_string('enablealertdesc', 'theme_ucsf');
        $setting = new admin_setting_configcheckbox($name, $title, $description, '0');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Recurrence pattern.
        $name = 'theme_ucsf/recurring_alert' . $i;
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

        $alert_settings = 'recurring_alert' . $i;

        if (isset($theme_config->$alert_settings)) {
            $alert_choice = $theme_config->$alert_settings;
        } else {
            $alert_choice = null;
        }
        if ($alert_choice == '1') {
            $null = '';

        } else if ($alert_choice == '2') {

            // Start date.
            $name = 'theme_ucsf/start_date' . $i;
            $title = get_string('startenddate', 'theme_ucsf');
            $date = 'start_date' . $i;
            $hour = 'start_hour' . $i;
            $minute = 'start_minute' . $i;
            $enddate = 'end_date' . $i;
            $endhour = 'end_hour' . $i;
            $endminute = 'end_minute' . $i;
            $description = get_string('startenddatedesc', 'theme_ucsf');
            $setting = new theme_ucsf_datetimepicker(
                    $name,
                    $date,
                    $hour,
                    $minute,
                    $enddate,
                    $endhour,
                    $endminute,
                    $title,
                    $description
            );
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

        } else if ($alert_choice == '3') {

            // Start/end daily date picker
            $name = 'theme_ucsf/start_date_daily' . $i;
            $title = get_string('startenddate', 'theme_ucsf');
            $date_start = 'start_date_daily' . $i;
            $date_end = 'end_date_daily' . $i;
            $description = get_string('startenddatedesc', 'theme_ucsf');
            $setting = new theme_ucsf_datepicker($name, $date_start, $date_end, $title, $description);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            //Start/end daily time picker
            $name = 'theme_ucsf/start_hour_and_minute_daily' . $i;
            $title = get_string('end_date_weekly', 'theme_ucsf');
            $hour_start = 'start_hour_daily' . $i;
            $minute_start = 'start_minute_daily' . $i;
            $hour_end = 'end_hour_daily' . $i;
            $minute_end = 'end_minute_daily' . $i;
            $description = get_string('start_hour_and_minute_dailydesc', 'theme_ucsf');
            $setting = new theme_ucsf_timepicker(
                    $name,
                    $hour_start,
                    $minute_start,
                    $hour_end,
                    $minute_end,
                    $title,
                    $description
            );
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

        } else if ($alert_choice == '4') {

            // Start/end weekly date picker
            $name = 'theme_ucsf/start_date_weekly' . $i;
            $title = get_string('startenddate', 'theme_ucsf');
            $date = 'start_date_weekly' . $i;
            $enddate = 'end_date_weekly' . $i;
            $default = null;
            $description = get_string('startenddatedesc', 'theme_ucsf');
            $setting = new theme_ucsf_datepicker($name, $date, $enddate, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Start/end weekly date picker
            $name = 'theme_ucsf/set_weekly_time' . $i;
            $title = get_string('end_date_weekly', 'theme_ucsf');
            $hour = 'end_hour_weekly' . $i;
            $minute = 'end_minute_weekly' . $i;
            $start_hour = 'start_hour_weekly' . $i;
            $start_minute = 'start_minute_weekly' . $i;
            $default = array();
            $description = get_string('end_weeklydesc', 'theme_ucsf');
            $setting = new theme_ucsf_timepicker(
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
            $name = 'theme_ucsf/show_week_day' . $i;
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
        $alert_categories = ['dashboard' => 'Dashboard Only'] + $categories;
        $name = 'theme_ucsf/categories_list_alert' . $i;
        $title = get_string('categories_list_alert', 'theme_ucsf');
        $description = get_string('categories_list_alertdesc', 'theme_ucsf');
        $default = 'dashboard';
        $setting = new admin_setting_configselect($name, $title, $description, $default, $alert_categories);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Alert Type.
        $name = 'theme_ucsf/alert' . $i . 'type';
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
        $name = 'theme_ucsf/alert' . $i . 'text';
        $title = get_string('alerttext', 'theme_ucsf');
        $description = get_string('alerttextdesc', 'theme_ucsf');
        $default = '';
        $setting = new admin_setting_configtextarea($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    $settings->add($page);

    // ----------------------------------------------------
    // Category Customizations
    // ----------------------------------------------------
    $page = new admin_settingpage(
            'theme_ucsf_category_customizations',
            get_string('categorycustomizationsheading', 'theme_ucsf')
    );

    /* With close to a thousand course categories in production, check boxes won't cut it.
       So let's use a multi-select dropdown instead, at the expense of making selections more finicky.
       @todo Revisit if/whenever sanity is restored in on this. [ST 2023/01/10]
    */
    //$setting = new admin_setting_configmulticheckbox(
    //        'theme_ucsf/all_categories',
    //        get_string('categorycustomizations', 'theme_ucsf'),
    //        get_string('categorycustomizationsdesc', 'theme_ucsf'),
    //        array(),
    //        $categories
    //);
    $setting = new admin_setting_configmultiselect(
            'theme_ucsf/all_categories',
            get_string('categorycustomizations', 'theme_ucsf'),
            get_string('categorycustomizationsdesc', 'theme_ucsf'),
            array(),
            $categories
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
