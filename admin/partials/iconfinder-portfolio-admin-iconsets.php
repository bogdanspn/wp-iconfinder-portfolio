<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @package Iconfinder Portfolio
 */
?>
<div class="wrap">
    <h2 style="margin: 20px 0;"><?php echo esc_html(get_admin_page_title()); ?></h2>
    <?php if (! isset($items) || empty($items)) : ?>
        <p><?php _e(@$message, $this->plugin_name ); ?></p>
    <?php else : ?>
        <div class="inner">
            <div class="icf-admin-pagination top">
                <p class="download-stats">Iconsets imported: <?php icf_the_iconset_count(); ?>, Icons imported: <?php icf_the_icon_count(); ?></p>
                <?php icf_admin_iconsets_pagination($data['page_count'], $data['current_page']); ?>
            </div>
            <?php foreach ($items as $iconset) : ?>
                <div class="iconset-card icf-mode-<?php echo $this->get_mode(); ?>">
                    <div class="top">
                        <p class="info"><strong><a href="<?php echo ICONFINDER_LINK_ICONSETS . $iconset['identifier']; ?>" target="_blank"><?php echo $iconset['name']; ?></a></strong></p>
                        <div class="preview">
                            <img src="<?php echo get_iconfinder_preview_url( 'medium', $iconset['identifier'] ); ?>" alt="<?php echo $iconset['name']; ?> preview image" />
                        </div>
                        <?php if ($this->get_mode() === ICF_PLUGIN_MODE_ADVANCED) : ?>
                            <form method="post" name="iconfinder_portfolio_options" action="admin-post.php">
                                <input type="hidden" name="action" value="update_iconset_data" />
                                <input type="hidden" name="paged" value="<?php icf_page_number(); ?>" />
                                <p class="button-row">
                                    <?php if (! $iconset['is_imported']): ?>
                                        <input type="submit" name="submit" id="submit" class="button button-import button-primary" value="Import" <?php onclick_confirm_import(); ?> />
                                    <?php else: ?>
                                        <input type="submit" name="submit" id="submit" class="icf-button button-update imported" value="Update" <?php onclick_confirm_update(); ?> />
                                        <input type="submit" name="submit" id="trash" class="button button-delete button-secondary" value="Delete" <?php onclick_confirm_delete(); ?> />
                                    <?php endif; ?>
                                    <span class="clear clearfix" style="clear: both;">&nbsp;</span>
                                </p>
                                <input type="hidden" id="<?php echo $this->plugin_name; ?>-import-iconset" name="<?php echo $this->plugin_name; ?>[iconset_id]" value="<?php echo $iconset['iconset_id']; ?>"/>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="bottom">
                        <table>
                            <?php if ($iconset['is_imported'] == 1): ?>
                                <tr>
                                    <th valign="top" align="left" width="35%">Links</th>
                                    <td>
                                        <a href="<?php echo @$iconset['post_view_link']; ?>" target="_blank">View Post</a>&nbsp;|&nbsp;
                                        <a href="<?php echo @$iconset['post_edit_link']; ?>" target="_blank">Edit Post</a> |
                                        <a href="javascript:void(0);" class="image-mapper" data-properties='{"iconset": "<?php echo $iconset['post_id']; ?>"}'>Icons</a>
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
                                    <th valign="top" align="left" width="35%">Updated</th>
                                    <td><?php echo $iconset['latest_sync']; ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div class="shortcode">
                        <input type="text" value="[iconfinder_portfolio sets=<?php echo $iconset['iconset_id']; ?>]" size="40" onClick="this.select();" />
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="icf-admin-pagination bottom">
                <?php icf_admin_iconsets_pagination($data['page_count'], $data['current_page']); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php add_image_mapper(); ?>
