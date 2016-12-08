<?php
/**
 * The template for display portfolio post archives
 *
 * @since 3.0
 */
?>

<?php do_action('icf_icon_searchform'); ?>

<section class="main icf-search-results icon-search-results">
    <div class="container">
        <div class="row">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="col-md-2 icf-item icf-search-item icon-search-item icon-<?php echo icf_the_icon_id(); ?>">
                    <?php global $post; ?>
                    <?php if (icf_show_links()) : ?>
                        <a href="<?php echo get_the_permalink($post->post_parent); ?>" class="icon-preview">
                    <?php endif; ?>
                        <?php icf_the_post_thumbnail( icf_get_the_icon_id(), '@128' ); ?>
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
    <?php icf_pagination(); ?>
</section>