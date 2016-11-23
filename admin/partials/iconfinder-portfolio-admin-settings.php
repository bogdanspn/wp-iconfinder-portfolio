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
    <div id="poststuff" class="clear">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle"><span><?php echo esc_html('Iconfinder API Documentation', $this->plugin_name); ?></span></h2>
                        <div class="inside">
                            <p><?php _e( 'Get more details by visiting the <a href="https://developer.iconfinder.com/" target="_blank">Iconfinder API Documentation</a> ', $this->plugin_name ); ?></p>
                        </div>
                    </div>
                </div>
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle"><span><?php echo esc_html(get_admin_page_title()); ?></span></h2>
                        <div class="inside">

                            <form method="post" name="iconfinder_portfolio_options" action="options.php">

                                <?php
                                    //Grab all options
                                    $options = get_option($this->plugin_name);

                                    $api_client_id       = get_val( $options, 'api_client_id' );
                                    $api_client_secret   = get_val( $options, 'api_client_secret' );
                                    $username            = get_val( $options, 'username' );
                                    $plugin_mode         = get_val( $options, 'plugin_mode', 'basic' );
                                    $use_powered_by_link = is_true(get_val( $options, 'use_powered_by_link', true ));
                                    $use_purchase_link   = is_true(get_val( $options, 'use_purchase_link', true ));
                                    
                                    if (empty($plugin_mode)) {
                                        $plugin_mode = 'basic';
                                    }

                                    settings_fields($this->plugin_name);
                                    do_settings_sections($this->plugin_name);
                                ?>

                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-username">
                                        <?php esc_attr_e('Iconfinder Username', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Iconfinder Username', $this->plugin_name); ?></span></legend>
                                        <input type="text" class="medium-text" id="<?php echo $this->plugin_name; ?>-username" name="<?php echo $this->plugin_name; ?>[username]" value="<?php echo $username; ?>"/>
                                    </div>
                                </div>
                                <div class="form-row">
                                   <label class="form-label" for="<?php echo $this->plugin_name; ?>-api_client_id">
                                        <?php esc_attr_e('Iconfinder API Client ID', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Iconfinder API Client ID', $this->plugin_name); ?></span></legend>
                                        <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-api_client_id" name="<?php echo $this->plugin_name; ?>[api_client_id]" value="<?php echo $api_client_id; ?>"/>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-api_client_secret">
                                        <?php esc_attr_e('Iconfinder API Client Secret', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Iconfinder API Client Secret', $this->plugin_name); ?></span></legend>
                                        <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-api_client_secret" name="<?php echo $this->plugin_name; ?>[api_client_secret]" value="<?php echo $api_client_secret; ?>"/>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-api_client_secret">
                                        <?php esc_attr_e('Plugin Mode', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Iconfinder API Client Secret', $this->plugin_name); ?></span></legend>
                                        <p><?php _e( 'By default, this plugin pulls data from the Iconfinder API and stores it in a simple cache. The data is not searchable in the default mode. If you enable storing your icon data in WordPress to make it searchable, be aware that large amounts of data can take a while to clone.', $this->plugin_name ); ?></p>
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[plugin_mode]" value="basic" <?php if ($plugin_mode ==  'basic') : ?>checked="checked"<?php endif; ?> />Basic
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[plugin_mode]" value="advanced" <?php if ($plugin_mode ==  'advanced') : ?>checked="checked"<?php endif; ?> />Advanced
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-api_client_secret">
                                        <?php esc_attr_e('Powered by Iconfinder Link', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Buy on Iconfinder Link', $this->plugin_name); ?></span></legend>
                                        <p><?php esc_attr_e( 'Displays a "Powered by Iconfinder" link in your site\'s footer using your referrer code.', $this->plugin_name ); ?></p>
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[use_powered_by_link]" value="true" <?php if ($use_powered_by_link) : ?>checked="checked"<?php endif; ?> />Yes
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[use_powered_by_link]" value="false" <?php if (! $use_powered_by_link) : ?>checked="checked"<?php endif; ?> />No
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-api_client_secret">
                                        <?php esc_attr_e('Buy on Iconfinder Link', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Buy on Iconfinder Link', $this->plugin_name); ?></span></legend>
                                        <p><?php esc_attr_e( 'Displays a "Buy on Iconfinder" link next to icons data using your referrer code.', $this->plugin_name ); ?></p>
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[use_purchase_link]" value="true" <?php if ($use_purchase_link) : ?>checked="checked"<?php endif; ?> />Yes
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[use_purchase_link]" value="false" <?php if (! $use_purchase_link) : ?>checked="checked"<?php endif; ?> />No
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-option">
                                        <?php submit_button('Save Settings', 'primary','purgecache', TRUE); ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
<?php
//        $valid['store_icon_data']        = get_val( $input, 'store_icon_data', false );
//        $valid['show_footer_link']       = get_val( $input, 'show_footer_link', false );
//        $valid['buy_on_iconfinder_link'] = get_val( $input, 'buy_on_iconfinder_link', false );
?>
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle"><span><?php esc_attr_e('Purge Cache', $this->plugin_name); ?></span></h2>
                        <div class="inside">
                            <form method="post" name="iconfinder_portfolio_options" action="admin-post.php">
                                <input type="hidden" name="action" value="purge_cache" />
                                <div class="form-row">
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Purge Cache', $this->plugin_name); ?></span></legend>
                                        <p><?php _e( 'Since your Iconfinder portfolio only changes when you upload new icons, there is no need to make live requests to the API with every pageview. By default, the plugin caches the results of API calls to reduce bandwidth demands and to insure quick responses to page requests. Click the button below to clear the cache each time you upload new icons to your portfolio. You do not need to clear the cache when you add a new shortcode to your site.', $this->plugin_name ); ?></p>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-option">
                                        <?php submit_button( 'Clear Cache', 'secondary' ); ?>
                                    </div>
                                </div>
                                <input type="hidden" id="<?php echo $this->plugin_name; ?>-purgecache" name="<?php echo $this->plugin_name; ?>[purgecache]" value="true"/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear clearfix">
    </div>
</div>