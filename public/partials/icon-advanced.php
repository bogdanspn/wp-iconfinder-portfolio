<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://iconify.it
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/public/partials
 */
?>
<?php 
/*
 * If you are displaying the icons within an iconset using the 
 * 'iconset' shortcode attribute, it will be added to your theme's 
 * scope automatically in the vairable '$iconset_post'
 */
?>
<div class="icf-icon-advanced">
    <?php if (! empty($iconset_post)) : ?>
        <?php $iconset_post_id = get_val($iconset_post, 'ID'); ?>
        <div class="gs_drib_area gs_drib_theme1 iconset-item iconfinder-portfolio icf-button-bar">
            <div class="container">
                <div class="row">
                    <div class="col-md-2 icf-button icf-button-<?php echo $iconset_post_id; ?>">
                        <?php if (icf_show_links()) : ?>
                            <?php $elattrs = array( 'class' => 'icf-buy-button' , 'style' => 'margin: 0 0 0 5px !important;'); ?>
                            <?php icf_the_product_button( $iconset_post_id, __( 'Buy Now', ICF_PLUGIN_NAME ), $elattrs ); ?>
                        <?php else: ?>
                            <?php get_the_title($iconset_post_id); ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-10 iconset-item icf-iconset-header icf-iconset-header-<?php echo $iconset_post_id; ?>">
                        <h2>
                            <?php echo get_the_title($iconset_post_id); ?>
                            <?php if (icf_show_price()) : ?>
                                <span class="price">
                                    <?php if (! empty(icf_get_the_price($iconset_post_id))) : ?>
                                        <?php icf_the_currency_symbol(); icf_the_price($iconset_post_id); ?>
                                    <?php else: ?>
                                        <?php _e( 'Free', ICF_PLUGIN_NAME ); ?>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="gs_drib_area gs_drib_theme1 iconfinder-portfolio">
        <div class="container">
            <div class="row">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="col-md-1 drib-shots iconset-item icon-<?php echo get_the_ID(); ?>">
                        <?php if (icf_show_links()) : ?>
                            <a href="<?php icf_the_permalink(); ?>" target="_blank" class="iconset-preview">
                        <?php endif; ?>
                            <?php icf_the_preview(get_the_ID(), icf_img_size(), array('title' => icf_get_the_post_tags()) ); ?>
                        <?php if (icf_show_links()) : ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                <?php else: ?>
                    <h3><?php _e( 'Nothing found', ICF_PLUGIN_NAME ); ?></h3>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container clearfix wpbeginner-pagenav">
        <?php icf_pagination(); ?>
    </div>
</div>