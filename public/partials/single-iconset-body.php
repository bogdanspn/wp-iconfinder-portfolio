<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @package Iconfinder Portfolio
 */
?>
<?php echo "<!-- Default template: " . basename(__FILE__) . " -->"; ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div class="icf-icon-advanced">
        <!-- iconset -->
        <div class="gs_drib_area gs_drib_theme1 iconset-item iconfinder-portfolio icf-button-bar">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 icf-preview icf-iconset-preview-<?php echo get_the_ID(); ?>">
                        <?php if (icf_show_links()) : ?>
                        <a href="<?php icf_the_permalink(); ?>" target="_blank" class="iconset-preview">
                            <?php endif; ?>
                            <?php the_post_thumbnail('large-image'); ?>
                            <?php if (icf_show_links()) : ?>
                        </a>
                    <?php endif; ?>
                    </div>
                    <div class="col-md-2 icf-button icf-button-<?php echo get_the_ID(); ?>">
                        <?php if (icf_show_links()) : ?>
                            <?php $elattrs = array( 'class' => 'icf-buy-button' , 'style' => 'margin: 0 0 0 5px !important;'); ?>
                            <?php icf_the_product_button( get_the_ID(), __( 'Buy Now', ICF_PLUGIN_NAME ), $elattrs ); ?>
                        <?php else: ?>
                            <?php the_title(); ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-10 iconset-item icf-iconset-header icf-iconset-header-<?php echo get_the_ID(); ?>">
                        <h2>
                            <?php the_title(); ?>
                            <?php if (icf_show_price()) : ?>
                                <span class="price">
                                    <?php if (! empty(icf_get_the_price(get_the_ID()))) : ?>
                                        <?php icf_the_currency_symbol(); icf_the_price(get_the_ID()); ?>
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
        <!--/iconset-->
        <!-- icons -->
        <?php $icons = icf_get_children(get_the_ID()); ?>
        <?php if (count($icons)) : ?>
            <div class="gs_drib_area gs_drib_theme1 iconfinder-portfolio">
                <div class="container">
                    <div class="row">
                        <?php foreach ($icons as $icon) : ?>
                            <div class="col-md-2 drib-shots iconset-item icon-<?php echo $icon->ID; ?>">
                                <?php if (icf_show_links()) : ?>
                                <a href="<?php icf_the_permalink($icon->ID); ?>" target="_blank" class="iconset-preview">
                                    <?php endif; ?>
                                    <?php echo get_the_post_thumbnail( $icon->ID, icf_img_size(), array('title' => icf_get_the_post_tags($icon->ID)) ); ?>
                                    <?php if (icf_show_links()) : ?>
                                </a>
                            <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- /icons -->
        <div class="container clearfix wpbeginner-pagenav">
            <?php icf_pagination(); ?>
        </div>
    </div>

<?php endwhile; ?>
<?php endif; ?>
