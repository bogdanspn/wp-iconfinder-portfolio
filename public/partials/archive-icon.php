<?php
/**
 * The template for display portfolio post archives
 *
 * @since 3.0
 */
get_header(); ?>
<!--<?php echo basename(__FILE__); ?>-iconfinder-default-template-->
<header class="page-titles">
    <div class="container clearfix">
        <div class="page-titles-wrap">
            <h1 class="entry-title"><?php _e( 'Icons Archive' , ICF_PLUGIN_NAME ); ?></h1>
        </div>
    </div>
</header>
<?php get_template_part( 'archive-icon-body' ); ?>
<?php get_footer(); ?>
