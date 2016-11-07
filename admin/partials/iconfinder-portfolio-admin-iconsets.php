<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://iconify.it
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <h2 style="margin: 20px 0;"><?php echo esc_html(get_admin_page_title()); ?></h2>
    
    <?php if (! isset($items) || empty($items)) : ?>
    	<p><?php _e($message, $this->plugin_name ); ?></p>
    <?php else : ?>
    	<div class="notice notice-info">
        	<p><?php _e( 'You can use the shortcodes given below in combination to display specific sets. ', 'iconfinder-portfolio' ); ?></p>
    	</div>
    	<h3><?php _e('Example', $this->plugin->name); ?></h3>
    	<p class="notice notice">
    		<?php
    		    $sample = array_slice($items, 0, 3);
    		    $a = $sample[0]['iconset_id'];
    		    $b = $sample[1]['iconset_id'];
    		    $c = $sample[2]['iconset_id'];
    		?>
    		[iconfinder_portfolio sets=<?php echo "$a,$b,$c"; ?>]
    	</p>
    	<div class="gs_drib_area gs_drib_theme1">
			<div class="container">
				<div class="row">
					<?php foreach ($items as $iconset) : ?>
						<div class="col-md-4 drib-shots" style="float: left; margin: 0 10px 10px 0;">
							<p class="info"><?php echo $iconset['name']; ?></p>
						    <img src="<?php echo ICONFINDER_CDN_URL . "data/iconsets/previews/medium/{$iconset['identifier']}.png"; ?>" alt="<?php echo $iconset['name']; ?> preview image" />
						    <input type="text" value="[iconfinder_portfolio sets=<?php echo $iconset['iconset_id']; ?>]" size="40" style="font-size: 1em; width: 294px; display: block;" onClick="this.select();" />
						    <?php if (isset($iconset['category_string'])) :?>
						        <p>
						        	<strong>Categories:</strong><br/><?php echo $iconset['category_string']; ?><br/>
						        </p>
						    <?php endif; ?>
						    <?php if (isset($iconset['styles_string'])) :?>
						        <p>
						        	<strong>Styles:</strong><br/><?php echo $iconset['styles_string']; ?>
						        </p>
						    <?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
    	
    <?php endif; ?>
</div>
