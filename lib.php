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
 * Moodle's ucsf theme, an example of how to make a Bootstrap theme
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

/**
 * Extra LESS code to inject.
 *
 * This will generate some LESS code from the settings used by the user. We cannot use
 * the {@link theme_more_less_variables()} here because we need to create selectors or
 * alter existing ones.
 *
 * @param theme_config $theme The theme config object.
 * @return string Raw LESS code.
 */
function theme_ucsf_extra_less($theme) {

    // get the ids of all course categories
    $all_category_ids = _theme_ucsf_get_all_category_ids();

    // get all categories that are configured for customizations
    $settings = $theme->settings;
    if (empty($settings->all_categories)) {
        return '';
    }
    $customized_category_ids = explode(',', $settings->all_categories);
    // filter out any categories that don't have CSS customizations turned on
    // and that don't provide any styles
    $customized_category_ids = array_filter($customized_category_ids, function($id) use ($settings) {
        $enabled_key = 'customcssenabled' . (int) $id;
        $css_key = 'customcss' . (int) $id;
        return ! empty($settings->$enabled_key) && ! empty($settings->$css_key);
    });
    $customized_category_ids = array_values($customized_category_ids);
    if (empty($customized_category_ids)) {
        return '';
    }

    // generate LESS rules by category
    // "inherit" any custom rules that may have been defined/enabled by parent categories.
    $contents = array();
    foreach($all_category_ids as $category_id) {
        $category_css = [];
        $ids = theme_ucsf_get_category_roots($category_id);
        foreach($ids as $id) {
            if (in_array($id, $customized_category_ids)) {
                $css_key = 'customcss' . (int) $id;
                $category_css[] = $settings->$css_key;
            }
        }
        if (! empty($category_css)) {
            $category_css = implode("\n", array_reverse($category_css));
            // anchor rules off of the body tag with category class applied
            $contents[] = "body.category-{$category_id} {\n{$category_css}\n}";
        }
    }

    return implode("\n", $contents);
}

/**
 * Returns variables for LESS.
 *
 * We will inject some LESS variables from the settings that the user has defined
 * for the theme. No need to write some custom LESS for this.
 *
 * @param theme_config $theme The theme config object.
 * @return array of LESS variables without the @.
 */
function theme_ucsf_less_variables($theme) {
    $variables = array();
    // @todo implement or remove, if n/a [ST 2016/04/06]
    return $variables;
}

/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_ucsf_process_css($css, $theme) {

    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');
    $css = theme_ucsf_set_logo($css, $logo);

    // Set custom CSS.
    if ($theme->settings->customcssenabled && !empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_ucsf_set_customcss($css, $customcss);

    // Block settings
    // Set block width for large desktop
    if (!empty($theme->settings->block_width_desktop)) {
        $block_width = $theme->settings->block_width_desktop;
    } else {
        $block_width = '';
    }
    $css = theme_ucsf_block_width($css, '[[setting:block_width_desktop]]', $block_width);

    // Block width for portrait tablet to landscape and desktop
    if (!empty($theme->settings->block_width_portrait_tablet)) {
        $block_width_portrait_tablet = $theme->settings->block_width_portrait_tablet;
    } else {
        $block_width_portrait_tablet = '';
    }
    $css = theme_ucsf_block_width($css, '[[setting:block_width_portrait_tablet]]', $block_width_portrait_tablet);

    return $css;
}

function theme_ucsf_block_width($css, $tag, $block_width) {
    $replacement = $block_width;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Adds the logo to CSS.
 *
 * @param string $css The CSS.
 * @param string $logo The URL of the logo.
 * @return string The parsed CSS
 */
function theme_ucsf_set_logo($css, $logo) {
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_ucsf_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $DB;
    $CATEGORILABELIMAGE = array();

    $sql = "SELECT cc.id
        FROM {course_categories} cc";

    $course_categories =  $DB->get_records_sql($sql);
    foreach ($course_categories as $cat) {
        $CATEGORILABELIMAGE[]= "categorylabelimage".$cat->id;
    }


    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $theme = theme_config::load('ucsf');
        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage') {
            return $theme->setting_file_serve('bannerimage', $args, $forcedownload, $options);
        } else if ($filearea === 'tile1image') {
            return $theme->setting_file_serve('tile1image', $args, $forcedownload, $options);
        } else if ($filearea === 'tile2image') {
            return $theme->setting_file_serve('tile2image', $args, $forcedownload, $options);
        } else if ($filearea === 'tile3image') {
            return $theme->setting_file_serve('tile3image', $args, $forcedownload, $options);
        } else if ($filearea === 'tile4image') {
            return $theme->setting_file_serve('tile4image', $args, $forcedownload, $options);
        } else if ($filearea === 'tile5image') {
            return $theme->setting_file_serve('tile5image', $args, $forcedownload, $options);
        } else if ($filearea === 'tile6image') {
            return $theme->setting_file_serve('tile6image', $args, $forcedownload, $options);
        }else if ($filearea === 'tile7image') {
            return $theme->setting_file_serve('tile7image', $args, $forcedownload, $options);
        }else if ($filearea === 'tile8image') {
            return $theme->setting_file_serve('tile8image', $args, $forcedownload, $options);
        }else if ($filearea === 'tile9image') {
            return $theme->setting_file_serve('tile9image', $args, $forcedownload, $options);
        }else if ($filearea === 'tile10image') {
            return $theme->setting_file_serve('tile10image', $args, $forcedownload, $options);
        }else if (in_array($filearea,  $CATEGORILABELIMAGE)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_ucsf_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_ucsf_get_html_for_settings(renderer_base $output, moodle_page $page) {
    global $CFG;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    if (!empty($page->theme->settings->logo)) {
        $return->heading = html_writer::link($CFG->wwwroot, '', array('title' => get_string('home'), 'class' => 'logo'));
    } else {
        $return->heading = $output->page_heading();
    }

    $return->copyright = '';
    if (!empty($page->theme->settings->copyright)) {
        $return->copyright = $page->theme->settings->copyright;
    }

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = $page->theme->settings->footnote;
    }

    return $return;
}

function theme_ucsf_get_setting($setting, $format = false)
{
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('ucsf');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting);
    } else {
        return format_string($theme->settings->$setting);
    }
}

/**
 * Returns an object containing HTML for the areas affected by category customization settings.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - enablecustomization: true if Category Customization is enabled. By default false.
 *      - categorylabel: String to use for the Top Level Category Label. By default ''.
 *      - displaycoursetitle: The course title will appear on the course page for all courses,
*         unless the course title is set NOT to display on configured categories.
 *      - displaycustommenu: Hide Custom Menu when logged out. By default returns custom menu.
 */
function theme_ucsf_get_global_settings(renderer_base $output, moodle_page $page) {
    global $CFG, $COURSE;
    $return = new stdClass;

    $return->categorylabel = '';
    $return->coursetitle = '';
    $return->displaycustommenu = $output->custom_menu();

    /*
    *   Help/Feedback Links
    *   Pulling the number of links dynamically from the Help/Feedback Settings inside Theme Settings
    */
    $target = '';

    $helpfeedbacktitle = null;

    if(!isset($page->theme->settings->helpfeedbacktitle) || $page->theme->settings->helpfeedbacktitle == "") {

        $helpfeedbacktitle = 'Help/Feedback';

    } else {
        $helpfeedbacktitle = $page->theme->settings->helpfeedbacktitle;
    }
    $helpfeedback = null;

    for ($i = 1; $i <= $page->theme->settings->numberoflinks; $i++ ) {
        $helpfeedbacklink = theme_ucsf_get_setting('helpfeedback' . $i . 'link');
        $helpfeedbacklinklabel = theme_ucsf_get_setting('helpfeedback' . $i . 'linklabel');
        $helpfeedbacklinktarget = theme_ucsf_get_setting('helpfeedback' . $i . 'linktarget');

        if (!empty($helpfeedbacklink)) {
            if(!empty($helpfeedbacklinklabel)) {
                if ($helpfeedbacklinktarget == true) {
                    $target = 'target = "_blank"';
                    $helpfeedback .= '<li role="presentation"><a title="'.$helpfeedbacklinklabel.'" href="'.$helpfeedbacklink.'" '.$target.'>'.$helpfeedbacklinklabel.'</a></li>
                    <li class="divider"></li>';
                }else{
                    $target = 'target = "_self"';
                    $helpfeedback .= '<li role="presentation"><a title="'.$helpfeedbacklinklabel.'" href="'.$helpfeedbacklink.'" '.$target.'>'.$helpfeedbacklinklabel.'</a></li><li class="divider">';
                }
            }else{
                $helpfeedback .=  '<li role="presentation"><a href="' . $helpfeedbacklink . '" ' . $target . '>'.$helpfeedbacklink.'</a></li><li class="divider">';
            }
        }
    }
    if ($page->theme->settings->enablehelpfeedback == 1 && $helpfeedback != false) {
        $return->helpfeedbacklink = '<div class="dropdown helpfeedback-box"><a class="dropdown-toggle" data-toggle="dropdown">'.$helpfeedbacktitle.'<span class="caret"></span></a>'
                . '<ul class="dropdown-menu help-feedback pull-right" role="menu">'
                . $helpfeedback
                . '</ul></div>';
    } else {
        $return->helpfeedbacklink ='';
    }

    // customization enable
    $return->enablecustomization = false;
    if ($page->theme->settings->enablecustomization) {
        $return->enablecustomization = true;
    }

    // category customization enabled
    if($return->enablecustomization) {

        // set toplevel category label
        $return->categorylabel = '';
        if (!empty($page->theme->settings->toplevelcategorylabel)) {
            $return->categorylabel = '<div class="category-label pull-left"><div class="category-label-text">'.$page->theme->settings->toplevelcategorylabel.'</div></div>';
        }

        $coursecategory = theme_ucsf_get_current_course_category($page, $COURSE);
        $categories = theme_ucsf_get_category_roots($coursecategory);

        // Help/Feedback Links
        if ($coursecategory != 0) {
            for($j = 0; $j < count($categories); $j++) {
                if (theme_ucsf_get_setting('catenablehelpfeedback'.$categories[$j]) == 1) {
                    $target = '';
                    if(theme_ucsf_get_setting('cathelpfeedbacktitle'.$categories[$j]) == null || theme_ucsf_get_setting('cathelpfeedbacktitle'.$categories[$j]) == "") {
                        $helpfeedbacktitle = 'Help/Feedback';
                    } else {
                        $helpfeedbacktitle = theme_ucsf_get_setting('cathelpfeedbacktitle'.$categories[$j]);
                    }
                    $cathelpfeedback = null;
                    $catnumberoflinks = theme_ucsf_get_setting('catnumberoflinks'.$categories[$j]);

                    for ($i = 1; $i <= $catnumberoflinks; $i++ ) {
                        $helpfeedbacklink = theme_ucsf_get_setting('cathelpfeedback' . $i . 'link' . $categories[$j]);
                        $helpfeedbacklinklabel = theme_ucsf_get_setting('cathelpfeedback' . $i . 'linklabel' . $categories[$j]);
                        $helpfeedbacklinktarget = theme_ucsf_get_setting('cathelpfeedback' . $i . 'linktarget' . $categories[$j]);

                        if (!empty($helpfeedbacklink)) {
                            if(!empty($helpfeedbacklinklabel)) {
                                if ($helpfeedbacklinktarget == true) {
                                    $target = 'target = "_blank"';
                                    $cathelpfeedback .= '<li role="presentation"><a title="'.$helpfeedbacklinklabel.'" href="'.$helpfeedbacklink.'" '.$target.'>'.$helpfeedbacklinklabel.'</a></li><li class="divider">';
                                }else{
                                    $target = 'target = "_self"';
                                    $cathelpfeedback .= '<li role="presentation"><a title="'.$helpfeedbacklinklabel.'" href="'.$helpfeedbacklink.'" '.$target.'>'.$helpfeedbacklinklabel.'</a></li><li class="divider">';}
                            }else{
                                $cathelpfeedback .=  '<li role="presentation"><a href="'.$helpfeedbacklink.'" '.$target.'>'.$helpfeedbacklink.'</a></li><li class="divider">';
                            }
                        }
                    }
                    if($cathelpfeedback != null){
                        if (theme_ucsf_get_setting('catenablehelpfeedback'.$categories[$j]) == 1) {
                            $return->helpfeedbacklink = '<div class="dropdown helpfeedback-box"><a class="dropdown-toggle" data-toggle="dropdown">'.$helpfeedbacktitle.'<span class="caret"></span></a>'
                                    . '<ul class="dropdown-menu help-feedback pull-right" role="menu">'
                                    . $cathelpfeedback
                                    . '</ul></div>';
                        } else {
                            $return->helpfeedbacklink;
                        }
                    }
                    break;
                }
            }
        }

        // set course title
        $return->coursetitle = '';
        if(!empty($page->theme->settings->displaycoursetitle))
            if ($page->theme->settings->displaycoursetitle)
                if(!empty($COURSE->fullname))
                    $return->coursetitle = '<div class="custom_course_title">'. $COURSE->fullname . '</div>';

        if(!is_null($coursecategory && $coursecategory!=0)) {
            $displaycustomcoursetitle = "displaycoursetitle".$coursecategory;
            if(isset($page->theme->settings->$displaycustomcoursetitle))
                if(!$page->theme->settings->$displaycustomcoursetitle)
                    $return->coursetitle = '';
        }

        // category labels
        $coursecategory = theme_ucsf_get_first_category_customization($page, $categories);

        // override top level category label with custom category label
        if(!is_null($coursecategory && $coursecategory!=0)) {
            $categorylabelcustom = "categorylabel".$coursecategory;
            $categorylabelimagecustom = "categorylabelimage".$coursecategory;
            $categorylabelimageheightcustom = "categorylabelimageheight".$coursecategory;
            $categorylabelimagealtcustom = "categorylabelimagealt".$coursecategory;
            $categorylabelimagetitlecustom = "categorylabelimagetitle".$coursecategory;

            if (!empty($page->theme->settings->$categorylabelcustom)) {

                $categorylabelimage = "";
                $imgheight = "";
                $imgalt = "";
                $imgtitle = "";

                if (!empty($page->theme->settings->$categorylabelimagecustom)) {
                    $categorylabelimage = '<div class="category-label-image"><img src="'.$page->theme->setting_file_url('categorylabelimage'.$coursecategory, 'categorylabelimage'.$coursecategory).'"';
                }
                if (!empty($page->theme->settings->$categorylabelimageheightcustom)) {
                    $categorylabelimage.= 'height="'.$page->theme->settings->$categorylabelimageheightcustom.'"';
                }
                if (!empty($page->theme->settings->$categorylabelimagealtcustom)) {
                    $categorylabelimage.= 'alt="'.$page->theme->settings->$categorylabelimagealtcustom.'"';
                }
                if (!empty($page->theme->settings->$categorylabelimagetitlecustom)) {
                    $categorylabelimage.= 'title="'.$page->theme->settings->$categorylabelimagetitlecustom.'"';
                }
                if (!empty($page->theme->settings->$categorylabelimagecustom)) {
                    $categorylabelimage.= '/></div>';
                }

                $return->categorylabel = '<div class="category-label pull-left">'.$categorylabelimage.'<div class="category-label-text">'.$page->theme->settings->$categorylabelcustom.'</div></div>';
            }
        }

        // set link label to category page
        $linklabeltocategorypage = "linklabeltocategorypage".$coursecategory;
        if (isset($page->theme->settings->$linklabeltocategorypage))
            if($page->theme->settings->$linklabeltocategorypage)
                $return->categorylabel = '<a href="'.$CFG->wwwroot.'/course/index.php?categoryid='.$coursecategory.'"">'.$return->categorylabel.'</a>';
            else
                $return->categorylabel = $return->categorylabel;

    }

    // display custom menu
    $return->displaycustommenu = $output->custom_menu();
    if ($page->theme->settings->hidecustommenuwhenloggedout) {
        if(!isloggedin())
            $return->displaycustommenu = '';
    }

    // logo
    $return->logo = '<img title="UCSF | CLE" alt="UCSF | CLE" src="'.$output->pix_url('ucsf-logo', 'theme_ucsf').'"/>';

    // menu background clean css
    $menubackgroundcleen = "";

    if($return->categorylabel == '') {
        $menubackgroundcleen = "menu-background-cleen";
    }

    $return->menubackgroundcleen = $menubackgroundcleen;

    return $return;
}

/**
 * Returns a list of all ancestral categories of a given category.
 * The first element in that list is the given category itself, followed by its parent, the parent's parent and so on.
 * @param int $id The category id.
 * @return array A list of category ids, will be empty if the given category is bogus.
 */
function theme_ucsf_get_category_roots($id) {
    static $cache = null;

    if (! isset($cache)) {
        $cache = array();
    }

    if (!array_key_exists($id, $cache)) {
        $ids = _theme_ucsf_get_category_roots($id);
        $cache[$id] = _theme_ucsf_get_category_roots($id);
        array_shift($ids);
        // cache category roots of all ancestors in that category hierarchy while at it.
        for ($i = 0, $n = count($ids); $i < $n; $i++) {
            $parent_id = $ids[$i];
            if (array_key_exists($parent_id, $cache)) {
                break;
            }
            $cache[$parent_id] = array_slice($ids, $i);
        }
    }
    return $cache[$id];
}

/**
 * Retrieves the current course category id.
 * @param moodle_page $page The current page object.
 * @param object $course The current course object.
 * @return int The course category id.
 */
function theme_ucsf_get_current_course_category(moodle_page $page, $course) {
    // ACHTUNG!
    // Unbelievably crappy code to follow.
    // For course category pages, peel the category out of the URL request parameter.
    // In all other cases, take it from the current course.
    // @todo Clean this horrid mess up [ST 2016/03/24]
    if ($page->pagelayout=="coursecategory" && isset($_REQUEST["categoryid"])) {
        return $_REQUEST["categoryid"];
    }
    return $course->category;
}


/**
 * Finds and returns the first customized category out of a given hierarchy of categories.
 * @param moodle_page $page The current moodle page.
 * @param array $categories A hierarchy of category ids, from lowest to highest.
 * @return int the ID of the first found customized category.
 */
function theme_ucsf_get_first_category_customization(moodle_page $page, array $categories) {

    $all_categories_array = array();
    if(!empty($page->theme->settings->all_categories)){
        $all_categories_array = explode(",", $page->theme->settings->all_categories);
    }

    foreach ($categories as $cat) {
        if(in_array($cat, $all_categories_array)) {
            $categorylabelcustom = "categorylabel".$cat;
            if (!empty($page->theme->settings->$categorylabelcustom)) {
                return $cat;
            }
        }
    }

    return 0;
}

/**
 * @param moodle_page $page
 * @param array $categories
 * @return int
 * @throws dml_exception
 */
function theme_ucsf_get_first_category_customization_menu(moodle_page $page, array $categories) {
    $theme_config = get_config('theme_ucsf');
    $all_categories_array = array();
    if(!empty($theme_config->all_categories)){
        $all_categories_array = explode(",", $theme_config->all_categories);
    }

    if(is_array($categories)) {
        foreach ($categories as $cat) {
            if(in_array($cat, $all_categories_array)) {
                $categorycustommenu = "custommenu".$cat;
                if (!empty($page->theme->settings->$categorycustommenu)) {
                    return $cat;
                }
            }
        }
    }

    return 0;
}

function theme_ucsf_get_alerts(renderer_base $output, moodle_page $page) {
    global $CFG, $COURSE;

    $cats = get_config('theme_ucsf');

    $all_cats = $cats->all_categories;
    $all_categories_array = explode(",", $all_cats);
    $sub_cat = [];

    $coursecategory = theme_ucsf_get_current_course_category($page, $COURSE);
    $categories = theme_ucsf_get_category_roots($coursecategory);

    foreach($all_categories_array as $sub_category){
       if (in_array($sub_category, $categories)){
           $sub_cat[] = $sub_category;
        }
     }

    $current_hour = date('G');
    $current_minute = date('i');
    $current_time = $current_hour. ':' .$current_minute;
    $current_time_timestamp = strtotime($current_time);
    $current_date = new DateTime();
    $current_date_timestamp = $current_date->getTimestamp();
    $current_day_timestamp = strtotime("midnight");

    $hasalert = array(false, false, false, false, false, false, false, false, false, false);

    $number_of_alerts = isset($page->theme->settings->number_of_alerts) ? intval($page->theme->settings->number_of_alerts) : '';

    for ($i = 1; $i <= $number_of_alerts; $i++) {
        $category = theme_ucsf_get_setting('categories_list_alert'.$i);
        $alert_type = theme_ucsf_get_setting('recurring_alert'.$i);
        $enable_alert = theme_ucsf_get_setting('enable'.$i.'alert');

        if ($coursecategory == $category || $category == 0 || in_array($category, $sub_cat)) {

            if(!isset($_SESSION["alerts"]["alert".$i]) || $_SESSION["alerts"]["alert".$i] != 0) {

                //Never-Ending Alert
                if ($alert_type == '1') {
                    if($enable_alert == 1) {
                        $_SESSION["alerts"]["alert".$i] = 1;
                        $hasalert[$i] = true;
                    }
                }
                //One-Time Alert
                if ($alert_type == '2') {

                    $start_date     = (null !== (theme_ucsf_get_setting('start_date'.$i))) ? theme_ucsf_get_setting('start_date'.$i) : '';
                    $start_hour     = (null !== (theme_ucsf_get_setting('start_hour'.$i))) ? theme_ucsf_get_setting('start_hour'.$i) : '';
                    $start_minute   = (null !== (theme_ucsf_get_setting('start_minute'.$i))) ? theme_ucsf_get_setting('start_minute'.$i) : '';

                    // Do not set false if the value is 0.
                    if ($start_minute == false) {
                        $start_minute = '00';
                    }
                    if ($start_hour == false) {
                        $start_hour = '00';
                    }

                    // Formating date and getting timestamp from it
                    $start_date_format = date($start_date .' '.$start_hour.':'.$start_minute.':00');
                    $start_date_timestamp   = strtotime($start_date_format);

                    // Creating end date.
                    $end_date   = (null !== (theme_ucsf_get_setting('end_date'.$i))) ? theme_ucsf_get_setting('end_date'.$i) : '';
                    $end_hour     = (null !== (theme_ucsf_get_setting('end_hour'.$i))) ? theme_ucsf_get_setting('end_hour'.$i) : '';
                    $end_minute   = (null !== (theme_ucsf_get_setting('end_minute'.$i))) ? theme_ucsf_get_setting('end_minute'.$i) : '';
                    // Do not set false if the value is 0.
                    if ($end_minute == false) {
                        $end_minute = '00';
                    }
                    if ($end_hour == false) {
                        $end_hour = '00';
                    }

                    // Formating date and getting timestamp from it
                    $end_date_format = date($end_date .' '.$end_hour.':'.$end_minute.':00');
                    $end_date_timestamp = strtotime($end_date_format);

                    if ($enable_alert == 1) {
                        if ($start_date_timestamp <= $current_date_timestamp && $end_date_timestamp >= $current_date_timestamp) {
                            $_SESSION["alerts"]["alert".$i] = 1;
                            $hasalert[$i] = true;
                        }
                    }
                }

                if ($alert_type == '3') {

                    //Getting daily start date from config and converting it to timestamp.
                    $start_date = (null !== (theme_ucsf_get_setting('start_date_daily'.$i))) ? theme_ucsf_get_setting('start_date_daily'.$i) : '';
                    $start_hour = (null !== (theme_ucsf_get_setting('start_hour_daily'.$i))) ? theme_ucsf_get_setting('start_hour_daily'.$i) : '';
                    $start_minute = (null !== (theme_ucsf_get_setting('start_minute_daily'.$i))) ? theme_ucsf_get_setting('start_minute_daily'.$i) : "";

                    // Do not set false if the value is 0.
                    if ($start_minute == false) {
                        $start_minute = '00';
                    }
                    if ($start_hour == false) {
                        $start_hour = '00';
                    }

                    $start_time = $start_hour. ':' .$start_minute;

                    $start_date_timestamp = strtotime($start_date);
                    $start_time_timestamp = strtotime($start_time);

                    //Getting daily end date from config and converting it to timestamp.
                    $end_date = (null !== (theme_ucsf_get_setting('end_date_daily'.$i))) ? theme_ucsf_get_setting('end_date_daily'.$i) : '';
                    $end_hour = (null !== (theme_ucsf_get_setting('end_hour_daily'.$i))) ? theme_ucsf_get_setting('end_hour_daily'.$i) : '';
                    $end_minute = (null !== (theme_ucsf_get_setting('end_minute_daily'.$i))) ? theme_ucsf_get_setting('end_minute_daily'.$i) : "";

                    if ($end_minute == false) {
                        $end_minute = '00';
                    }
                    if ($end_hour == false) {
                        $end_hour = '00';
                    }

                    $end_time = $end_hour. ':' .$end_minute;

                    // Formating date and getting timestamp from it
                    $end_date_timestamp = strtotime($end_date);
                    $end_time_timestamp = strtotime($end_time);

                    if ($enable_alert == 1) {
                        if($start_date_timestamp <= $current_day_timestamp && $end_date_timestamp >= $current_day_timestamp) {
                            if($start_time_timestamp <= $current_time_timestamp && $end_time_timestamp > $current_time_timestamp){
                                $_SESSION["alerts"]["alert".$i] = 1;
                                $hasalert[$i] = true;
                            }
                        }
                    }
                }
                if ($alert_type == '4') {

                    // Get settings for weekday and put them into timestamp.
                    if (theme_ucsf_get_setting('show_week_day'.$i) == '0') {
                        $weekday = 'Sunday';
                        $weekday_timestamp = strtotime($weekday);
                    } elseif (theme_ucsf_get_setting('show_week_day'.$i) == '1') {
                        $weekday = 'Monday';
                        $weekday_timestamp = strtotime($weekday);
                    } elseif (theme_ucsf_get_setting('show_week_day'.$i) == '2') {
                        $weekday = 'Tuesday';
                        $weekday_timestamp = strtotime($weekday);
                    } elseif (theme_ucsf_get_setting('show_week_day'.$i) == '3') {
                        $weekday = 'Wednesday';
                        $weekday_timestamp = strtotime($weekday);
                    } elseif (theme_ucsf_get_setting('show_week_day'.$i) == '4') {
                        $weekday = 'Thursday';
                        $weekday_timestamp = strtotime($weekday);
                    } elseif (theme_ucsf_get_setting('show_week_day'.$i) == '5') {
                        $weekday = 'Friday';
                        $weekday_timestamp = strtotime($weekday);
                    } elseif (theme_ucsf_get_setting('show_week_day'.$i) == '6') {
                        $weekday = 'Saturday';
                        $weekday_timestamp = strtotime($weekday);
                    }

                    //Current weekday converted to the timestamp.
                    $current_weekday = date('D');
                    $current_weekday_timestamp = strtotime($current_weekday);

                    $start_date = (null !== (theme_ucsf_get_setting('start_date_weekly'.$i))) ? theme_ucsf_get_setting('start_date_weekly'.$i) : '';
                    $start_hour = (null !== (theme_ucsf_get_setting('start_hour_weekly'.$i))) ? theme_ucsf_get_setting('start_hour_weekly'.$i) : '';
                    $start_minute = (null !==(theme_ucsf_get_setting('start_minute_weekly'.$i))) ? theme_ucsf_get_setting('start_minute_weekly'.$i) : '';

                    if ($start_minute == false) {
                        $start_minute = '00';
                    }
                    if ($start_hour == false) {
                        $start_hour = '00';
                    }

                    $start_time = $start_hour. ':' .$start_minute;

                    $start_date_timestamp = strtotime($start_date);
                    $start_time_timestamp = strtotime($start_time);

                    //Getting daily end date from config and converting it to timestamp.
                    $end_date = (null !== (theme_ucsf_get_setting('end_date_weekly'.$i))) ? theme_ucsf_get_setting('end_date_weekly'.$i) : '';
                    $end_hour = (null !== (theme_ucsf_get_setting('end_hour_weekly'.$i))) ? theme_ucsf_get_setting('end_hour_weekly'.$i) : '';
                    $end_minute = (null !== (theme_ucsf_get_setting('end_minute_weekly'.$i))) ? theme_ucsf_get_setting('end_minute_weekly'.$i) : "";

                    if ($end_minute == false) {
                        $end_minute = '00';
                    }

                    if ($end_hour == false) {
                        $end_hour = '00';
                    }

                    $end_time = $end_hour. ':' .$end_minute;

                    // Formating date and getting timestamp from it
                    $end_date_timestamp = strtotime($end_date);
                    $end_time_timestamp = strtotime($end_time);

                    if ($enable_alert == 1){
                        if ($weekday_timestamp == $current_weekday_timestamp) {
                            if ($start_date_timestamp <= $current_day_timestamp && $end_date_timestamp >= $current_day_timestamp) {
                                if ($start_time_timestamp <= $current_date_timestamp && $end_time_timestamp > $current_date_timestamp) {
                                    $_SESSION["alerts"]["alert".$i] = 1;
                                    $hasalert[$i] = true;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $alert = null;

    for ($i = 0; $i < 10; $i++) {
        if($hasalert[$i]) {

            $alert.= '<div class="useralerts alert alert-'.theme_ucsf_get_setting('alert'.$i.'type').' alert1">';
            $alert.='<a class="close" data-dismiss="alert" data-target-url="'.$CFG->wwwroot.'" href="#">×</a>';
            $alert.='<span class="title">'.theme_ucsf_get_setting('alert'.$i.'title').'</span>'.theme_ucsf_get_setting('alert'.$i.'text');
            $alert.='</div>';
            $showalert = true;
        }
    }

    if (in_array(true, $hasalert, true)) {
        $alert = '<div class="alerts">'. $alert . '</div>';
    } else if ($page->pagelayout=="frontpage") {
        $alert = '<div class="alerts"></div>';
    }

    return $alert;
}

function theme_ucsf_get_category_label_image(renderer_base $output, moodle_page $page) {
    $categorylabelimage = "";

    if(!empty($page->theme->settings->categorylabelimage))
        $categorylabelimage = '<img src="'.$page->theme->setting_file_url('categorylabelimage', 'categorylabelimage').'" alt="'.$page->theme->settings->bannerimagealt.'" title="'.$page->theme->settings->bannerimagetitle.'" class="banner-image">';

    return $categorylabelimage;
}

function theme_ucsf_get_banner(renderer_base $output, moodle_page $page) {
    $banner = null;


    $bannerimage = "";
    if(!empty($page->theme->settings->bannerimage))
        $bannerimage = '<img src="'.$page->theme->setting_file_url('bannerimage', 'bannerimage').'" alt="'.$page->theme->settings->bannerimagealt.'" title="'.$page->theme->settings->bannerimagetitle.'" class="banner-image">';

    $bannertext = "";
    if(!empty($page->theme->settings->banner))
        $bannertext = $page->theme->settings->banner;

    if(!empty($page->theme->settings->bannerimage) || !empty($page->theme->settings->banner))
        $banner = '<div class="banner">'.$bannertext.'<div class="banner-image-container">'.$bannerimage.'</div></div>';

    return $banner;
}

function theme_ucsf_get_tiles(renderer_base $output, moodle_page $page) {
    $tiles = null;
    $tilesinnerfirst = null;
    $tilesinnersecond = null;
    $myarr = [null, null, null, null, null, null];

    for ($i = 1; $i <= $page->theme->settings->numberoftiles; $i++){
    $test = theme_ucsf_get_setting('positionoftile'. $i) - 1;
        if (theme_ucsf_get_setting('tile' . $i . 'select') == 1){

            if(theme_ucsf_get_setting('positionoftile'. $i) != false){
                $myarr[$test] = $i;
            }else if(theme_ucsf_get_setting('positionoftile'. $i) == false) {
                array_push($myarr, $i);
            }
        }
    }

    foreach($myarr as $key=>$val) {
        if ($val === null)
            unset($myarr[$key]);
    }

    if(count($myarr) == 4 || count($myarr) == 2){
        foreach($myarr as $index => $arr){
            if ($index < 2) {
                ${'tile' . $arr . 'image'} = "";
                if(!empty(theme_ucsf_get_setting('tile' . $arr . 'image'))) {
                    ${'tile' . $arr . 'image'} = '<img src="'.$page->theme->setting_file_url('tile' . $arr . 'image', 'tile' . $arr . 'image').'" alt="'.theme_ucsf_get_setting('tile' . $arr . 'imagealt').'" title="'.theme_ucsf_get_setting('tile' . $arr . 'imagetitle').'" class="tile-image">';
                }
                if(!empty(theme_ucsf_get_setting('tile' . $arr . 'content')) || !empty(theme_ucsf_get_setting('tile' . $arr . 'image'))) {
                    $tileborder = 'tile-border';
                }
                if($arr == null) {
                    $tileborder = '';
                }
                $tilesinnerfirst .='<div class="span6 tile '.$tileborder.' tile-even">'.theme_ucsf_get_setting('tile' . $arr . 'content').'<div class="tile-image-container">'.${'tile' . $arr . 'image'}.'</div></div>';
            } else if($index >1){
                ${'tile' . $arr . 'image'} = "";
                if(!empty(theme_ucsf_get_setting('tile' . $arr . 'image'))) {
                    ${'tile' . $arr . 'image'} = '<img src="'.$page->theme->setting_file_url('tile' . $arr . 'image', 'tile' . $arr . 'image').'" alt="'.theme_ucsf_get_setting('tile' . $arr . 'imagealt').'" title="'.theme_ucsf_get_setting('tile' . $arr . 'imagetitle').'" class="tile-image">';
                }
                if(!empty(theme_ucsf_get_setting('tile' . $arr . 'content')) || !empty(theme_ucsf_get_setting('tile' . $arr . 'image'))) {
                    $tileborder = 'tile-border';
                }
                if($arr == null) {
                    $tileborder = '';
                }

                $tilesinnersecond .='<div class="span6 tile '.$tileborder.' tile-even">'.theme_ucsf_get_setting('tile' . $arr . 'content').'<div class="tile-image-container">'.${'tile' . $arr . 'image'}.'</div></div>';
            }
        }
    }else{
        foreach($myarr as $index => $arr) {
            if($index < 3){
                ${'tile' . $arr . 'image'} = "";
                if(!empty(theme_ucsf_get_setting('tile' . $arr . 'image'))) {
                    ${'tile' . $arr . 'image'} = '<img src="'.$page->theme->setting_file_url('tile' . $arr . 'image', 'tile' . $arr . 'image').'" alt="'.theme_ucsf_get_setting('tile' . $arr . 'imagealt').'" title="'.theme_ucsf_get_setting('tile' . $arr . 'imagetitle').'" class="tile-image">';
                }
                if(!empty(theme_ucsf_get_setting('tile' . $arr . 'content')) || !empty(theme_ucsf_get_setting('tile' . $arr . 'image'))) {
                    $tileborder = 'tile-border';
                }
                if($arr == null) {
                    $tileborder = '';
                }

                $tilesinnerfirst .='<div class="span4 tile '.$tileborder.'">'.theme_ucsf_get_setting('tile' . $arr . 'content').'<div class="tile-image-container">'.${'tile' . $arr . 'image'}.'</div></div>';
            } else if($index < 6) {
                ${'tile' . $arr . 'image'} = "";
                if(!empty(theme_ucsf_get_setting('tile' . $arr . 'image'))) {
                    ${'tile' . $arr . 'image'} = '<img src="'.$page->theme->setting_file_url('tile' . $arr . 'image', 'tile' . $arr . 'image').'" alt="'.theme_ucsf_get_setting('tile' . $arr . 'imagealt').'" title="'.theme_ucsf_get_setting('tile' . $arr . 'imagetitle').'" class="tile-image">';
                }
                if(!empty(theme_ucsf_get_setting('tile' . $arr . 'content')) || !empty(theme_ucsf_get_setting('tile' . $arr . 'image'))) {
                    $tileborder = 'tile-border';
                }
                if($arr == null) {
                    $tileborder = '';
                }

                $tilesinnersecond .='<div class="span4 tile '.$tileborder.'">'.theme_ucsf_get_setting('tile' . $arr . 'content').'<div class="tile-image-container">'.${'tile' . $arr . 'image'}.'</div></div>';
            }
        }
    }

        $tiles .= '<div class="row-fluid">'.$tilesinnerfirst.'</div>';
        $tiles .= '<div class="row-fluid">'.$tilesinnersecond.'</div>';

        return $tiles;
}


/**
 * All theme functions should start with theme_ucsf_
 * @deprecated since 2.5.1
 */
function ucsf_process_css() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_ucsf_
 * @deprecated since 2.5.1
 */
function ucsf_set_logo() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_ucsf_
 * @deprecated since 2.5.1
 */
function ucsf_set_customcss() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

function theme_ucsf_page_init(moodle_page $page) {
    $page->requires->jquery();
    $page->requires->jquery_plugin('alert', 'theme_ucsf');
}

//
// "private" methods.
//

/**
 * Recursively retrieve all ancestral categories for a given category, including the category itself.
 * @param int $id The category id.
 * @param array $categories A partial list of ancestral category ids.
 * @return array A list full list of ancestral category ids, including the given id itself.
 */
function _theme_ucsf_get_category_roots($id, $categories = array()) {
    global $DB;

    $sql = "SELECT cc.parent, cc.name FROM {course_categories} cc WHERE cc.id = ?";
    $cats =  $DB->get_records_sql($sql, array($id));

    if (empty($cats)) {
        return $categories;
    }

    $categories[]  = $id;
    $cat = array_shift($cats);
    return  _theme_ucsf_get_category_roots($cat->parent, $categories);
}

/**
 * Retrieve a list of all course category ids,
 * since Moodle's course API does not appear to provide such a method.
 * @return array A list course ids, sorted by ID in descending order (newest first).
 */
function _theme_ucsf_get_all_category_ids() {
    global $DB;

    $sql = "SELECT cc.id FROM {course_categories} cc ORDER BY cc.id DESC";
    $categories =  array_keys($DB->get_records_sql($sql));
    return $categories;
}


