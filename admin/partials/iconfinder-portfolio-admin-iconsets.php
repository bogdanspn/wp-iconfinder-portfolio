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
    	<h3><?php _e('Example', $this->plugin_name); ?></h3>
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
						<div class="col-md-4 drib-shots" style="float: left; margin: 0 10px 10px 0; padding: 10px; border: 1px solid #eee; background: #fff;">
                            <p class="info"><strong><?php echo $iconset['name']; ?></strong></p>
                            
						    <img src="<?php echo ICONFINDER_CDN_URL . "data/iconsets/previews/medium/{$iconset['identifier']}.png"; ?>" alt="<?php echo $iconset['name']; ?> preview image" />
                            <form method="post" name="iconfinder_portfolio_options" action="admin-post.php">
                                <input type="hidden" name="action" value="update_iconset_data" />
                                <p class="button-row">
                                    <?php if (! $iconset['is_imported']): ?> 
                                        <input type="submit" name="submit" id="submit" class="button button-primary" style="float: left;" value="Import"/>
                                    <?php else: ?>
                                        <input type="submit" name="submit" id="submit" class="icf-button imported" style="float: left;" value="Update" <?php echo onclick_confirm_update(); ?> />
                                        <input type="submit" name="submit" id="trash" class="button button-secondary" style="float: right;" value="Trash" <?php onclick_confirm_delete(); ?> />
                                    <?php endif; ?>
                                    <span class="clear clearfix" style="clear: both;">&nbsp;</span>
                                </p>
                                <input type="hidden" id="<?php echo $this->plugin_name; ?>-import-iconset" name="<?php echo $this->plugin_name; ?>[iconset_id]" value="<?php echo $iconset['iconset_id']; ?>"/>
                            </form>
                            <p><input type="text" value="[iconfinder_portfolio sets=<?php echo $iconset['iconset_id']; ?>]" size="40" style="font-size: 1em; width: 294px; display: block;" onClick="this.select();" /></p>
                            
                            <table style="margin: 10px; width: 280px">
                                <tr>
                                    <th valign="top" align="left" width="35%">Icon Count</th>
                                    <td><?php echo $iconset['icons_count']; ?></td>
                                </tr>
                                <?php if (isset($iconset['category_string'])) :?>
                                    <tr>
                                        <th valign="top" align="left" width="35%">Categories</th>
                                        <td><?php echo $iconset['category_string']; ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($iconset['styles_string'])) :?>
                                    <tr>
                                        <th valign="top" align="left" width="35%">Styles</th>
                                        <td><?php echo $iconset['styles_string']; ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($iconset['latest_sync'])) :?>
                                    <tr>
                                        <th valign="top" align="left" width="35%">Latest Sync</th>
                                        <td><?php echo $iconset['latest_sync']; ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
    	
    <?php endif; ?>
</div>
