<?php

/**
 * The one column layout.
 *
 * @package theme_ucsf
 */

// Get the HTML for the settings bits.
$html = theme_ucsf_get_html_for_settings($OUTPUT, $PAGE);

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google fonts -->
    <link href="//fonts.googleapis.com/css?family=Open Sans:400,600,700,Bold,italic" rel="stylesheet" type="text/css"/>
    <!-- Awesome fonts -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header role="banner" class="navbar navbar-fixed-top<?php echo $html->navbarclass ?> moodle-has-zindex">
    <nav role="navigation" class="navbar-inner">
        <div class="container-fluid">
            <?php echo $html->navbar_home; ?>
            <?php echo $OUTPUT->navbar_button(); ?>
            <?php echo $OUTPUT->user_menu(); ?>
            <?php echo $html->help_menu; ?>
            <?php echo $OUTPUT->navbar_plugin_output(); ?>
            <?php echo $OUTPUT->search_box(); ?>
            <div class="nav-collapse collapse">
                <?php echo $html->custom_menu; ?>
                <ul class="nav pull-right">
                    <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<header role="banner" class="navbar ucsf-custom-menu">
    <nav role="navigation">
        <div class="container-fluid">
            <?php echo $html->category_label; ?>
            <?php echo $html->custom_menu; ?>
        </div>
    </nav>
</header>

<div id="page" class="container-fluid">

    <?php echo $html->custom_alerts; ?>
    <?php echo $OUTPUT->full_header(); ?>

    <div id="page-content" class="row-fluid">
        <section id="region-main" class="span12">
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </section>
    </div>
</div>

<div class="main-footer container-fluid">
    <footer id="page-footer">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <div class="ucsf_footer_text">
            <?php echo $html->copyright; ?>
        </div>
        <div class="ucsf_footer_links_container">
            <?php echo $html->footnote; ?>
        </div>
        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->standard_footer_html();
        ?>
    </footer>
    <?php echo $OUTPUT->standard_end_of_body_html() ?>
</div>

</body>
</html>
