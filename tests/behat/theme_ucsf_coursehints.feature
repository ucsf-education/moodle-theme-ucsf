@theme @theme_ucsf @theme_ucsf_coursehints
Feature: Course hints
  In order to get a more complete picture about a given course
  As a moodle user
  I need to be able to see additional information

  Background:
    Given the following "users" exist:
      | username |
      | teacher1 |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Show hint for switched role
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Switch role to..." in the user menu
    And I click on "Student" "button"
    Then I should see "You are viewing this course currently with the role:" in the ".course-hint-switchedrole" "css_element"
    When I click on "Return to my normal role" "link" in the ".course-hint-switchedrole" "css_element"
    Then I should not see "You are viewing this course currently with the role:"
    And ".course-hint-switchedrole" "css_element" should not exist

  Scenario: Show hint in hidden courses
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Course visibility | Hide |
    And I click on "Save and display" "button"
    Then I should see "This course is currently hidden. Only enrolled teachers can access this course when hidden." in the ".course-hint-hidden" "css_element"
    When I am on "Course 1" course homepage
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Course visibility | Show |
    And I click on "Save and display" "button"
    Then I should not see "This course is currently hidden. Only enrolled teachers can access this course when hidden."
    And ".course-hint-hidden" "css_element" should not exist

  Scenario: Show hint guest for access
    Given the following "users" exist:
      | username |
      | student2 |
    When I log in as "teacher1"
    And I am on the "Course 1" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And I set the following fields to these values:
      | Allow guest access | Yes |
    And I press "Save changes"
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should see "You are currently viewing this course as Guest." in the ".course-hint-guestaccess" "css_element"
    And I log out
    And I log in as "teacher1"
    And I am on the "Course 1" "enrolment methods" page
    And I click on "Enable" "link" in the "Self enrolment (Student)" "table_row"
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should see "To have full access to the course, you can self enrol into this course." in the ".course-hint-guestaccess" "css_element"
    And I click on "self enrol into this course" "link" in the ".course-hint-guestaccess" "css_element"
    And I click on "Enrol me" "button"
    Then I should not see "You are currently viewing this course as Guest."
    And ".course-hint-guestaccess" "css_element" should not exist
    And I log out
    When I log in as "teacher1"
    And I am on the "Course 1" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And I set the following fields to these values:
      | Allow guest access | No |
    And I press "Save changes"
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
    Then I should not see "You are currently viewing this course as Guest."
    And ".course-hint-guestaccess" "css_element" should not exist
