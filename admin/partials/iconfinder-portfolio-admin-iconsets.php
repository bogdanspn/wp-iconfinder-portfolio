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

$a = $b = $c = null;
if (isset($items)) {
    $sample = array_slice($items, 0, 3);
    $a = @$sample[0]['iconset_id'];
    $b = @$sample[1]['iconset_id'];
    $c = @$sample[2]['iconset_id'];
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2 style="margin: 20px 0;"><?php echo esc_html(get_admin_page_title()); ?></h2>
    <?php if (! isset($items) || empty($items)) : ?>
    	<p><?php _e(@$message, $this->plugin_name ); ?></p>
    <?php else : ?>
        <div class="icf-admin-pagination">
            <?php icf_admin_iconsets_pagination($data['page_count'], $data['current_page']); ?>
        </div>
    	<div class="gs_drib_area gs_drib_theme1">
			<div class="container">
                <div class="row">
					<?php foreach ($items as $iconset) : ?>
						<div class="col-md-4 drib-shots icf-mode-<?php echo $this->get_mode(); ?>" style="float: left; margin: 0 10px 10px 0; padding: 10px; border: 1px solid #eee; background: #fff;">
                            <p class="info"><strong><a href="<?php echo ICONFINDER_LINK_ICONSETS . $iconset['identifier']; ?>" target="_blank"><?php echo $iconset['name']; ?></a></strong></p>
						    <img src="<?php echo ICONFINDER_CDN_URL . "data/iconsets/previews/medium/{$iconset['identifier']}.png"; ?>" alt="<?php echo $iconset['name']; ?> preview image" />
                            <?php if ($this->get_mode() === ICF_PLUGIN_MODE_ADVANCED) : ?>
                            <form method="post" name="iconfinder_portfolio_options" action="admin-post.php">
                                <input type="hidden" name="action" value="update_iconset_data" />
                                <input type="hidden" name="page_num" value="<?php icf_page_number(); ?>" />
                                <p class="button-row">
                                    <?php if (! $iconset['is_imported']): ?> 
                                        <input type="submit" name="submit" id="submit" class="button button-primary" style="float: left;" value="Import" <?php onclick_confirm_import(); ?> />
                                    <?php else: ?>
                                        <input type="submit" name="submit" id="submit" class="icf-button imported" style="float: left;" value="Update" <?php onclick_confirm_update(); ?> />
                                        <input type="submit" name="submit" id="trash" class="button button-secondary" style="float: right;" value="Delete" <?php onclick_confirm_delete(); ?> />
                                    <?php endif; ?>
                                    <span class="clear clearfix" style="clear: both;">&nbsp;</span>
                                </p>
                                <input type="hidden" id="<?php echo $this->plugin_name; ?>-import-iconset" name="<?php echo $this->plugin_name; ?>[iconset_id]" value="<?php echo $iconset['iconset_id']; ?>"/>
                            </form>
                            <?php endif; ?>
                            <p><input type="text" style="border: none; box-shadow: none; text-align: center; background: #eee;" value="[iconfinder_portfolio sets=<?php echo $iconset['iconset_id']; ?>]" size="40" style="font-size: 1em; width: 294px; display: block;" onClick="this.select();" /></p>
                            
                            <table>
                                <?php if ($iconset['is_imported'] == 1): ?>
                                <tr>
                                    <th valign="top" align="left" width="35%">Links</th>
                                    <td>
                                        <a href="<?php echo @$iconset['post_view_link']; ?>" target="_blank">View Post</a>&nbsp;|&nbsp;
                                        <a href="<?php echo @$iconset['post_edit_link']; ?>" target="_blank">Edit Post</a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th valign="top" align="left" width="35%">Icon Count</th>
                                    <td><?php echo $iconset['icons_count']; ?></td>
                                </tr>
                                <?php if (isset($iconset['category_string'])) :?>
                                    <tr>
                                        <th valign="top" align="left" width="35%">Categories</th>
                                        <td>
                                            <?php /* <div style=" width: 200px;"><?php echo $iconset['category_string']; ?> */ ?>
                                            <input type="text" name="#" size="25" style="border: none; box-shadow: none;" value="<?php echo $iconset['category_string']; ?>" />
                                        </td>
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
                    <div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
        <div class="icf-admin-pagination">
            <?php icf_admin_iconsets_pagination($data['page_count'], $data['current_page']); ?>
        </div>
    <?php endif; ?>
</div>
