<?php
/*
Template Name: Icon Search
*/

# icf_dump($wp_query);

?>
<?php get_header(); ?>
    <header class="page-titles">
        <div class="container clearfix">
            <h1 class="entry-title"><?php echo __( 'Search Icons', ICF_PLUGIN_NAME ) ?></h1>
            <h3 class="entry-subtitle"><?php echo __( 'Showing results for: ', ICF_PLUGIN_NAME ) . "`$s`" ?></h3>
        </div>
    </header>

    <?php do_action('icf_icon_searchform'); ?>

    <?php # echo do_shortcode('[searchandfilter types="icon" taxonomies="icon_category,icon_tag"]'); ?>

    <section class="main">
        
        <div class="gs_drib_area gs_drib_theme1">
            <div class="container">
                <div class="row">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <?php
                            $price     = get_post_meta( get_the_ID(), 'price', true );
                            $image     = get_the_post_thumbnail();
                            $icon_id   = get_post_meta(get_the_ID(), 'icon_id', true);
                            $username  = icf_get_option('username');
                            
                            $ref = null;
                            if (! empty($username)) {
                                $ref = "?ref={$username}";
                            }
                            $permalink = ICONFINDER_LINK_ICONS . $icon_id . $ref;
                        ?>
                        <div class="col-md-<?php echo icf_get_setting('icon_search_cols'); ?> drib-shots icon-<?php echo $icon_id; ?>">
                            <?php if (icf_get_option('use_purchase_link')) : ?>
                                <a href="<?php echo $permalink; ?>" target="_blank">
                            <?php endif; ?>
                                <?php echo $image; ?>
                            </a>
                            <p class="info">
                                <?php if (icf_get_option('use_purchase_link')) : ?>
                                    <a href="<?php echo $permalink; ?>" target="_blank">
                                <?php endif; ?>
                                    Buy on Iconfinder
                                <?php if (icf_get_option('use_purchase_link')) : ?>
                                    </a> 
                                <?php endif; ?>
                                <?php if (icf_get_option('use_purchase_link') && $price) : ?>
                                    <span class="price"><?php echo $price != "" ? "$" . $price : "Free"; ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <h3>Nothing found</h3>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="container clearfix wpbeginner-pagenav">
            <?php wpbeginner_numeric_posts_nav(); ?>
        </div>
    </section>
<!-- Footer -->
<?php get_footer(); ?>

