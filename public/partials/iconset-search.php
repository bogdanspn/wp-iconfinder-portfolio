<?php
/*
Template Name: Icon Search
*/
# icf_dump($wp_query);

# icf_dump(get_defined_vars());
?>
<?php get_header(); ?>
    <header class="page-titles">
        <div class="container clearfix">
            <h1 class="entry-title"><?php echo __( 'Search Icon Sets', ICF_PLUGIN_NAME ) ?></h1>
            <h3 class="entry-subtitle"><?php echo __( 'Icon Set Search Result for ', ICF_PLUGIN_NAME ) . "`$s`" ?></h3>
        </div>
    </header>
    <section class="main">
        <div class="container clearfix" style="margin-bottom: 20px;">
            <?php do_action('icf_iconset_searchform'); ?>
        </div>
        <div class="gs_drib_area gs_drib_theme1">
            <div class="container">
                <div class="row">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <?php # icf_dump($post); ?>
                        <?php
                            $price      = get_post_meta( get_the_ID(), 'price', true );
                            $image      = get_the_post_thumbnail();
                            $iconset_id = get_post_meta(get_the_ID(), 'iconset_id', true);
                            $identifier = get_post_meta(get_the_ID(), 'iconset_identifier', true);
                            $username   = icf_get_option('username');
                            
                            $ref = null;
                            if (! empty($username)) {
                                $ref = "?ref={$username}";
                            }
                            $permalink = ICONFINDER_LINK_ICONSETS . $identifier . $ref;
                        ?>
                        <div class="col-md-<?php echo icf_get_setting('iconset_search_cols'); ?> drib-shots icon-<?php echo $iconset_id; ?>">
                            <?php if (icf_get_option('use_purchase_link')) : ?>
                                <a href="<?php echo $permalink; ?>" target="_blank">
                            <?php endif; ?>
                                <?php the_post_thumbnail('full'); ?>
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
        <div class="container clearfix">
            <?php wpbeginner_numeric_posts_nav(); ?>
        </div>
    </section>
<!-- Footer -->
<?php get_footer(); ?>

