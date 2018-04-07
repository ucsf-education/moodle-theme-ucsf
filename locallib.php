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
class theme_ucsfx_add_category_customization extends admin_setting_configselect
{

    /**
     * @inheritdoc
     */
    public function write_setting($data)
    {

        $categories = get_config('theme_ucsfx');

        if ($data != 0) {
            set_config('displaycoursetitle'.$data, 1, 'theme_ucsfx');
        }

        if ( ! empty($categories->all_categories)) {
            set_config('all_categories', $categories->all_categories.','.$data, 'theme_ucsfx');
        } else {
            set_config('all_categories', $data, 'theme_ucsfx');
        }

        return parent::write_setting(0);

    }
}

class theme_ucsfx_remove_category_customization extends admin_setting_configselect
{

    /**
     * @inheritdoc
     */
    public function write_setting($data)
    {

        $categories = get_config('theme_ucsfx');

        if ( ! empty($categories->all_categories)) {
            $temp_array = explode(",", $categories->all_categories);
            if (in_array($data, $temp_array)) {

                $arr = array_diff($temp_array, array($data));
                $fin = implode(",", $arr);

                set_config('all_categories', $fin, 'theme_ucsfx');
            }
        }

        return parent::write_setting(0);
    }
}
