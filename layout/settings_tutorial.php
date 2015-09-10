<?php
/*
 * This file is part of the lambda_liquid theme package.
 *
 * Copyright (c) 2015 Lambda Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * @license MIT
 *
 */


global $CFG, $OUTPUT;

ob_start();

require_once(dirname(dirname(__FILE__)).'/settings/help_variables.php');
require_once(dirname(dirname(__FILE__)).'/../../config.php');



$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Settings tutorial");
$PAGE->set_heading("Settings tutorial");
$PAGE->set_url($CFG->wwwroot.'/blank_page.php');

echo $OUTPUT->header();
    
?>
<h3>
<?php if(isloggedin()) : ?>
    
<!-- Help for $recurring_alert variable --------------------------------------------------------->

    <?php if (isset($_GET['recurring_alert'])): ?>
        <h1>Recurring alerts tutorial</h1>
        <div class="recurring_alerts_document">
            <h2>Never end alerts</h2>
            <p>When this value is set the alerts will show until you disable it.</p>
        </div><!-- end recurring_alerts_document -->
        <div class="recurring_alerts_document"> <div class="recurring_alerts_document">
            <h2>One time alerts</h2>
            <p>Set the start date and time when you want your alert to start showing and set your end date and time when you want your alert to stop showing.</p>
        </div><!-- end recurring_alerts_document -->
        <div class="recurring_alerts_document">
            <h2>Daily alerts</h2>
            <p>Set the start date when alert starts and set the end date when alert stops. After that, set start hour and minute when you want alert to show every day and set end hour and minute when you want your alert to stop showing. Daily alert will show between this two dates, every day, between start hour and minute and end hour and minute.</p>
        </div><!-- end recurring_alerts_document -->
        <div class="recurring_alerts_document">
            <h2>Weekly alerts</h2>
            <p>Set the start date when alert starts and set the end date when alert stops. After that, set weekday you want alert to show. Weekly alert will show between this two dates, on that specific day that is selected.</p>
        </div><!-- end recurring_alerts_document -->
        
    <?php endif;?>
        
<!-- End of help for $recurring_alert variable --------------------------------------------------------->
<!-- Help for $categories_list_alert variable --------------------------------------------------------->
    <?php if (isset($_GET['categories_list_alert'])): ?>
        <h1>Category list tutorial</h1>
        <p>Alert will show on selected date only on selected category or subcategory. If you don't select category, the alert will be show throughout entire site. </p>
    <?php endif; ?>
<!-- End of help for $recurring_alert variable --------------------------------------------------------->
        
<?php endif;?>
<?php
    echo $OUTPUT->footer();
?>