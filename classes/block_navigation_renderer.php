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
        global $COURSE, $SITE;

        $course_id = $COURSE->id;

        $current_course_rootnode = $navigation->find('currentcourse', global_navigation::TYPE_ROOTNODE);
        $my_courses_rootnode = $navigation->find('mycourses', global_navigation::TYPE_ROOTNODE);
        $courses_rootnode = $navigation->find('courses', global_navigation::TYPE_ROOTNODE);

        // ACHTUNG MINEN!
        // If the current course is the site-wide default course,
        // then we need to hide the "Current course" root node and bail early.
        // Otherwise, this whole hack goes off the rails.
        // [ST 2017/05/22]
        if ($course_id === $SITE->id && !empty($current_course_rootnode)) {
            $current_course_rootnode->display = false;
            return $navigation;
        }

        if (!empty($current_course_rootnode)) {
            $current_course_rootnode->forceopen = true;
        }

        if (!empty($my_courses_rootnode)) {
            $my_courses_rootnode->forceopen = false;
        }

        if (!empty($courses_rootnode)) {
            $courses_rootnode->forceopen = false;
        }

        $course_node = $navigation->find($course_id, global_navigation::TYPE_COURSE);

        if (!empty($course_node) && !empty($current_course_rootnode)) {
            // HACKETY HACK!
            // The Kaltura plugin adds its own node to "Current course".
            // If it's there, then re-attach the current course node above it.
            // [ST 2017/05/22]
            $kaltura_gallery_node = $current_course_rootnode->find('kalcrsgal', global_navigation::NODETYPE_LEAF);
            $next_node_key = !empty($kaltura_gallery_node) ? $kaltura_gallery_node->key : null;

            $current_course_rootnode->children->add($course_node, $next_node_key);
        }

        return $navigation;
    }
}
