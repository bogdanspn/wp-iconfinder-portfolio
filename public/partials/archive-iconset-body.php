<?php
/**
 * The template to display iconset archives.
 *
 * @since 2.0
 */

?>
<!-- Default template: <?php echo basename(__FILE__); ?> -->

<?php do_action('icf_iconset_searchform'); ?>

<section class="main search-results-main iconset-search-results icf-search-results iconfinder-portfolio">
    <div class="gs_drib_area gs_drib_theme1 iconfinder-portfolio">
        <div class="container">
            <div class="row">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="col-md-4 drib-shots iconset-item iconset-<?php icf_the_iconset_id(); ?>">
                        <?php if (icf_show_links()) : ?>
                        <a href="<?php icf_the_permalink(); ?>" target="_blank" class="iconset-preview">
                            <?php endif; ?>
                            <?php icf_the_preview(icf_the_icon_id(), 'medium'); ?>
                            <?php if (icf_show_links()) : ?>
                        </a>
                    <?php endif; ?>
                        <div class="info">
                            <?php if (icf_show_links()) : ?>
                                <?php $elattrs = array('class' => 'icf-buy-button'); ?>
                                <?php icf_the_product_button(get_the_ID(), __( 'Buy Now', ICF_PLUGIN_NAME ), $elattrs); ?>
                            <?php else: ?>
                                <?php the_title(); ?>
                            <?php endif; ?>
                            <?php if (icf_show_price()) : ?>
                                <span class="price">
                                    <?php if (! empty(icf_get_the_price())) : ?>
                                        <?php icf_the_currency_symbol(); icf_the_price(); ?>
                                    <?php else: ?>
                                        <?php _e( 'Free', ICF_PLUGIN_NAME ); ?>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                            <div class="clear clearfix"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php else: ?>
                    <h3><?php _e( 'Nothing found', ICF_PLUGIN_NAME ); ?></h3>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container clearfix">
        <?php icf_pagination(); ?>
    </div>
</section>