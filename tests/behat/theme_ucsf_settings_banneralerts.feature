@theme @theme_ucsf @theme_ucsf_settings_banneralerts
Feature: Banner Alerts settings form
  In order to put notifications onto pages
  As an administrator
  I need to be able to configure banner alerts in the theme settings

  Background:
    When I log in as "admin"
    And I navigate to "Appearance > Themes" in site administration

  Scenario: Settings form renders without data
    When I click on "Edit theme settings 'UCSF'" "link"
    And I click on "Banner Alerts" "link"
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert            | 0              |
      | id_s_theme_ucsf_recurring_alert1        | Never end      |
      | id_s_theme_ucsf_categories_list_alert1  | Dashboard Only |
      | id_s_theme_ucsf_alert1type              | Information    |
      | id_s_theme_ucsf_alert1text              |                |
      | id_s_theme_ucsf_enable2alert            | 0              |
      | id_s_theme_ucsf_recurring_alert2        | Never end      |
      | id_s_theme_ucsf_categories_list_alert2  | Dashboard Only |
      | id_s_theme_ucsf_alert2type              | Information    |
      | id_s_theme_ucsf_alert2text              |                |
      | id_s_theme_ucsf_enable3alert            | 0              |
      | id_s_theme_ucsf_recurring_alert3        | Never end      |
      | id_s_theme_ucsf_categories_list_alert3  | Dashboard Only |
      | id_s_theme_ucsf_alert3type              | Information    |
      | id_s_theme_ucsf_alert3text              |                |
      | id_s_theme_ucsf_enable4alert            | 0              |
      | id_s_theme_ucsf_recurring_alert4        | Never end      |
      | id_s_theme_ucsf_categories_list_alert4  | Dashboard Only |
      | id_s_theme_ucsf_alert4type              | Information    |
      | id_s_theme_ucsf_alert4text              |                |
      | id_s_theme_ucsf_enable5alert            | 0              |
      | id_s_theme_ucsf_recurring_alert5        | Never end      |
      | id_s_theme_ucsf_categories_list_alert5  | Dashboard Only |
      | id_s_theme_ucsf_alert5type              | Information    |
      | id_s_theme_ucsf_alert5text              |                |
      | id_s_theme_ucsf_enable6alert            | 0              |
      | id_s_theme_ucsf_recurring_alert6        | Never end      |
      | id_s_theme_ucsf_categories_list_alert6  | Dashboard Only |
      | id_s_theme_ucsf_alert6type              | Information    |
      | id_s_theme_ucsf_alert6text              |                |
      | id_s_theme_ucsf_enable7alert            | 0              |
      | id_s_theme_ucsf_recurring_alert7        | Never end      |
      | id_s_theme_ucsf_categories_list_alert7  | Dashboard Only |
      | id_s_theme_ucsf_alert7type              | Information    |
      | id_s_theme_ucsf_alert7text              |                |
      | id_s_theme_ucsf_enable8alert            | 0              |
      | id_s_theme_ucsf_recurring_alert8        | Never end      |
      | id_s_theme_ucsf_categories_list_alert8  | Dashboard Only |
      | id_s_theme_ucsf_alert8type              | Information    |
      | id_s_theme_ucsf_alert8text              |                |
      | id_s_theme_ucsf_enable9alert            | 0              |
      | id_s_theme_ucsf_recurring_alert9        | Never end      |
      | id_s_theme_ucsf_categories_list_alert9  | Dashboard Only |
      | id_s_theme_ucsf_alert9type              | Information    |
      | id_s_theme_ucsf_alert10text             |                |
      | id_s_theme_ucsf_enable10alert           | 0              |
      | id_s_theme_ucsf_recurring_alert10       | Never end      |
      | id_s_theme_ucsf_categories_list_alert10 | Dashboard Only |
      | id_s_theme_ucsf_alert10type             | Information    |
      | id_s_theme_ucsf_alert10text             |                |

  Scenario: Settings form renders with data
    # Let's populate the test db with a set of alert fixtures that
    # are representative of all configuration options available.
    #
    # Running through all possible combinations of these options is not feasible,
    # but every option is at least represented once in this fixtures set.
    # So this will (have to) do.

    # Alert 1 is a never-ending, inactive alert showing on the dashboard only.
    Given the following config values are set as admin:
      | enable1alert           | 0         | theme_ucsf |
      | recurring_alert1       | 1         | theme_ucsf |
      | categories_list_alert1 | dashboard | theme_ucsf |
      | alert1type             | error     | theme_ucsf |
      | alert1text             | bazbork   | theme_ucsf |

    # Alert 2 is a one-time informational, active alert targeting Category 1
    # that's set to be active between 9:15am on June 1st 2026 and 11:45pm on July 1st 2026.
    And the following config values are set as admin:
      | enable2alert           | 1          | theme_ucsf |
      | recurring_alert2       | 2          | theme_ucsf |
      | start_date2            | 2026-06-01 | theme_ucsf |
      | start_hour2            | 9          | theme_ucsf |
      | start_minute2          | 15         | theme_ucsf |
      | end_date2              | 2026-07-01 | theme_ucsf |
      | end_hour2              | 23         | theme_ucsf |
      | end_minute2            | 45         | theme_ucsf |
      | categories_list_alert2 | 1          | theme_ucsf |
      | alert2type             | info       | theme_ucsf |
      | alert2text             | foobar     | theme_ucsf |

    # Alert 3 is a site-wide, active announcement alert that is scheduled to
    # show every day between 8:05am and 10:30pm between August 3rd 2024 and September 12th 2025.
    And the following config values are set as admin:
      | enable3alert           | 1          | theme_ucsf |
      | recurring_alert3       | 3          | theme_ucsf |
      | start_date_daily3      | 2024-08-03 | theme_ucsf |
      | start_hour_daily3      | 8          | theme_ucsf |
      | start_minute_daily3    | 5          | theme_ucsf |
      | end_date_daily3        | 2026-09-12 | theme_ucsf |
      | end_hour_daily3        | 20         | theme_ucsf |
      | end_minute_daily3      | 30         | theme_ucsf |
      | categories_list_alert3 | 0          | theme_ucsf |
      | alert3type             | success    | theme_ucsf |
      | alert3text             | lorem      | theme_ucsf |

    # Alert 4 is a dashboard-only, inactive informational alert this scheduled to
    # show every Tuesday between 4:35pm and 5:20pm, between June 29th 2024 and September 30th 2026
    And the following config values are set as admin:
      | enable4alert           | 0           | theme_ucsf |
      | recurring_alert4       | 4           | theme_ucsf |
      | start_date_weekly4     | 2024-06-29  | theme_ucsf |
      | start_hour_weekly4     | 16          | theme_ucsf |
      | start_minute_weekly4   | 35          | theme_ucsf |
      | end_date_weekly4       | 2026-09-30  | theme_ucsf |
      | end_hour_weekly4       | 17          | theme_ucsf |
      | end_minute_weekly4     | 20          | theme_ucsf |
      | show_week_day4         | 2           | theme_ucsf |
      | categories_list_alert4 | dashboard   | theme_ucsf |
      | alert4type             | info        | theme_ucsf |
      | alert4text             | snack break | theme_ucsf |

    # Navigate to the banner alerts form in the theme settings.
    # This has to happen AFTER we populate the test db.
    When I click on "Edit theme settings 'UCSF'" "link"
    And I click on "Banner Alerts" "link"

    # Check Alert 1 settings
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert           | 0              |
      | id_s_theme_ucsf_recurring_alert1       | Never end      |
      | id_s_theme_ucsf_categories_list_alert1 | Dashboard Only |
      | id_s_theme_ucsf_alert1type             | Warning        |
      | id_s_theme_ucsf_alert1text             | bazbork        |
    And I should not see "id_s_theme_ucsf_datebound_datepicker1_start_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker1_start_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker1_start_minute"
    And I should not see "id_s_theme_ucsf_datebound_datepicker1_end_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker1_end_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker1_end_minute"
    And I should not see "id_s_theme_ucsf_daily_datepicker1_start_date"
    And I should not see "id_s_theme_ucsf_daily_timepicker1_start_hour"
    And I should not see "id_s_theme_ucsf_daily_timepicker1_start_minute"
    And I should not see "id_s_theme_ucsf_daily_datepicker1_end_date"
    And I should not see "id_s_theme_ucsf_daily_timepicker1_end_hour"
    And I should not see "id_s_theme_ucsf_daily_timepicker1_end_minute"
    And I should not see "id_s_theme_ucsf_weekly_datepicker1_start_date"
    And I should not see "id_s_theme_ucsf_weekly_timepicker1_start_hour"
    And I should not see "id_s_theme_ucsf_weekly_timepicker1_start_minute"
    And I should not see "id_s_theme_ucsf_weekly_datepicker1_end_date"
    And I should not see "id_s_theme_ucsf_weekly_timepicker1_end_hour"
    And I should not see "id_s_theme_ucsf_weekly_timepicker1_end_minute"
    And I should not see "id_s_theme_ucsf_show_week_day1"

    # Check Alert 2 settings
    And the following fields match these values:
      | id_s_theme_ucsf_enable2alert                       | 1           |
      | id_s_theme_ucsf_recurring_alert2                   | One time    |
      | id_s_theme_ucsf_datebound_datepicker2_start_date   | 2026-06-01  |
      | id_s_theme_ucsf_datebound_datepicker2_start_hour   | 9           |
      | id_s_theme_ucsf_datebound_datepicker2_start_minute | 15          |
      | id_s_theme_ucsf_datebound_datepicker2_end_date     | 2026-07-01  |
      | id_s_theme_ucsf_datebound_datepicker2_end_hour     | 23          |
      | id_s_theme_ucsf_datebound_datepicker2_end_minute   | 45          |
      | id_s_theme_ucsf_categories_list_alert2             | Category 1  |
      | id_s_theme_ucsf_alert2type                         | Information |
      | id_s_theme_ucsf_alert2text                         | foobar      |
    And I should not see "id_s_theme_ucsf_daily_datepicker2_start_date"
    And I should not see "id_s_theme_ucsf_daily_timepicker2_start_hour"
    And I should not see "id_s_theme_ucsf_daily_timepicker2_start_minute"
    And I should not see "id_s_theme_ucsf_daily_datepicker2_end_date"
    And I should not see "id_s_theme_ucsf_daily_timepicker2_end_hour"
    And I should not see "id_s_theme_ucsf_daily_timepicker2_end_minute"
    And I should not see "id_s_theme_ucsf_weekly_datepicker2_start_date"
    And I should not see "id_s_theme_ucsf_weekly_timepicker2_start_hour"
    And I should not see "id_s_theme_ucsf_weekly_timepicker2_start_minute"
    And I should not see "id_s_theme_ucsf_weekly_datepicker2_end_date"
    And I should not see "id_s_theme_ucsf_weekly_timepicker2_end_hour"
    And I should not see "id_s_theme_ucsf_weekly_timepicker2_end_minute"
    And I should not see "id_s_theme_ucsf_show_week_day2"

    # Check Alert 3 settings
    And the following fields match these values:
      | id_s_theme_ucsf_enable3alert                   | 1            |
      | id_s_theme_ucsf_recurring_alert3               | Daily        |
      | id_s_theme_ucsf_daily_datepicker3_start_date   | 2024-08-03   |
      | id_s_theme_ucsf_daily_timepicker3_start_hour   | 8            |
      | id_s_theme_ucsf_daily_timepicker3_start_minute | 5            |
      | id_s_theme_ucsf_daily_datepicker3_end_date     | 2026-09-12   |
      | id_s_theme_ucsf_daily_timepicker3_end_hour     | 20           |
      | id_s_theme_ucsf_daily_timepicker3_end_minute   | 30           |
      | id_s_theme_ucsf_categories_list_alert3         | Site-wide    |
      | id_s_theme_ucsf_alert3type                     | Announcement |
      | id_s_theme_ucsf_alert3text                     | lorem        |
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_start_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_start_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_start_minute"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_end_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_end_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_end_minute"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_start_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_start_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_start_minute"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_end_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_end_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker3_end_minute"
    And I should not see "id_s_theme_ucsf_weekly_datepicker3_start_date"
    And I should not see "id_s_theme_ucsf_weekly_timepicker3_start_hour"
    And I should not see "id_s_theme_ucsf_weekly_timepicker3_start_minute"
    And I should not see "id_s_theme_ucsf_weekly_datepicker3_end_date"
    And I should not see "id_s_theme_ucsf_weekly_timepicker3_end_hour"
    And I should not see "id_s_theme_ucsf_weekly_timepicker3_end_minute"
    And I should not see "id_s_theme_ucsf_show_week_day3"

    # Check Alert 4 settings
    And the following fields match these values:
      | id_s_theme_ucsf_enable4alert                    | 0              |
      | id_s_theme_ucsf_recurring_alert4                | Weekly         |
      | id_s_theme_ucsf_weekly_datepicker4_start_date   | 2024-06-29     |
      | id_s_theme_ucsf_weekly_timepicker4_start_hour   | 16             |
      | id_s_theme_ucsf_weekly_timepicker4_start_minute | 35             |
      | id_s_theme_ucsf_weekly_datepicker4_end_date     | 2026-09-30     |
      | id_s_theme_ucsf_weekly_timepicker4_end_hour     | 17             |
      | id_s_theme_ucsf_weekly_timepicker4_end_minute   | 20             |
      | id_s_theme_ucsf_show_week_day4                  | Tuesday        |
      | id_s_theme_ucsf_categories_list_alert4          | Dashboard Only |
      | id_s_theme_ucsf_alert4type                      | Information    |
      | id_s_theme_ucsf_alert4text                      | snack break    |
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_start_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_start_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_start_minute"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_end_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_end_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_end_minute"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_start_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_start_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_start_minute"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_end_date"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_end_hour"
    And I should not see "id_s_theme_ucsf_datebound_datepicker4_end_minute"
    And I should not see "id_s_theme_ucsf_daily_datepicker4_start_date"
    And I should not see "id_s_theme_ucsf_daily_timepicker4_start_hour"
    And I should not see "id_s_theme_ucsf_daily_timepicker4_start_minute"
    And I should not see "id_s_theme_ucsf_daily_datepicker4_end_date"
    And I should not see "id_s_theme_ucsf_daily_timepicker4_end_hour"
    And I should not see "id_s_theme_ucsf_daily_timepicker4_end_minute"

  # ACHTUNG MINEN!
  # A note on programmatically setting values on date input fields,
  # such as the various start- and end-date inputs, in the scenarios below.
  #
  # If you run these tests in the browser, by slapping the `@javascript` tag
  # onto a scenario, then you may run into unexpected date shifting behaviour.
  # The problem appears to be a UTC timezone conversion on the input date when the date is set.
  # "The date may be a day off due to your local timezone."
  # source: https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/valueAsDate
  #
  # I cannot reproduce this by click-testing the form, nor by running the scenario
  # without the `@javascript` tag.
  # So keep the `@javascript` tag off of those scenarios.
  # [ST 2026/07/15]
  Scenario: Configure an unbound alert
    When I click on "Edit theme settings 'UCSF'" "link"
    And I click on "Banner Alerts" "link"
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert           | 0              |
      | id_s_theme_ucsf_recurring_alert1       | Never end      |
      | id_s_theme_ucsf_categories_list_alert1 | Dashboard Only |
      | id_s_theme_ucsf_alert1type             | Information    |
      | id_s_theme_ucsf_alert1text             |                |
    When I set the field "id_s_theme_ucsf_enable1alert" to "Never end"
    And I set the field "id_s_theme_ucsf_categories_list_alert1" to "Site-wide"
    And I set the field "id_s_theme_ucsf_alert1type" to "Information"
    And I set the field "id_s_theme_ucsf_alert1text" to "something"
    And I press "Save changes"
    And I click on "Banner Alerts" "link"
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert           | 1           |
      | id_s_theme_ucsf_recurring_alert1       | Never end   |
      | id_s_theme_ucsf_categories_list_alert1 | Site-wide   |
      | id_s_theme_ucsf_alert1type             | Information |
      | id_s_theme_ucsf_alert1text             | something   |

  Scenario: Configure a date-bound alert
    When I click on "Edit theme settings 'UCSF'" "link"
    And I click on "Banner Alerts" "link"
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert           | 0              |
      | id_s_theme_ucsf_recurring_alert1       | Never end      |
      | id_s_theme_ucsf_categories_list_alert1 | Dashboard Only |
      | id_s_theme_ucsf_alert1type             | Information    |
      | id_s_theme_ucsf_alert1text             |                |
    And I set the field "id_s_theme_ucsf_recurring_alert1" to "One time"
    And I set the field "id_s_theme_ucsf_categories_list_alert1" to "Dashboard Only"
    And I set the field "id_s_theme_ucsf_alert1type" to "Announcement"
    And I set the field "id_s_theme_ucsf_alert1text" to "go away"
    # We have to save the form here in order to bring up the date/time input fields.
    And I press "Save changes"
    And I click on "Banner Alerts" "link"
    And I set the field "id_s_theme_ucsf_datebound_datepicker1_start_date" to "2024-08-02"
    And I set the field "id_s_theme_ucsf_datebound_datepicker1_start_hour" to "15"
    And I set the field "id_s_theme_ucsf_datebound_datepicker1_start_minute" to "10"
    And I set the field "id_s_theme_ucsf_datebound_datepicker1_end_date" to "2026-09-23"
    And I set the field "id_s_theme_ucsf_datebound_datepicker1_end_hour" to "20"
    And I set the field "id_s_theme_ucsf_datebound_datepicker1_end_minute" to "30"
    And I press "Save changes"
    And I click on "Banner Alerts" "link"
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert                       | 0              |
      | id_s_theme_ucsf_recurring_alert1                   | One time       |
      | id_s_theme_ucsf_datebound_datepicker1_start_date   | 2024-08-02     |
      | id_s_theme_ucsf_datebound_datepicker1_start_hour   | 15             |
      | id_s_theme_ucsf_datebound_datepicker1_start_minute | 10             |
      | id_s_theme_ucsf_datebound_datepicker1_end_date     | 2026-09-23     |
      | id_s_theme_ucsf_datebound_datepicker1_end_hour     | 20             |
      | id_s_theme_ucsf_datebound_datepicker1_end_minute   | 30             |
      | id_s_theme_ucsf_categories_list_alert1             | Dashboard Only |
      | id_s_theme_ucsf_alert1type                         | Announcement   |
      | id_s_theme_ucsf_alert1text                         | go away        |

  Scenario: Configure a daily recurring alert
    When I click on "Edit theme settings 'UCSF'" "link"
    And I click on "Banner Alerts" "link"
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert           | 0              |
      | id_s_theme_ucsf_recurring_alert1       | Never end      |
      | id_s_theme_ucsf_categories_list_alert1 | Dashboard Only |
      | id_s_theme_ucsf_alert1type             | Information    |
      | id_s_theme_ucsf_alert1text             |                |
    And I set the field "id_s_theme_ucsf_recurring_alert1" to "Daily"
    And I set the field "id_s_theme_ucsf_categories_list_alert1" to "Category 1"
    And I set the field "id_s_theme_ucsf_alert1type" to "Warning"
    And I set the field "id_s_theme_ucsf_alert1text" to "flimflam"
    And I press "Save changes"
    And I click on "Banner Alerts" "link"
    And I set the field "id_s_theme_ucsf_daily_datepicker1_start_date" to "2024-08-02"
    And I set the field "id_s_theme_ucsf_daily_timepicker1_start_hour" to "15"
    And I set the field "id_s_theme_ucsf_daily_timepicker1_start_minute" to "10"
    And I set the field "id_s_theme_ucsf_daily_datepicker1_end_date" to "2026-09-23"
    And I set the field "id_s_theme_ucsf_daily_timepicker1_end_hour" to "20"
    And I set the field "id_s_theme_ucsf_daily_timepicker1_end_minute" to "30"
    And I press "Save changes"
    And I click on "Banner Alerts" "link"
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert                   | 0          |
      | id_s_theme_ucsf_recurring_alert1               | Daily      |
      | id_s_theme_ucsf_daily_datepicker1_start_date   | 2024-08-02 |
      | id_s_theme_ucsf_daily_timepicker1_start_hour   | 15         |
      | id_s_theme_ucsf_daily_timepicker1_start_minute | 10         |
      | id_s_theme_ucsf_daily_datepicker1_end_date     | 2026-09-23 |
      | id_s_theme_ucsf_daily_timepicker1_end_hour     | 20         |
      | id_s_theme_ucsf_daily_timepicker1_end_minute   | 30         |
      | id_s_theme_ucsf_categories_list_alert1         | Category 1 |
      | id_s_theme_ucsf_alert1type                     | Warning    |
      | id_s_theme_ucsf_alert1text                     | flimflam   |

  Scenario: Configure a weekly recurring alert
    When I click on "Edit theme settings 'UCSF'" "link"
    And I click on "Banner Alerts" "link"
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert           | 0              |
      | id_s_theme_ucsf_recurring_alert1       | Never end      |
      | id_s_theme_ucsf_categories_list_alert1 | Dashboard Only |
      | id_s_theme_ucsf_alert1type             | Information    |
      | id_s_theme_ucsf_alert1text             |                |
    And I set the field "id_s_theme_ucsf_recurring_alert1" to "Weekly"
    And I set the field "id_s_theme_ucsf_categories_list_alert1" to "Category 1"
    And I set the field "id_s_theme_ucsf_alert1type" to "Warning"
    And I set the field "id_s_theme_ucsf_alert1text" to "wurstwasser"
    And I press "Save changes"
    And I click on "Banner Alerts" "link"
    And I set the field "id_s_theme_ucsf_weekly_datepicker1_start_date" to "2024-08-02"
    And I set the field "id_s_theme_ucsf_weekly_timepicker1_start_hour" to "15"
    And I set the field "id_s_theme_ucsf_weekly_timepicker1_start_minute" to "10"
    And I set the field "id_s_theme_ucsf_weekly_datepicker1_end_date" to "2026-09-23"
    And I set the field "id_s_theme_ucsf_weekly_timepicker1_end_hour" to "20"
    And I set the field "id_s_theme_ucsf_weekly_timepicker1_end_minute" to "30"
    And I set the field "id_s_theme_ucsf_show_week_day1" to "Wednesday"
    And I press "Save changes"
    And I click on "Banner Alerts" "link"
    Then the following fields match these values:
      | id_s_theme_ucsf_enable1alert                    | 0           |
      | id_s_theme_ucsf_recurring_alert1                | Weekly      |
      | id_s_theme_ucsf_weekly_datepicker1_start_date   | 2024-08-02  |
      | id_s_theme_ucsf_weekly_timepicker1_start_hour   | 15          |
      | id_s_theme_ucsf_weekly_timepicker1_start_minute | 10          |
      | id_s_theme_ucsf_weekly_datepicker1_end_date     | 2026-09-23  |
      | id_s_theme_ucsf_weekly_timepicker1_end_hour     | 20          |
      | id_s_theme_ucsf_weekly_timepicker1_end_minute   | 30          |
      | id_s_theme_ucsf_show_week_day1                  | Wednesday   |
      | id_s_theme_ucsf_categories_list_alert1          | Category 1  |
      | id_s_theme_ucsf_alert1type                      | Warning     |
      | id_s_theme_ucsf_alert1text                      | wurstwasser |
