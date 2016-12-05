<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @package Iconfinder Portfolio
 */
?>
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
                                    $plugin_mode         = get_val( $options, 'plugin_mode', ICF_PLUGIN_MODE_BASIC );
                                    $use_powered_by_link = is_true(get_val( $options, 'use_powered_by_link', true ));
                                    $use_purchase_link   = is_true(get_val( $options, 'use_purchase_link', true ));
                                    $selected_sizes      = get_val($options, 'icon_preview_sizes');
                                    $iconset_img_size    = get_val($options, 'iconset_preview_size');
                                    $posts_per_page      = icf_get_option('posts_per_page', ICF_SEARCH_POSTS_PER_PAGE);
                                    $show_price          = icf_get_option('show_price', true);
                                    
                                    if (! is_array($selected_sizes) || empty($selected_sizes)) {
                                        $selected_sizes = array(icf_get_setting('icon_default_preview_size'));
                                    }
                                    
                                    if (empty($iconset_img_size)) {
                                        $iconset_img_size = icf_get_setting('iconset_default_preview_size');
                                    }
                                    
                                    if (empty($plugin_mode)) {
                                        $plugin_mode = ICF_PLUGIN_MODE_BASIC;
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
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-plugin_mode">
                                        <?php esc_attr_e('Plugin Mode', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Iconfinder Plugin Mode', $this->plugin_name); ?></span></legend>
                                        <p><?php _e( 'By default, this plugin pulls data from the Iconfinder API and stores it in a simple cache. The data is not searchable in the basic mode. If you enable storing your icon data in WordPress to make it searchable, be aware that large amounts of data can take a while to clone.', $this->plugin_name ); ?></p>
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[plugin_mode]" value="basic" <?php if ($plugin_mode ==  ICF_PLUGIN_MODE_BASIC) : ?>checked="checked"<?php endif; ?> />Basic
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[plugin_mode]" value="advanced" <?php if ($plugin_mode ==  ICF_PLUGIN_MODE_ADVANCED) : ?>checked="checked"<?php endif; ?> />Advanced
                                    </div>
                                </div>
                                <?php if ($plugin_mode == ICF_PLUGIN_MODE_ADVANCED) : ?>
                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-plugin_options">
                                        <?php esc_attr_e('Preview Image Sizes', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Iconfinder Preview Import Settings', $this->plugin_name); ?></span></legend>
                                        <p><?php _e( 'Be aware that importing more previews per icon will cause the imports to take longer. Fewer is better.', $this->plugin_name ); ?></p>
                                        <?php $preview_sizes = icf_get_setting('icon_import_sizes'); ?>
                                        <?php foreach ($preview_sizes as $size): ?> 
                                            <?php 
                                                $checked = null; 
                                                if (in_array($size, $selected_sizes)) {
                                                    $checked = ' checked="checked" ';
                                                }
                                            ?>
                                            <input type="checkbox" name="<?php echo $this->plugin_name; ?>[icon_preview_sizes][]" value="<?php echo $size; ?>" <?php echo $checked; ?> /><?php echo $size; ?> pixels&nbsp;&nbsp;&nbsp;
                                            <?php $checked = null; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-plugin_options">
                                        <?php esc_attr_e('Iconset Preview Size', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Iconfinder Iconset Preview Image Size', $this->plugin_name); ?></span></legend>
                                        <p><?php _e( 'Select the size of the iconset preview image you want to import from Iconfinder.', $this->plugin_name ); ?></p>
                                        <?php $preview_sizes = icf_get_setting('iconset_preview_sizes'); ?>
                                        <?php foreach ($preview_sizes as $size): ?> 
                                            <?php 
                                                $checked = null; 
                                                if ($size === $iconset_img_size) {
                                                    $checked = ' checked="checked" ';
                                                }
                                                $size_label = $size;
                                                switch ($size) {
                                                    case 'medium':
                                                        $size_label = 'small';
                                                        break;
                                                    case 'medium-2x':
                                                        $size_label = 'medium';
                                                        break;
                                                }
                                            ?>
                                            <input type="radio" name="<?php echo $this->plugin_name; ?>[iconset_preview_size]" value="<?php echo $size; ?>" <?php echo $checked; ?> /> <?php echo $size_label; ?>&nbsp;&nbsp;&nbsp;
                                            <?php $checked = null; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-posts-per-page">
                                        <?php esc_attr_e('Search Resulets Per Page', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Search Results Per Page', $this->plugin_name); ?></span></legend>
                                        <input type="number" min="1" max="<?php echo ICF_SEARCH_POSTS_PER_PAGE_MAX; ?>" step="1" id="<?php echo $this->plugin_name; ?>-posts-per-page" name="<?php echo $this->plugin_name; ?>[posts_per_page]" value="<?php echo $posts_per_page; ?>"/>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php $currency_symbol = icf_get_option('currency_symbol', ICF_DEFAULT_CURRENCY); ?>
                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-currency_symbol">
                                        <?php esc_attr_e('Currency Symbol', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Currency Symbol', $this->plugin_name); ?></span></legend>
                                        <?php echo icf_currency_selector( $currency_symbol, $this->plugin_name . "[currency_symbol]" ); ?>
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
                                        <?php esc_attr_e('Show Product Links', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Show Product Links', $this->plugin_name); ?></span></legend>
                                        <p><?php esc_attr_e( 'In advanced mode, will show a link to a local product page if you create a custom field on the iconset named `product_link`, otherwise displays a "Buy on Iconfinder" link next to icons data using your referrer code. Basic Mode does not allow linking to local products.', $this->plugin_name ); ?></p>
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[use_purchase_link]" value="true" <?php if ($use_purchase_link) : ?>checked="checked"<?php endif; ?> />Yes
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[use_purchase_link]" value="false" <?php if (! $use_purchase_link) : ?>checked="checked"<?php endif; ?> />No
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label" for="<?php echo $this->plugin_name; ?>-plugin_mode">
                                        <?php esc_attr_e('Show Prices', $this->plugin_name); ?>
                                    </label>
                                    <div class="form-option">
                                        <legend class="screen-reader-text"><span><?php _e('Show Prices', $this->plugin_name); ?></span></legend>
                                        <p><?php _e( 'Use this setting to show or hide prices site-wide. You can override this in shortcodes using `show_price=true`.', $this->plugin_name ); ?></p>
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[show_price]" value="1" <?php if ($show_price ==  true) : ?>checked="checked"<?php endif; ?> />Yes
                                        <input type="radio" name="<?php echo $this->plugin_name; ?>[show_price]" value="0" <?php if ($show_price ==  false) : ?>checked="checked"<?php endif; ?> />No
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
                <?php if (! icf_is_advanced_mode()) : ?>
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
                                            <?php submit_button( 'Clear Cache', 'secondary', 'submit', false , array('onclick' => confirm_js('Are you sure you want to Clear the cache?')) ); ?>
                                            <?php submit_button( 'Refresh Cache', 'primary', 'submit', false, array('onclick' => confirm_js('Are you sure you want to Refresh the cache?')) ); ?>
                                        </div>
                                    </div>
                                    <input type="hidden" id="<?php echo $this->plugin_name; ?>-purgecache" name="<?php echo $this->plugin_name; ?>[purgecache]" value="true"/>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <br class="clear clearfix">
    </div>
</div>