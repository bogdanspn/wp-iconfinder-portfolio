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

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    
    <div class="notice notice-info">
		<p><?php _e( 'Get more details by visiting the <a href="https://developer.iconfinder.com/" target="_blank">Iconfinder API Documentation</a> ', $this->plugin_name ); ?></p>
	</div>
    
    <form method="post" name="iconfinder_portfolio_options" action="options.php">
    
		<?php
		//Grab all options
		$options = get_option($this->plugin_name);
		
		$api_client_id     = $options['api_client_id'];
		$api_client_secret = $options['api_client_secret'];
		$username          = $options['username'];
		?>
		
		<?php
			settings_fields($this->plugin_name);
			do_settings_sections($this->plugin_name);
		?>
    	
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-username">
                <h3><?php esc_attr_e('Iconfinder Username', $this->plugin_name); ?></h3>
            </label>
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Iconfinder Username', $this->plugin_name); ?></span></legend>
                <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-username" name="<?php echo $this->plugin_name; ?>[username]" value="<?php echo $username; ?>"/>
            </fieldset>
        </fieldset>
        
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-api_client_id">
                <h3><?php esc_attr_e('Iconfinder API Client ID', $this->plugin_name); ?></h3>
            </label>
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Iconfinder API Client ID', $this->plugin_name); ?></span></legend>
                <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-api_client_id" name="<?php echo $this->plugin_name; ?>[api_client_id]" value="<?php echo $api_client_id; ?>"/>
            </fieldset>
        </fieldset>
        
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-api_client_secret">
                <h3><?php esc_attr_e('Iconfinder API Client Secret', $this->plugin_name); ?></h3>
            </label>
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Iconfinder API Client Secret', $this->plugin_name); ?></span></legend>
                <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-api_client_secret" name="<?php echo $this->plugin_name; ?>[api_client_secret]" value="<?php echo $api_client_secret; ?>"/>
            </fieldset>
        </fieldset>

        <?php submit_button('Save all changes', 'primary','purgecache', TRUE); ?>
    </form>
    
    <form method="post" name="iconfinder_portfolio_options" action="admin-post.php">
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-purgecache">
                <h3><?php esc_attr_e('Purge Cache', $this->plugin_name); ?></h3>
            </label>
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Purge Cache', $this->plugin_name); ?></span></legend>
                <div class="notice notice-info inline"><p><?php _e( 'Since your Iconfinder portfolio only changes when you upload new icons, there is no need to make live requests to the API with every pageview. By default, the plugin caches the results of API calls to reduce bandwidth demands and to insure quick responses to page requests. Click the button below to clear the cache each time you upload new icons to your portfolio. You do not need to clear the cache when you add a new shortcode to your site.', $this->plugin_name ); ?></p></div>
                <?php submit_button( 'Clear Cache', 'secondary' ); ?>
            </fieldset>
        </fieldset>
        <input type="hidden" id="<?php echo $this->plugin_name; ?>-purgecache" name="<?php echo $this->plugin_name; ?>[purgecache]" value="true"/>
    </form>

</div>
