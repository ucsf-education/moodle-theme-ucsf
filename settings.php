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
 * Settings file for the UCSF theme.
 *
 * @package theme_ucsf
 * @copyright The Regents of the University of California
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_ucsf\admin_setting_daterange;
use theme_ucsf\admin_setting_datetimerange;
use theme_ucsf\admin_setting_timerange;
use theme_ucsf\constants;
use theme_ucsf\utils\config;

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingucsf', get_string('configtitle', 'theme_ucsf'));
    $page = new admin_settingpage('theme_ucsf_general', get_string('generalsettings', 'theme_ucsf'));

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
    // These are the built-in presets.
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
            ['maxfiles' => 20, 'accepted_types' => ['.scss']]);
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

    // Course-catgories list.
    $categories = core_course_category::make_categories_list('', 0, ' | ');

    // Banner Alerts.
    $page = new admin_settingpage('theme_ucsf_alerts', get_string('alertsheading', 'theme_ucsf'));

    for ($i = 1; $i <= constants::BANNERALERT_ITEMS_COUNT; $i++) {
        $setting = new admin_setting_heading(
                'theme_ucsf/alert' . $i . 'info',
                get_string('alertnumber', 'theme_ucsf', $i),
                ''
        );
        $page->add($setting);

        $setting = new admin_setting_configcheckbox(
                'theme_ucsf/enable' . $i . 'alert',
                get_string('enablealert', 'theme_ucsf'),
                get_string('enablealertdesc', 'theme_ucsf'),
                '0'
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $setting = new admin_setting_configselect(
                'theme_ucsf/recurring_alert' . $i,
                get_string('recurring_alert', 'theme_ucsf'),
                get_string('recurring_alertdesc', 'theme_ucsf'),
                constants::BANNERALERT_TYPE_UNBOUND,
                [
                        constants::BANNERALERT_TYPE_UNBOUND => get_string('never_end', 'theme_ucsf'),
                        constants::BANNERALERT_TYPE_DATEBOUND => get_string('one_time', 'theme_ucsf'),
                        constants::BANNERALERT_TYPE_RECURRENCE_DAILY => get_string('daily', 'theme_ucsf'),
                        constants::BANNERALERT_TYPE_RECURRENCE_WEEKLY => get_string('weekly', 'theme_ucsf'),
                ]
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $alerttype = get_config('theme_ucsf', 'recurring_alert' . $i) ?: '';
        switch ($alerttype) {
            case constants::BANNERALERT_TYPE_DATEBOUND:
                $setting = new admin_setting_datetimerange(
                        'theme_ucsf/datebound_datepicker' . $i,
                        'start_date' . $i,
                        'start_hour' . $i,
                        'start_minute' . $i,
                        'end_date' . $i,
                        'end_hour' . $i,
                        'end_minute' . $i,
                        get_string('startenddate', 'theme_ucsf'),
                        get_string('startenddatedesc', 'theme_ucsf')
                );
                $setting->set_updatedcallback('theme_reset_all_caches');
                $page->add($setting);
                break;

            case constants::BANNERALERT_TYPE_RECURRENCE_DAILY:
                $name = 'theme_ucsf/daily_datepicker' . $i;
                $title = get_string('startenddate', 'theme_ucsf');
                $description = get_string('startenddatedesc', 'theme_ucsf');
                $startdate = 'start_date_daily' . $i;
                $enddate = 'end_date_daily' . $i;
                $setting = new admin_setting_daterange(
                        $name,
                        $startdate,
                        $enddate,
                        get_string('startenddate', 'theme_ucsf'),
                        get_string('startenddatedesc', 'theme_ucsf')
                );
                $setting->set_updatedcallback('theme_reset_all_caches');
                $page->add($setting);

                $setting = new admin_setting_timerange(
                        'theme_ucsf/daily_timepicker' . $i,
                        'start_hour_daily' . $i,
                        'start_minute_daily' . $i,
                        'end_hour_daily' . $i,
                        'end_minute_daily' . $i,
                        get_string('end_date_weekly', 'theme_ucsf'),
                        get_string('start_hour_and_minute_dailydesc', 'theme_ucsf')
                );
                $setting->set_updatedcallback('theme_reset_all_caches');
                $page->add($setting);
                break;

            case constants::BANNERALERT_TYPE_RECURRENCE_WEEKLY:
                $setting = new admin_setting_daterange(
                        'theme_ucsf/weekly_datepicker' . $i,
                        'start_date_weekly' . $i,
                        'end_date_weekly' . $i,
                        get_string('startenddate', 'theme_ucsf'),
                        get_string('startenddatedesc', 'theme_ucsf')
                );
                $setting->set_updatedcallback('theme_reset_all_caches');
                $page->add($setting);

                $setting = new admin_setting_timerange(
                        'theme_ucsf/weekly_timepicker' . $i,
                        'start_hour_weekly' . $i,
                        'start_minute_weekly' . $i,
                        'end_hour_weekly' . $i,
                        'end_minute_weekly' . $i,
                        get_string('end_date_weekly', 'theme_ucsf'),
                        get_string('end_weeklydesc', 'theme_ucsf'),
                );
                $setting->set_updatedcallback('theme_reset_all_caches');
                $page->add($setting);

                $setting = new admin_setting_configselect(
                        'theme_ucsf/show_week_day' . $i,
                        get_string('show_week_day', 'theme_ucsf'),
                        get_string('show_week_daydesc', 'theme_ucsf'),
                        constants::BANNERALERT_WEEKDAYS_SUNDAY,
                        [
                                constants::BANNERALERT_WEEKDAYS_SUNDAY => new lang_string('sunday', 'calendar'),
                                constants::BANNERALERT_WEEKDAYS_MONDAY => new lang_string('monday', 'calendar'),
                                constants::BANNERALERT_WEEKDAYS_TUESDAY => new lang_string('tuesday', 'calendar'),
                                constants::BANNERALERT_WEEKDAYS_WEDNESDAY => new lang_string('wednesday', 'calendar'),
                                constants::BANNERALERT_WEEKDAYS_THURSDAY => new lang_string('thursday', 'calendar'),
                                constants::BANNERALERT_WEEKDAYS_FRIDAY => new lang_string('friday', 'calendar'),
                                constants::BANNERALERT_WEEKDAYS_SATURDAY => new lang_string('saturday', 'calendar'),
                        ]
                );
                $setting->set_updatedcallback('theme_reset_all_caches');
                $page->add($setting);
                break;

            case constants::BANNERALERT_TYPE_UNBOUND:
            default:
                // Do nothing.
        }

        $setting = new admin_setting_configselect(
                'theme_ucsf/categories_list_alert' . $i,
                get_string('categories_list_alert', 'theme_ucsf'),
                get_string('categories_list_alertdesc', 'theme_ucsf'),
                constants::BANNERALERT_TARGET_DASHBOARD,
                [
                        constants::BANNERALERT_TARGET_DASHBOARD => get_string('dashboardonly', 'theme_ucsf'),
                        constants::BANNERALERT_TARGET_SITEWIDE => get_string('sitewide', 'theme_ucsf'),
                ] + $categories
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $setting = new admin_setting_configselect(
                'theme_ucsf/alert' . $i . 'type',
                get_string('alerttype', 'theme_ucsf'),
                get_string('alerttypedesc', 'theme_ucsf'),
                constants::BANNERALERT_LEVEL_INFORMATION,
                [
                        constants::BANNERALERT_LEVEL_INFORMATION => get_string('alert_info', 'theme_ucsf'),
                        constants::BANNERALERT_LEVEL_WARNING => get_string('alert_warning', 'theme_ucsf'),
                        constants::BANNERALERT_LEVEL_ANNOUNCEMENT => get_string('alert_general', 'theme_ucsf'),
                ]
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $setting = new admin_setting_configtextarea(
                'theme_ucsf/alert' . $i . 'text',
                get_string('alerttext', 'theme_ucsf'),
                get_string('alerttextdesc', 'theme_ucsf'),
                ''
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }

    $settings->add($page);

    // Category Customizations selector.
    $page = new admin_settingpage('theme_ucsf_category_customizations', get_string('categorycustomizationsheading', 'theme_ucsf'));

    // Switch to enable/disable category customizations.
    $setting = new admin_setting_configcheckbox(
            'theme_ucsf/enablecustomization',
            get_string('enablecustomization', 'theme_ucsf'),
            get_string('enablecustomizationdesc', 'theme_ucsf'),
            '0'
    );
    $page->add($setting);

    // With close to a thousand course categories in production, check boxes won't cut it.
    // So let's use a multi-select dropdown instead, at the expense of making selections more finicky.
    // @todo Revisit if/whenever sanity is restored in on this [ST 2023/01/10].
    $setting = new admin_setting_configmultiselect(
            'theme_ucsf/all_categories',
            get_string('categorycustomizations', 'theme_ucsf'),
            get_string('categorycustomizationsdesc', 'theme_ucsf'),
            [],
            $categories
    );

    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // Category-specific Customizations.
    //
    // Filter all course categories down to the customizable categories.
    $customizedcategoryids = array_filter(explode(',', trim(config::get_setting('all_categories', ''))));
    $customizedcategories = array_filter($categories, function($categoryid) use ($customizedcategoryids) {
        return in_array($categoryid, $customizedcategoryids);
    }, ARRAY_FILTER_USE_KEY);

    asort($customizedcategories);

    // Create a tab for each customizable category.
    foreach ($customizedcategories as $categoryid => $categoryname) {
        $page = new admin_settingpage('theme_ucsf_' . $categoryid, $categoryname);

        // Primary navigation additions.
        $setting = new admin_setting_heading(
                $name = 'theme_ucsf/custommenusubsection',
                get_string('custommenusubsectiontitle', 'theme_ucsf'),
                ''
        );
        $page->add($setting);

        $setting = new admin_setting_configtextarea(
                'theme_ucsf/custommenu' . $categoryid,
                get_string('custommenuitems', 'admin'),
                get_string('configcustommenuitems', 'admin'),
                ''
        );
        $page->add($setting);

        $settings->add($page);
    }
}
