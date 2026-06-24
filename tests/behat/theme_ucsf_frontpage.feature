@theme @theme_ucsf @theme_ucsf_frontpage
Feature: Frontpage
  In order to enter the CLE
  As an unauthenticated user
  I need to travail our super duper frontpage first

  Background:
    Given I am on site homepage

  Scenario: Login link is present in the primary navigation
    Then I should see "Log in" in the ".usermenu" "css_element"

  Scenario: Login link in main content area points at login page
    Then I should see "Sign in with UCSF SSO" in the "region-main" "region"
    And the "href" attribute of "Sign in with UCSF SSO" "link" should contain "/login/index.php"

  Scenario: Troubleshooting link in main content area points at an Education IT help page
    Then I should see "Trouble signing in?" in the "region-main" "region"
    And the "href" attribute of "Trouble signing in?" "link" should contain "https://tiny.ucsf.edu/educationithelp"

  @javascript @accessibility
  Scenario: Frontpage meets accessibility standards
    Then the page should meet accessibility standards with "best-practice" extra tests
