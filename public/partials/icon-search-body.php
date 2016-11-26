<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
# icf_dump(get_defined_vars());
?>
<header class="page-titles">
    <div class="container clearfix">
        <h1 class="entry-title"><?php echo __( 'Search Icons', ICF_PLUGIN_NAME ) ?></h1>
        <h3 class="entry-subtitle"><?php echo __( 'Showing results for: ', ICF_PLUGIN_NAME ) . "`$s`" ?></h3>
    </div>
</header>
<?php do_action('icf_icon_searchform'); ?>
<section class="main search-results-main">
    <div class="gs_drib_area gs_drib_theme1">
        <div class="container">
            <div class="row">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="col-md-2 drib-shots icon-<?php echo icf_the_icon_id(); ?>">
                        <?php if (icf_show_links()) : ?>
                            <a href="<?php icf_the_permalink(); ?>" target="_blank">
                        <?php endif; ?>
                            <?php icf_the_preview( null, array('title' => icf_get_the_post_tags()) ); ?>
                        <?php if (icf_show_links()) : ?>    
                            </a>
                        <?php endif; ?>
                        <p class="info">
                            <?php if (icf_show_links()) : ?>
                                <a href="<?php icf_the_permalink(); ?>" target="_blank">
                                    <?php _e('View Product', ICF_PLUGIN_NAME ); ?>
                                </a> 
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
    <div class="container clearfix wpbeginner-pagenav">
        <?php wpbeginner_numeric_posts_nav(); ?>
    </div>
</section>