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
}
