<?php
/**
 * The template for display portfolio post archives
 *
 * @since 3.0
 */
get_header(); ?>

<header class="page-titles">
    <div class="container clearfix">
        <div class="page-titles-wrap">
            <h1 class="entry-title"><?php _e( 'Icon Sets Archive' , ICF_PLUGIN_NAME ); ?></h1>
        </div>
    </div>
</header>
<?php get_template_part( 'archive-iconset-body' ); ?>
<?php get_footer(); ?>
