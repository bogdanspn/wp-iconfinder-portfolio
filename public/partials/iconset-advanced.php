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
<div class="gs_drib_area gs_drib_theme1">
	<div class="container">
		<div class="row">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="col-md-4 drib-shots iconset-<?php icf_the_iconset_id(); ?>">
                    <?php if (icf_show_links()) : ?>
                        <a href="<?php icf_the_permalink(); ?>" target="_blank">
                    <?php endif; ?>
                        <?php icf_the_preview(); ?>
                    </a>
                    <p class="info">
                        <?php if (icf_show_links()) : ?>
                            <a href="<?php icf_the_permalink(); ?>" target="_blank">
                            <?php if (icf_is_advanced_mode()) : ?>
                                <?php _e('View Package', ICF_PLUGIN_NAME); ?>
                            <?php else: ?>
                                <?php _e('Buy on Iconfinder', ICF_PLUGIN_NAME); ?>
                            <?php endif; ?>
                            </a> 
                        <?php endif; ?>
                        <?php if (icf_show_links()) : ?>
                            <span class="price"><?php icf_get_the_price() != "" ? icf_the_currency_symbol() . icf_the_price() : "Free"; ?></span>
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