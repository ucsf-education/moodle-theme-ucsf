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

        $navigation->children = $this->reorder_my_courses($navigation->children);

        return parent::navigation_tree($navigation, $expansionlimit, $options);
    }

    /**
     * Forces the active course under "My Courses" to the top of the list.
     *
     * @param navigation_node_collection $children
     * @return navigation_node_collection
     */
    protected function reorder_my_courses(navigation_node_collection $children)
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

        foreach ($my_courses_children as $child) {
            if (! $first_course) {
                $first_course = $child;
            }

            if ($child->isactive || $child->forceopen) {
                $active_course = $child;
                break;
            }
        }

        if (! empty($active_course) && $first_course !== $active_course)  {
            $my_courses_children->remove($active_course->key);
            $my_courses_children->add($active_course, $first_course->key);
        }
        return $children;
    }
}
