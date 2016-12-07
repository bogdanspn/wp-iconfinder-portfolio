<?php
/**
 * Icon search results.
 */
?>
<?php echo "<!-- Default template: " . basename(__FILE__) . " -->"; ?>
<header class="page-titles">
    <div class="container clearfix">
        <h1 class="entry-title"><?php echo __( 'Search Icons', ICF_PLUGIN_NAME ) ?></h1>
        <h3 class="entry-subtitle"><?php echo __( 'Showing results for: ', ICF_PLUGIN_NAME ) . "`$s`" ?></h3>
    </div>
</header>
<?php do_action('icf_icon_searchform'); ?>
<section class="main search-results-main icf-search-results icon-search-results iconfinder-portfolio">
    <div class="gs_drib_area gs_drib_theme1">
        <div class="container">
            <div class="row">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="col-md-2 drib-shots icf-search-item icon-search-item icon-<?php echo icf_the_icon_id(); ?>">
                        <?php global $post; ?>
                        <?php if (icf_show_links()) : ?>
                            <a href="<?php echo get_the_permalink($post->post_parent); ?>" class="icon-preview">
                        <?php endif; ?>
                            <?php icf_the_preview( icf_get_the_icon_id(), null, array('title' => icf_get_the_post_tags()) ); ?>
                        <?php if (icf_show_links()) : ?>
                            </a>
                        <?php endif; ?>
                        <p class="info">
                            <?php if (icf_show_links()) : ?>
                                <?php echo icf_get_the_product_button(get_the_ID(), __( 'View Package', ICF_PLUGIN_NAME )); ?>
                            <?php else: ?>
                                <?php the_title(); ?>
                            <?php endif; ?>
                            <?php if ( icf_show_price() ) : ?>
                                <span class="price"><?php icf_get_the_price() != "" ? icf_the_currency_symbol(). icf_the_price() : __('Free', ICF_PLUGIN_NAME); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endwhile; ?>
                <?php else: ?>
                    <h3><?php _e( 'No results were found matching your search criteria. Please enter another search.', ICF_PLUGIN_NAME ); ?></h3>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container clearfix wpbeginner-pagenav">
        <?php icf_pagination(); ?>
    </div>
</section>