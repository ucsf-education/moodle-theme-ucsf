@theme @theme_ucsf @theme_ucsf_banneralerts
Feature: Banner Alerts display
  In order stay informed
  As a user
  I should receive banner alert messages on pages

  Scenario: Alert levels are applied correctly
    Given the following config values are set as admin:
      | enable1alert           | 1       | theme_ucsf |
      | recurring_alert1       | 1       | theme_ucsf |
      | categories_list_alert1 | 0       | theme_ucsf |
      | alert1type             | info    | theme_ucsf |
      | alert1text             | eins    | theme_ucsf |
      | enable2alert           | 1       | theme_ucsf |
      | recurring_alert2       | 1       | theme_ucsf |
      | categories_list_alert2 | 0       | theme_ucsf |
      | alert2type             | success | theme_ucsf |
      | alert2text             | zwei    | theme_ucsf |
      | enable3alert           | 1       | theme_ucsf |
      | recurring_alert3       | 1       | theme_ucsf |
      | categories_list_alert3 | 0       | theme_ucsf |
      | alert3type             | error   | theme_ucsf |
      | alert3text             | drei    | theme_ucsf |

    Given I am on site homepage
    Then I should see the non-dismissible "eins" info banner
    And I should see the non-dismissible "zwei" announcement banner
    And I should see the non-dismissible "drei" warning banner

  Scenario: Alert is non-dismissible when logged out, but dismissible when logged in
    Given the following config values are set as admin:
      | enable1alert           | 1    | theme_ucsf |
      | recurring_alert1       | 1    | theme_ucsf |
      | categories_list_alert1 | 0    | theme_ucsf |
      | alert1type             | info | theme_ucsf |
      | alert1text             | eins | theme_ucsf |
    Given I am on site homepage
    Then I should see the non-dismissible "eins" info banner
    When I log in as "admin"
    Then I should see the dismissible "eins" info banner

  @javascript
  # A quick note on that `@javascript` tag.
  # This scenario must be run in javascript mode.
  # Otherwise, dismissing the alert wouldn't work, b/c the driver
  # used in "normal" mode doesn't support clicking on arbitrary buttons.
  Scenario: Dismiss a banner alert
    Given the following config values are set as admin:
      | enable1alert           | 1    | theme_ucsf |
      | recurring_alert1       | 1    | theme_ucsf |
      | categories_list_alert1 | 0    | theme_ucsf |
      | alert1type             | info | theme_ucsf |
      | alert1text             | eins | theme_ucsf |
    Given I log in as "admin"
    Then I should see the dismissible "eins" info banner
    When I dismiss the "eins" banner
    Then I shouldn't see the dismissible "eins" info banner
    When I reload the page
    Then I shouldn't see the dismissible "eins" info banner
