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

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

function theme_ucsfx_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');

    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_ucsfx', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_ucsfx and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/ucsfx/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/ucsfx/scss/post.scss');

    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post;
}

/**
 * Returns the help menu data as an object, or NULL if none could be if the help menu has been disabled.
 *
 * @param moodle_page $page
 *
 * @return \stdClass|null
 *
 * @throws coding_exception
 */
function theme_ucsfx_get_helpmenu(moodle_page $page)
{
    $theme_settings = $page->theme->settings;

    if (! _theme_ucsfx_get_setting($theme_settings, 'helpfeedbackenabled')) {
        return null;
    }

    $menu = new \stdClass();

    $title = _theme_ucsfx_get_setting($theme_settings, 'helpfeedbacktitle', '');
    $menu->title = empty($title) ? get_string('helpmenutitle', 'theme_ucsfx') : $title;

    $menu->items = array();
    $number_of_links = (int) _theme_ucsfx_get_setting($theme_settings, 'numberoflinks', 0);
    for ($i = 1; $i <= $number_of_links; $i++) {
        $url = _theme_ucsfx_get_setting($theme_settings, 'helpfeedback' . $i . 'link', '');
        $title = _theme_ucsfx_get_setting($theme_settings, 'helpfeedback' . $i . 'linklabel', '');
        $target = _theme_ucsfx_get_setting($theme_settings, 'helpfeedback' . $i . 'linktarget');

        if (! empty($url)) {
            $menu->items[] = array(
                'url'     => $url,
                'title'   => $title,
                'options' => array(
                    'target' => empty($target) ? '_self' : '_blank',
                ),
            );
        }
    }

    if (empty($menu->items)) {
        return null;
    }

    return $menu;
}

/**
 * Retrieves a theme setting.
 *
 * @param stdClass $theme_settings The theme settings object
 * @param string   $setting        The name of the setting.
 * @param mixed    $default        A default value, to be used as fallback if the setting is not defined.
 *
 * @return mixed The setting's value, or the given default if the setting has not been defined.
 */
function _theme_ucsfx_get_setting($theme_settings, $setting, $default = false)
{
    if (! isset($theme_settings->$setting)) {
        return $default;
    }

    return $theme_settings->$setting;
}
