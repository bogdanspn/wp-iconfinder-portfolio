<?php
/**
 * The template for display portfolio post archives
 *
 * @since 3.0
 */
global $post;

$iconset_name = null;

if (! empty($post) && ! empty($post->post_parent) ) {
    $iconset_post = get_post($post->post_parent);
    $iconset_name = $iconset_post->post_title;
}

get_header(); ?>
<?php echo "<!-- Default template: " . basename(__FILE__) . " -->"; ?>
<header class="page-titles">
    <div class="container clearfix">
        <div class="page-titles-wrap">
            <?php if ( is_search() ) : ?>
                <h1 class="entry-title"><?php _e( sprintf('Showing search results for `%s`', get_search_query() ) , ICF_PLUGIN_NAME ); ?></h1>
                <?php if ( is_limited_search() && ! empty($iconset_name) ) : ?>
                    <h2 class="entry-subtitle"><?php _e( sprintf('Searching icon set `%s`', $iconset_name ) , ICF_PLUGIN_NAME ); ?></h2>
                <?php endif; ?>
            <?php else : ?>
                <h1 class="entry-title"><?php _e( 'Icons Archive' , ICF_PLUGIN_NAME ); ?></h1>
            <?php endif; ?>
        </div>
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
<?php get_footer(); ?>
