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

        $navigation = $this->tweak_current_course_nav($navigation);

        return parent::navigation_tree($navigation, $expansionlimit, $options);
    }

    /**
     * KLUDGE!
     * Find the currently active course node and attach it to the "Current course" root node.
     * @see https://tracker.moodle.org/browse/MDL-58213?focusedCommentId=466128&page=com.atlassian.jira.plugin.system.issuetabpanels:comment-tabpanel#comment-466128
     * @todo Remove this code once the patch referenced above land in Moodle Core [ST 2017/05/18]
     * @param global_navigation $navigation
     * @return global_navigation
     */
    protected function tweak_current_course_nav(global_navigation $navigation) {
        global $PAGE;

        $current_course_rootnode = $navigation->find('currentcourse', global_navigation::TYPE_ROOTNODE);
        $current_course_rootnode->forceopen = true;

        $my_courses_rootnode = $navigation->find('mycourses', global_navigation::TYPE_ROOTNODE);
        if ($my_courses_rootnode) {
            $my_courses_rootnode->forceopen = false;
        }

        $courses_rootnode = $navigation->find('courses', global_navigation::TYPE_ROOTNODE);
        if ($courses_rootnode) {
            $courses_rootnode->forceopen = false;
        }

        $course_id = $PAGE->course->id;

        $course_node = $navigation->find($course_id, global_navigation::TYPE_COURSE);

        if ($course_node) {
            $kaltura_gallery_node = $current_course_rootnode->find('kalcrsgal', global_navigation::NODETYPE_LEAF);
            $next_node_key = !empty($kaltura_gallery_node) ? $kaltura_gallery_node->key : null;
            $current_course_rootnode->children->add($course_node, $next_node_key);
        }

        return $navigation;
    }
}
