<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://iconify.it
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/admin
 * @author     Scott Lewis <scott@iconify.it>
 */
class Iconfinder_Portfolio_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Iconfinder_Portfolio_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Iconfinder_Portfolio_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/iconfinder-portfolio-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Iconfinder_Portfolio_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Iconfinder_Portfolio_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/iconfinder-portfolio-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		/*
		 * Add a settings page for this plugin to the Admin.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		add_menu_page( 'Iconfinder Portfolio Setup', 'Iconfinder Portfolio', 'manage_options', $this->plugin_name);
		add_submenu_page( $this->plugin_name, 'API Settings', 'API Settings', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
		add_submenu_page( $this->plugin_name, 'My Collections', 'My Collections', 'manage_options', $this->plugin_name . '-collections', array($this, 'display_collections_page'));
		add_submenu_page( $this->plugin_name, 'My Iconsets', 'My Iconsets', 'manage_options', $this->plugin_name . '-iconsets', array($this, 'display_iconsets_page'));
		add_submenu_page( $this->plugin_name, 'Documentation', 'Documentation', 'manage_options', $this->plugin_name . '-documentation', array($this, 'display_plugin_documentation'));
	}
	
	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		/*
		*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		    */
		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
		);
		return array_merge(  $settings_link, $links );
	}
	
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
	
	    echo $this->apply_admin_theme(null, 'iconfinder-portfolio-admin-display.php');
	}
	
	/**
	 * Render the documentation page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_documentation() {
	
	    $_options = get_option('iconfinder-portfolio');
		$username = isset($_options['username']) ? $_options['username'] : null;

	    $response = iconfinder_call_api(
            $this->get_admin_api_url('categories'), 
            icf_get_cache_key($username, 'categories')
        );
        
        if (isset($response['categories'])) {
	    	$data['categories'] = $response['categories'];
	    }
	    
	    $response = iconfinder_call_api(
            $this->get_admin_api_url('styles'), 
            icf_get_cache_key($username, 'styles')
        );
        
        if (isset($response['styles'])) {
	    	$data['styles'] = $response['styles'];
	    }
	
	    echo $this->apply_admin_theme($data, 'iconfinder-portfolio-documentation.php');
	}
	
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_collections_page() {
	
	    # Grab the collections from the API
	    
	    $_options = get_option('iconfinder-portfolio');
		$username = isset($_options['username']) ? $_options['username'] : null;
	    
	    $data = array('message' => 'Enter your API credentials on the API Settings page to list your collections here');
	    
	    $response = iconfinder_call_api(
            $this->get_admin_api_url('collections'), 
            icf_get_cache_key($username, 'collections')
        );
	    
	    if (isset($response['items'])) {
	    	$data['items'] = $response['items'];
	    }
	    
	    echo $this->apply_admin_theme($data, 'iconfinder-portfolio-admin-collections.php');
	}
	
	private function dump($what) {
	
	    die('<pre>' . print_r($what, true) . '</pre>');
	}
	
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_iconsets_page() {
	
		# Grab the iconsets from the API
		
	    $_options = get_option( 'iconfinder-portfolio' );
		$username = isset( $_options['username'] ) ? $_options['username'] : null;
		
		$data = array( 'message' => 'Enter your API credentials on the API Settings page to list your iconsets here' );
		
		$response = iconfinder_call_api(
            $this->get_admin_api_url( 'iconsets' ), 
             icf_get_cache_key( $username, 'iconsets' )
        );
        
	    if (isset($response['items'])) {
	    	$data['items'] = $response['items'];
	    }
	    
	    if (count($data['items'])) {
	        foreach ($data['items'] as &$item) {
	            if (isset($item['categories'])) {
	                $categories = array();
	                foreach ($item['categories'] as $category) {
	                    array_push($categories, $category['identifier']);
	                }
	                $item['category_string'] = implode(',', $categories);
	            }
	            if (isset($item['styles'])) {
	                $styles = array();
	                foreach ($item['styles'] as $style) {
	                    array_push($styles, $style['identifier']);
	                }
	                $item['styles_string'] = implode(',', $styles);
	            }
	        }
	    }
	    
		echo $this->apply_admin_theme($data, 'iconfinder-portfolio-admin-iconsets.php');
	}
	
	/**
	 * Determine correct API URl from the shortcode attrs
	 * @param $attrs - The shortcode attrs
	 * @since 1.0.0
	 */
	private function get_admin_api_url($channel) {
	
		$_options = get_option('iconfinder-portfolio');
		
		$valid_channels = array('iconsets', 'collections', 'categories', 'styles');
		
		$api_client_id     = isset($_options['api_client_id']) ? $_options['api_client_id'] : null;
		$api_client_secret = isset($_options['api_client_secret']) ? $_options['api_client_secret'] : null;
		$username          = isset($_options['username']) ? $_options['username'] : null;
		
		if (in_array($channel, array('iconsets', 'collections'))) {
		    $api_path = "users/{$username}/{$channel}";
		}
		else {
		    $api_path = "{$channel}";
		}
		
		$api_url = ICONFINDER_API_URL . 
			"{$api_path}?client_id={$api_client_id}&client_secret={$api_client_secret}" . 
			"&count=" . ICONFINDER_API_MAX_COUNT;
			
		return $api_url;
	}
	
	/**
	 * Apply the custom or default theme to the output
	 * @param <String> $theme - The theme name
	 * @return <Strong> The HTML output
	 */
	private function apply_admin_theme($data, $filename) {
		$output = "";
		
		$admin_file = null;
	    if ($filename != "") {
	        $admin_file = plugin_dir_path( __FILE__ ) . "/partials/$filename";
	    }
	    
	    $items = array();
	    if (isset($data['items'])) {
	        $items = $data['items'];
	    }
	    
	    $items = iconfinder_sort_array($items, 'name', SORT_ASC);
	    
	    $message = 'Nothing to display';
	    if (isset($data['message'])) {
	        $message = $data['message'];
	    }
	    
		ob_start();
		include $admin_file;
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	*  Save the plugin options
	*
	*
	* @since    1.0.0
	*/
	public function options_update() {
		register_setting( $this->plugin_name, $this->plugin_name, array($this, 'validate') );
	}
	
	/**
	 * Performs the cache purge
	 *
	 * @since 1.0.0
	 */
	public function purge_cache() {
	
	    $cache_keys = icf_get_cache_keys();
	    
	    foreach ( $cache_keys as $cache_key ) {
	    
	        delete_option( $cache_key );
	    }
	    
	    update_option( 'icf_cache_keys', array() );

	    wp_redirect( admin_url( 'admin.php?page=iconfinder-portfolio' ) );
	}
    
    /**
	 * Sync local content with source.
	 *
	 * @since 1.1.0
	 */
	public function sync_content() {
	
	    // Sync local content with source

	    wp_redirect( admin_url( 'admin.php?page=iconfinder-portfolio' ) );
	}
	
	/**
	 * Validate all options fields
	 *
	 * @since    1.0.0
	 */
	public function validate($input) {
		$valid = array();
		
		$valid['api_client_id']        = isset($input['api_client_id']) && ! empty($input['api_client_id']) ? $input['api_client_id'] : null;
		$valid['api_client_secret']    = isset($input['api_client_secret']) && ! empty($input['api_client_secret']) ? $input['api_client_secret'] : null;
		$valid['username']             = isset($input['username']) && ! empty($input['username']) ? $input['username'] : null;
        
        $valid['use_custom_post_type'] = isset($input['use_custom_post_type']) && ! empty($input['use_custom_post_type']) ? $input['use_custom_post_type'] : null;
        $valid['show_footer_link']     = isset($input['show_footer_link']) && ! empty($input['show_footer_link']) ? $input['show_footer_link'] : null;
        $valid['link_to_iconfinder']   = isset($input['link_to_iconfinder']) && ! empty($input['link_to_iconfinder']) ? $input['link_to_iconfinder'] : null;

        return $valid;
	}
	
}
