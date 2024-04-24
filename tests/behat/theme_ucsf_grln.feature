@theme @theme_ucsf @theme_ucsf_grln
Feature: Name information links are on the edit my profile form
  In order to update my name information
  As a moodle user
  I need to be able to get information on how to do so in my user profile form

  Background:
    Given the following "users" exist:
      | username |
      | testuser |

  @javascript
  Scenario: Update name information links are present in my profile form
    When I log in as "testuser"
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link"
    And I expand all fieldsets
    Then I should see "How to update name information." in the "id_moodle" "fieldset"
    Then I should see "How to update name information." in the "id_moodle_additional_names" "fieldset"
