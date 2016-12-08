<?php
/**
 * The template for displaying portfolio posts
 *
 * @since 1.0
 */
global $post;
get_header(); ?>
<?php echo "<!-- Default template: " . basename(__FILE__) . " -->"; ?>
<header class="page-titles">
    <div class="container clearfix">
        <div class="page-titles-wrap">
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <?php if ( get_post_meta( $post->ID, 'subtitle', true ) ) : ?>
                <h3 class="entry-subtitle"><?php echo get_post_meta( $post->ID, 'subtitle', true ) ?></h3>
            <?php endif; ?>
        </div>
    </div>
</header>
<?php do_action( 'icf_icon_searchform', array( 'search_iconset_id' => get_post_meta( $post->ID, 'iconset_id', true ) )); ?>
<section class="main">
    <div class="container">
        <div id="content">
            <div class="posts">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <div class="icf-icon-advanced">
                        <!-- iconset -->
                        <div class="iconset-item icf-button-bar">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12 icf-preview icf-iconset-preview-<?php echo get_the_ID(); ?>">
                                    <?php if (icf_show_links()) : ?>
                                        <a href="<?php icf_the_permalink(); ?>" target="_blank" class="iconset-preview">
                                    <?php endif; ?>
                                        <?php icf_the_post_thumbnail( get_the_ID(), 'large' ); ?>
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
                            <div class="iconfinder-portfolio">
                                <div class="container">
                                    <div class="row">
                                    <?php foreach ($icons as $icon) : ?>
                                        <div class="col-md-2 icf-item iconset-item icon-<?php echo $icon->ID; ?>">
                                        <?php if (icf_show_links()) : ?>
                                            <a href="<?php icf_the_permalink($icon->ID); ?>" target="_blank" class="iconset-preview">
                                        <?php endif; ?>
                                            <?php icf_the_post_thumbnail( $icon->ID, '@128' ); ?>
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
                        <?php icf_pagination(); ?>
                    </div>
                <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>