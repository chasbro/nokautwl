<?php
/**
 * The Template for displaying single product
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <?php echo NokautWL\View\Product\Page::render(); ?>
        </div>
        <!-- #content -->
    </div><!-- #primary -->

<?php
get_sidebar('content');
get_sidebar();
get_footer();
