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
     * @inheritdoc;
     */
    public function navigation_tree(global_navigation $navigation, $expansionlimit, array $options = array()) {

        $navigation->children = $this->tweak_my_courses($navigation->children);
        $navigation = $this->tweak_kaltura_placement($navigation);

        return parent::navigation_tree($navigation, $expansionlimit, $options);
    }

    /**
     * Various tweaks to the "My Courses" section of the nav block.
     * a) force the active course under "My Courses" to the top of the list.
     * b) flag the section to be hidden if no course is active.
     *
     * @param navigation_node_collection $children
     * @return navigation_node_collection
     */
    protected function tweak_my_courses(navigation_node_collection $children)
    {
        $my_courses = $children->get('mycourses');

        if (empty($my_courses)) {
            return $children;
        }

        // add a class so we can style "My courses".
        // see the "Navigation Block" section in style/custom.css
        $my_courses->classes[] = 'mycourses';

        $my_courses_children = $my_courses->children;

        $active_course = false;
        $first_course = false;

        // find the currently active course,
        // and grab the first course while at it.
        foreach ($my_courses_children as $child) {
            if (! $first_course) {
                $first_course = $child;
            }

            // yes, force-open qualifies as "active".
            if ($child->isactive || $child->forceopen) {
                $active_course = $child;
                break;
            }
        }

        // no "active" course? then hide "My courses".
        if (! $active_course) {
            $my_courses->classes[] = 'hidden';
            return $children;
        }

        // re-label "My Courses" as "Current course"
        $my_courses->text ='Current course';

        // if the "active" course is not the first course in the list, then move it there.
        if ($first_course !== $active_course)  {
            $my_courses_children->remove($active_course->key);
            $my_courses_children->add($active_course, $first_course->key);
        }

        return $children;
    }

    /**
     * KLUDGE!
     * Detach the Kaltura Media Gallery node from the obsolete "Current course" root-node and
     * Re-attach it to the current course.
     * @param global_navigation $navigation
     * @return global_navigation
     * @todo remove this once https://github.com/kaltura/moodle_plugin/issues/137 lands in prod. [ST 2017/05/15]
     */
    protected function tweak_kaltura_placement(global_navigation $navigation) {
        global $PAGE;

        $current_course_root_node = $navigation->find('currentcourse', global_navigation::TYPE_ROOTNODE);
        $kaltura_node = $current_course_root_node->get('kalcrsgal');
        $course_node = $navigation->find($PAGE->course->id, navigation_node::TYPE_COURSE);

        if (!empty($current_course_root_node) && !empty($kaltura_node) && !empty($course_node)) {
            $current_course_root_node->children->remove('kalcrsgal');
            $course_node->children->add($kaltura_node);
            $navigation->children->remove('currentcourse');
        }
        return $navigation;
    }
}
