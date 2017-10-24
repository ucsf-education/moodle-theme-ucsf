<?php

defined('MOODLE_INTERNAL') || die();

include_once ($CFG->dirroot . "/blocks/navigation/renderer.php");

/**
 * Block Navigation Renderer.
 *
 * @package theme_ucsf
 */
class theme_ucsf_block_navigation_renderer extends block_navigation_renderer {

    /**
     * @inheritdoc
     */
    public function navigation_tree(global_navigation $navigation, $expansionlimit, array $options = array()) {
        $navigation = $this->always_hide_my_courses($navigation);
        return parent::navigation_tree($navigation, $expansionlimit, $options);
    }

    /**
     * Mark the "My courses" node with a class, so it can be targeted and hidden via CSS.
     * @link https://github.com/ucsf-ckm/moodle-theme-ucsf/issues/63
     * @param global_navigation $navigation
     * @return global_navigation
     */
    protected function always_hide_my_courses(global_navigation $navigation) {
        $my_courses_rootnode = $navigation->find('mycourses', global_navigation::TYPE_ROOTNODE);
        if ($my_courses_rootnode) {
            $my_courses_rootnode->classes[] = 'mycourses';
        }
        return $navigation;
    }
}
