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
 
if (! isset($options)) $options = array(
    'show_links' => 1,
    'show_price' => 1
);
?>
<div class="gs_drib_area gs_drib_theme1">
	<div class="container">
		<div class="row">
			<?php foreach ($items as $iconset) : ?>
				<div class="col-md-4 drib-shots">
				    <?php if ($options['show_links']) : ?>
				        <a href="<?php echo $iconset['permalink']; ?>" target="_blank">
				    <?php endif; ?>
				    	<img src="<?php echo $iconset['preview']; ?>" alt="<?php echo $iconset['name']; ?> preview image" />
				    </a>
				    <p class="info">
				        <?php if ($options['show_links']) : ?>
				    	    <a href="<?php echo $iconset['permalink']; ?>" target="_blank">
				    	<?php endif; ?>
				    	    <?php echo $iconset['name']; ?>
				    	<?php if ($options['show_links']) : ?>
				    	    </a> 
				    	<?php endif; ?>
				    	<?php if ($options['show_price']) : ?>
				    	    <span class="price"><?php echo $iconset['price'] != "" ? "$" . $iconset['price'] : "Free"; ?></span>
				    	<?php endif; ?>
				    </p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>