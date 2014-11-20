<?php
/**
 * The Template for displaying products
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <?php echo NokautWL\View\Products\Page::render(); ?>
        </div>
        <!-- #content -->
    </div><!-- #primary -->

<?php
get_sidebar();
get_footer();
