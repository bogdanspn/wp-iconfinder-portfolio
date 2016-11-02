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
    		<p>Get more details by visiting the <a href="https://developer.iconfinder.com/" target="_blank">Iconfinder API Documentation</a> </p>
    	</fieldset>
    	
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-username">
                <span><?php esc_attr_e('Iconfinder Username', $this->plugin_name); ?></span>
            </label>
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Iconfinder Username', $this->plugin_name); ?></span></legend>
                <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-username" name="<?php echo $this->plugin_name; ?>[username]" value="<?php echo $username; ?>"/>
            </fieldset>
        </fieldset>
        
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-api_client_id">
                <span><?php esc_attr_e('Iconfinder API Client ID', $this->plugin_name); ?></span>
            </label>
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Iconfinder API Client ID', $this->plugin_name); ?></span></legend>
                <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-api_client_id" name="<?php echo $this->plugin_name; ?>[api_client_id]" value="<?php echo $api_client_id; ?>"/>
            </fieldset>
        </fieldset>
        
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-api_client_secret">
                <span><?php esc_attr_e('Iconfinder API Client Secret', $this->plugin_name); ?></span>
            </label>
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Iconfinder API Client Secret', $this->plugin_name); ?></span></legend>
                <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-api_client_secret" name="<?php echo $this->plugin_name; ?>[api_client_secret]" value="<?php echo $api_client_secret; ?>"/>
            </fieldset>
        </fieldset>

        <?php submit_button('Save all changes', 'primary','submit', TRUE); ?>

    </form>

</div>
