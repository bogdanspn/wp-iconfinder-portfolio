<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php echo "<!-- Default template: " . basename(__FILE__) . " -->"; ?>
<header class="page-titles">
    <div class="container clearfix">
        <h1 class="entry-title"><?php echo __( 'Search Icon Sets', ICF_PLUGIN_NAME ) ?></h1>
        <h3 class="entry-subtitle"><?php echo __( 'Icon Set Search Result for ', ICF_PLUGIN_NAME ) . "`$s`" ?></h3>
    </div>
</header>
<?php echo "<!-- Default template: " . basename(__FILE__) . "-->"; ?>
<?php do_action('icf_iconset_searchform'); ?>
<section class="main search-results-main iconset-search-results icf-search-results iconfinder-portfolio">
    <div class="gs_drib_area gs_drib_theme1">
        <div class="container">
            <div class="row">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="col-md-6 drib-shots iconset-search-item icf-search-item iconset-<?php icf_the_iconset_id(); ?>">
                        <?php if (icf_show_links()) : ?>
                            <a href="<?php icf_the_permalink(); ?>" target="_blank" class="iconset-preview">
                        <?php endif; ?>
                            <?php icf_the_preview( get_the_ID(), array('550', ''), array('title' => icf_get_the_post_tags()) ); ?>
                        <?php if (icf_show_links()) : ?>
                            </a>
                        <?php endif; ?>
                        <p class="info">
                            <?php if (icf_show_links()) : ?>
                                <?php icf_the_product_button(get_the_ID(), __( 'View Package', ICF_PLUGIN_NAME )); ?>
                            <?php else: ?>
                                <?php the_title(); ?>
                            <?php endif; ?>
                            <span class="price"><?php icf_get_the_price() != "" ? icf_the_currency_symbol(). icf_the_price() : __('Free', ICF_PLUGIN_NAME); ?></span>
                        </p>
                    </div>
                <?php endwhile; ?>
                <?php else: ?>
                    <h3><?php _e( 'No results were found matching your search criteria. Please enter another search.', ICF_PLUGIN_NAME ); ?></h3>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container clearfix">
        <?php icf_pagination(); ?>
    </div>
</section>
<!-- Footer -->