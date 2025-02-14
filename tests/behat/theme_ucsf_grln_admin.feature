@theme @theme_ucsf @theme_ucsf_grln_admin
Feature: Name information links are in the edit user profile admin form
  In order to update a user's name information
  As an administrator
  I need to be able to get information on how to do so in the user's profile form

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | testuser | Clem      | Chowder  |

  @javascript
  Scenario: Update name information links are present in a user's admin profile form
    Given I log in as "admin"
    And I am on site homepage
    When I click on "Site administration" "link"
    And I click on "Users" "link"
    And I click on "Browse list of users" "link"
    And I click on "Clem Chowder" "link"
    And I click on "Edit profile" "link"
    And I expand all fieldsets
    Then I should see "How to update name information." in the "id_moodle" "fieldset"
    Then I should see "How to update name information." in the "id_moodle_additional_names" "fieldset"
