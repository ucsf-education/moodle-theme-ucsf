<?php

/**
 * UCSF theme core renderer.
 *
 * @package theme_ucsf
 */

require_once($CFG->dirroot . '/theme/clean/classes/core_renderer.php');
require_once($CFG->dirroot . '/theme/ucsf/lib.php');

class theme_ucsf_core_renderer extends theme_clean_core_renderer
{
    /**
     * Returns a help menu.
     *
     * @param array $items An associative array containing the help menu items.
     * @param string $title The menu title.
     * @return string The help menu HTML, or a blank string if the given menu data is empty.
     */
    public function help_menu($items = array(), $title = '')
    {
        if (empty($items)) {
            return '';
        }

        $context = new stdClass();
        $context->items = $items;
        $context->title = $title;

        return $this->render_from_template('theme_ucsf/helpmenu_popover', $context);
    }

    /**
     * Returns the custom alerts.
     *
     * @param string $callback_url
     * @param array $alerts
     * @return string The custom alerts HTML, or a blank string if no alerts were given.
     */
    public function custom_alerts($callback_url, $alerts = array()) {

        if (empty($alerts)) {
            return '';
        }

        $context = new stdClass();
        $context->alerts = $alerts;
        $context->url = $callback_url;

        return $this->render_from_template('theme_ucsf/custom_alerts', $context);
    }


    /**
     * Renders a given category label.
     *
     * @param string $label_text The label text.
     * @param string $label_link Optional link for the given label.
     * @return string The rendered markup.
     */
    public function category_label($label_text, $label_link = '') {

        $out = $label_text;

        if (!empty($label_link)) {

            $out = html_writer::link($label_link, $out);
        }

        return html_writer::div($out, 'category-label pull-left');
    }

    /**
     * Renders the given logo, to be included in the primary header.
     *
     * @param string $logo_image_url
     * @param string $logo_image_alt
     * @param string $logo_link_url
     * @param string $logo_link_title
     * @param string $logo_link_target
     * @return string
     */
    public function navbar_home_logo(
        $logo_image_url,
        $logo_image_alt = '',
        $logo_link_url = '',
        $logo_link_title = '',
        $logo_link_target = '_self'
    ) {
        $out = html_writer::img($logo_image_url, $logo_image_alt, array('class' => 'small-logo'));
        if (! empty($logo_link_url)) {
            $out = html_writer::link(
                $logo_link_url,
                $out,
                array('class' => 'small-logo-container', 'target' => $logo_link_target, 'title' => $logo_link_title)
            );
        }

        return $out;
    }

    /**
     * Renders the given title, to be rendered in the primary header.
     *
     * @param string $title
     * @return string
     */
    public function navbar_home_title($title)
    {
        return html_writer::span($title, 'brand');
    }

    /**
     * @inheritdoc
     */
    public function heading( $text, $level = 2, $classes = null, $id = null ) {
        global $SITE;
        // KLUDGE!
        // if the given heading text is the same as the site's title,
        // and  this is level 1 heading, then print an empty heading.
        // @see https://github.com/ucsf-ckm/moodle-theme-ucsf/issues/75
        // [ST 2017/06/20]
        if ($level === 1 && $SITE->fullname === $text) {
            $text = '';
        }
        return parent::heading( $text, $level, $classes, $id );
    }
}

