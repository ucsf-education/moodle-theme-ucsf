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

  Scenario: Banner is non-dismissible when logged out, but dismissible when logged in
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

  Scenario Outline: Date-bound alert is visible within its scheduled time window
    # The banner is scheduled to be visible between 9:45am and 10:15am on July 1st 2026.
    Given the following config values are set as admin:
      | enable1alert           | 1          | theme_ucsf |
      | recurring_alert1       | 2          | theme_ucsf |
      | start_date1            | 2026-07-01 | theme_ucsf |
      | start_hour1            | 9          | theme_ucsf |
      | start_minute1          | 45         | theme_ucsf |
      | end_date1              | 2026-07-01 | theme_ucsf |
      | end_hour1              | 10         | theme_ucsf |
      | end_minute1            | 15         | theme_ucsf |
      | categories_list_alert1 | 0          | theme_ucsf |
      | alert1type             | info       | theme_ucsf |
      | alert1text             | eins       | theme_ucsf |
    When the time is frozen at "<date>"
    And I am on site homepage
    Then I should see the non-dismissible "eins" info banner

    Examples:
      | date             |
      | 2026-07-01 09:45 |
      | 2026-07-01 10:00 |
      | 2026-07-01 10:15 |

  Scenario Outline: Date-bound alert is not visible outside its scheduled time window
    # The banner is scheduled to be visible between 9:45am and 10:15am on July 1st 2026.
    Given the following config values are set as admin:
      | enable1alert           | 1          | theme_ucsf |
      | recurring_alert1       | 2          | theme_ucsf |
      | start_date1            | 2026-07-01 | theme_ucsf |
      | start_hour1            | 9          | theme_ucsf |
      | start_minute1          | 45         | theme_ucsf |
      | end_date1              | 2026-07-01 | theme_ucsf |
      | end_hour1              | 10         | theme_ucsf |
      | end_minute1            | 15         | theme_ucsf |
      | categories_list_alert1 | 0          | theme_ucsf |
      | alert1type             | info       | theme_ucsf |
      | alert1text             | eins       | theme_ucsf |
    When the time is frozen at "<date>"
    And I am on site homepage
    Then I shouldn't see the non-dismissible "eins" info banner

    Examples:
      | date             |
      | 2026-06-30 10:00 |
      | 2026-07-01 09:44 |
      | 2026-07-01 10:16 |
      | 2026-07-02 10:00 |

  Scenario Outline: Daily alert is visible within its scheduled time window
    # The banner is scheduled to be visible between 9:45am and 10:15am
    # every day between on July 1st and July 3rd 2026.
    Given the following config values are set as admin:
      | enable1alert           | 1          | theme_ucsf |
      | recurring_alert1       | 3          | theme_ucsf |
      | start_date_daily1      | 2026-07-01 | theme_ucsf |
      | start_hour_daily1      | 9          | theme_ucsf |
      | start_minute_daily1    | 45         | theme_ucsf |
      | end_date_daily1        | 2026-07-03 | theme_ucsf |
      | end_hour_daily1        | 10         | theme_ucsf |
      | end_minute_daily1      | 15         | theme_ucsf |
      | categories_list_alert1 | 0          | theme_ucsf |
      | alert1type             | info       | theme_ucsf |
      | alert1text             | eins       | theme_ucsf |
    When the time is frozen at "<date>"
    And I am on site homepage
    Then I should see the non-dismissible "eins" info banner

    Examples:
      | date             |
      | 2026-07-01 09:45 |
      | 2026-07-01 10:00 |
      | 2026-07-01 10:15 |
      | 2026-07-02 09:45 |
      | 2026-07-02 10:00 |
      | 2026-07-02 10:15 |
      | 2026-07-03 09:45 |
      | 2026-07-03 10:00 |
      | 2026-07-03 10:15 |

  Scenario Outline: Daily alert is not visible outside its scheduled time window
    # The banner is scheduled to be visible between 9:45am and 10:15am on July 1st 2026.
    Given the following config values are set as admin:
      | enable1alert           | 1          | theme_ucsf |
      | recurring_alert1       | 2          | theme_ucsf |
      | start_date1            | 2026-07-01 | theme_ucsf |
      | start_hour1            | 9          | theme_ucsf |
      | start_minute1          | 45         | theme_ucsf |
      | end_date1              | 2026-07-01 | theme_ucsf |
      | end_hour1              | 10         | theme_ucsf |
      | end_minute1            | 15         | theme_ucsf |
      | categories_list_alert1 | 0          | theme_ucsf |
      | alert1type             | info       | theme_ucsf |
      | alert1text             | eins       | theme_ucsf |
    When the time is frozen at "<date>"
    And I am on site homepage
    Then I shouldn't see the non-dismissible "eins" info banner

    Examples:
      | date             |
      | 2026-06-30 10:00 |
      | 2026-07-01 09:44 |
      | 2026-07-01 10:16 |
      | 2026-07-02 09:44 |
      | 2026-07-02 10:16 |
      | 2026-07-03 09:44 |
      | 2026-07-03 10:16 |
      | 2026-07-04 10:00 |

  Scenario Outline: Weekly alert is visible within its scheduled time window
    # The banner is scheduled to be visible between 9:45am and 10:15am
    # every Tuesday between on July 1st and July 10th 2026.
    # Those Tuesdays are July 2nd and 9th.
    Given the following config values are set as admin:
      | enable1alert           | 1          | theme_ucsf |
      | recurring_alert1       | 4          | theme_ucsf |
      | start_date_weekly1     | 2026-07-01 | theme_ucsf |
      | start_hour_weekly1     | 9          | theme_ucsf |
      | start_minute_weekly1   | 45         | theme_ucsf |
      | end_date_weekly1       | 2026-07-10 | theme_ucsf |
      | end_hour_weekly1       | 10         | theme_ucsf |
      | end_minute_weekly1     | 15         | theme_ucsf |
      | show_week_day1         | 4          | theme_ucsf |
      | categories_list_alert1 | 0          | theme_ucsf |
      | alert1type             | info       | theme_ucsf |
      | alert1text             | eins       | theme_ucsf |
    When the time is frozen at "<date>"
    And I am on site homepage
    Then I should see the non-dismissible "eins" info banner

    Examples:
      | date             |
      | 2026-07-02 09:45 |
      | 2026-07-02 10:00 |
      | 2026-07-02 10:15 |
      | 2026-07-09 09:45 |
      | 2026-07-09 10:00 |
      | 2026-07-09 10:15 |

  Scenario Outline: Weekly alert is not visible outside its scheduled time window
    # The banner is scheduled to be visible between 9:45am and 10:15am
    # every Tuesday between on July 1st and July 10th 2026.
    # Those Tuesdays are July 2nd and 9th.
    Given the following config values are set as admin:
      | enable1alert           | 1          | theme_ucsf |
      | recurring_alert1       | 4          | theme_ucsf |
      | start_date_weekly1     | 2026-07-01 | theme_ucsf |
      | start_hour_weekly1     | 9          | theme_ucsf |
      | start_minute_weekly1   | 45         | theme_ucsf |
      | end_date_weekly1       | 2026-07-10 | theme_ucsf |
      | end_hour_weekly1       | 10         | theme_ucsf |
      | end_minute_weekly1     | 15         | theme_ucsf |
      | show_week_day1         | 4          | theme_ucsf |
      | categories_list_alert1 | 0          | theme_ucsf |
      | alert1type             | info       | theme_ucsf |
      | alert1text             | eins       | theme_ucsf |
    When the time is frozen at "<date>"
    And I am on site homepage
    Then I shouldn't see the non-dismissible "eins" info banner

    Examples:
      | date             |
      | 2026-06-25 10:00 |
      | 2026-07-01 10:00 |
      | 2026-07-02 09:44 |
      | 2026-07-02 10:16 |
      | 2026-07-03 10:00 |
      | 2026-07-04 10:00 |
      | 2026-07-05 10:00 |
      | 2026-07-06 10:00 |
      | 2026-07-07 10:00 |
      | 2026-07-08 10:00 |
      | 2026-07-09 09:44 |
      | 2026-07-09 10:16 |
      | 2026-07-10 10:00 |
      | 2026-07-11 10:00 |
      | 2026-07-17 10:00 |

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
    # Reload the page, make sure the alert doesn't resurface.
    When I reload the page
    Then I shouldn't see the dismissible "eins" info banner
