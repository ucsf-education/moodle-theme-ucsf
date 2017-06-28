<?php
/**
 * A theme for UCSF's Collaborative Learning Environment (CLE).
 *
 * @package theme_ucsf
 */
defined('MOODLE_INTERNAL') || die;

$plugin->version   = 2017062700;
$plugin->requires  = 2016112900;
$plugin->release = 'v2.0.0-rc4';
$plugin->component = 'theme_ucsf';
$plugin->dependencies = array(
    'theme_bootstrapbase'  => 2016112900,
    'theme_clean'  => 2016112900,
);
