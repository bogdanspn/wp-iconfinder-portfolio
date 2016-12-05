<?php

if ( ! defined( 'ABSPATH' ) ) exit;

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
     * Get the current plugin mode.
     * @returns string Plugin mode
     */
    public function get_mode() {
        $_options = get_option( ICF_PLUGIN_NAME );
        $plugin_mode = get_val($_options, 'plugin_mode', ICF_PLUGIN_MODE_DEFAULT);
        if (empty($plugin_mode)) {
            $plugin_mode = ICF_PLUGIN_MODE_DEFAULT;
        }
        return $plugin_mode;
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
        $iconsets_menu_text = 'Iconsets Shortcodes';
        if ($this->get_mode() == ICF_PLUGIN_MODE_ADVANCED) {
            $iconsets_menu_text = 'Import Manager';
        }
		add_menu_page( 'Iconfinder Portfolio Setup', 'Iconfinder Portfolio', 'manage_options', $this->plugin_name);
        add_submenu_page( $this->plugin_name, $iconsets_menu_text, $iconsets_menu_text, 'manage_options', $this->plugin_name . '-iconsets', array($this, 'display_iconsets_page'));
		add_submenu_page( $this->plugin_name, 'Collections Shortcodes', 'Collections Shortcodes', 'manage_options', $this->plugin_name . '-collections', array($this, 'display_collections_page'));
        add_submenu_page( $this->plugin_name, 'Settings', 'Settings', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
		add_submenu_page( $this->plugin_name, 'Documentation', 'Documentation', 'manage_options', $this->plugin_name . '-documentation', array($this, 'display_plugin_documentation'));
        
        // Only show these in advanced mode after API credentials are set
        
        if ($this->get_mode() == ICF_PLUGIN_MODE_ADVANCED) {
            # add_submenu_page( $this->plugin_name, 'Importer', 'Importer', 'manage_options', $this->plugin_name . '-importer', array($this, 'display_plugin_importer'));
        }
	}

    /**
     * Add settings action link to the plugins page.
     * @param $links
     * @return array
     * @since 1.1.0
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
	
	    echo $this->apply_admin_theme(null, 'iconfinder-portfolio-admin-settings.php');
	}
	
	/**
	 * Render the documentation page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_documentation() {
	
	    $_options = get_option( ICF_PLUGIN_NAME );
		$username = isset($_options['username']) ? $_options['username'] : null;

		$data = null;

	    $response = iconfinder_call_api(
            get_api_url(get_api_path('categories')),
            icf_get_cache_key($username, 'categories')
        );
        
        if (isset($response['categories'])) {
	    	$data['categories'] = $response['categories'];
	    }
	    
	    $response = iconfinder_call_api(
            get_api_url(get_api_path('styles')),
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
	    
	    $_options = get_option( ICF_PLUGIN_NAME );
		$username = isset($_options['username']) ? $_options['username'] : null;
	    
	    $data = array('message' => 'Enter your API credentials on the API Settings page to list your collections here');
	    
	    $response = iconfinder_call_api(
            get_api_url(get_api_path('collections')),
            icf_get_cache_key($username, 'collections')
        );
	    
	    if (isset($response['items'])) {
	    	$data['items'] = $response['items'];
	    }
	    
	    echo $this->apply_admin_theme($data, 'iconfinder-portfolio-admin-collections.php');
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_iconsets_page() {
	
		// Grab the iconsets from the API
		
	    $_options = get_option( ICF_PLUGIN_NAME );
		$username = isset( $_options['username'] ) ? $_options['username'] : null;

		$current_page = icf_get_page_number();

        $more_args = null;
        if ($current_page > 1) {
            $more_args = array(
                'after' => get_transient(ICF_KEY_LAST_ICONSET_ID)
            );
        }
		
		$data = array( 
            'message' => ICF_ENTER_API_CREDENTIALS,
            'items'   => array()
        );

        $response = iconfinder_call_api(
            $this->get_admin_api_url( 'iconsets', $more_args ),
            icf_get_cache_key( $username, 'iconsets' ),
            false
        );

	    if (isset($response['items'])) {
	    	$data['items'] = paginate_items($response['items'], 100, $current_page);
	    }

	    $data['page_count'] = ceil(get_val($response, 'total_count', 100) / 100);
	    $data['current_page'] = $current_page;

	    $offset = count($data['items']);
	    $offset = $offset > 0 ? $offset - 1 : 0 ;
	    if (isset($data['items'][$offset])) {
            set_transient(ICF_KEY_LAST_ICONSET_ID, $data['items'][$offset]['iconset_id']);
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
                $item['is_imported'] = false;

                if ($this->get_mode() == ICF_PLUGIN_MODE_ADVANCED) {
                    $iconset_post = get_post_by_iconset_id($item['iconset_id']);
                    if (is_object($iconset_post) && isset($iconset_post->ID)) {
                        $item['is_imported'] = true;
                        $item['post_id'] = $iconset_post->ID;
                        $item['post_view_link'] = get_the_permalink($iconset_post->ID);
                        $item['post_edit_link'] = admin_url("post.php?post={$iconset_post->ID}&action=edit");
                        $item['latest_sync'] = get_post_meta( $iconset_post->ID, 'latest_sync', true );
                    }
                }
	        }
	    }
		echo $this->apply_admin_theme($data, 'iconfinder-portfolio-admin-iconsets.php');
	}

    /**
     * Determine correct API URl from the shortcode attrs
     * @param $channel
     * @param array $args
     * @return null|string|WP_Error
     */
	private function get_admin_api_url($channel, $args=array()) {

	    if (! is_array($args)) $args = array();
	
		$_options = get_option( ICF_PLUGIN_NAME );
		
        $valid_channels = icf_get_setting('valid_api_channels', array());
		
        $client_id     = get_val($_options, 'api_client_id');
        $client_secret = get_val($_options, 'api_client_secret');
        $username      = get_val($_options, 'username');

        $result = null;
        if (! verify_credentials()) {
            $result = new WP_Error('error', 'No valid API credentials');
        }
		else {
            if (in_array($channel, $valid_channels)) {
                $api_path = "users/{$username}/{$channel}";
            }
            else {
                $api_path = "{$channel}";
            }

            $query_args = array_merge($args, array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'count' => get_val($args, 'count', ICONFINDER_API_MAX_COUNT)
            ));

            $query_string = http_build_query($query_args);

            $result = ICONFINDER_API_URL . "{$api_path}?" . $query_string ;
        }

		return $result;
	}

    /**
     * Apply the custom or default theme to the output
     * @param array $data
     * @param string $filename
     * @return string
     */
	private function apply_admin_theme($data, $filename) {

		$output = "";
		
		$admin_file = null;
	    if ($filename != "") {
	        $admin_file = plugin_dir_path( __FILE__ ) . "partials/$filename";
	    }
	    
	    $items = array();
	    if (isset($data['items'])) {
	        $items = $data['items'];
	    }
	    
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

    public function process_iconset_admin_post() {
        
        $result = null;
        $post_data  = get_val( $_POST, $this->plugin_name, null );
        $action     = strtolower(get_val( $_POST, 'submit', '' ));
        $iconset_id = get_val( $post_data, 'iconset_id', null );
        
        if (empty($iconset_id) || empty($action)) {
            $result = icf_append_error($result, null, "Iconset could not be found");
        }
        else {
            // Do the thing
            if (in_array($action, array('delete', 'update'))) {
                $iconset = get_post_by_iconset_id($iconset_id);
                if (is_wp_error($iconset)) {
                    $result = icf_append_error($result, $iconset);
                }
                else {
                    if ( 'delete' === $action ) {
                        $delete = delete_iconset_post($iconset_id);
                        if (is_wp_error($delete)) {
                            $result = icf_append_error(
                                $result, 
                                $delete,
                                "Iconset ID {$iconset_id} could not be deleted"
                            );
                        }
                        else {
                            icf_queue_notices(
                                "Iconset `{$iconset->post_title}` was successfully deleted.",
                                'success'
                            );
                        }
                    }
                    else if ( 'update' === $action ) {
                        
                        $post_id = update_iconset_post($iconset_id, array());
                        if (is_wp_error($post_id)) {
                            $result = icf_append_error(
                                $result, 
                                $post_id,
                                "Iconset ID {$iconset_id} could not be updated"
                            );
                        }
                        else {
                            icf_queue_notices(
                                "Iconset `{$iconset['identifier']}` was successfully updated.",
                                'success'
                            );
                        }
                    }
                }
            }
            else if ( 'import' === $action ) {
                $post_id = create_iconset_post($iconset_id, array());
                if (is_wp_error($post_id)) {
                    $result = icf_append_error(
                        $result, 
                        $post_id,
                        "Iconset ID {$iconset_id} could not be imported"
                    );
                }
                else {
                    $post = get_post($post_id);
                    icf_queue_notices(
                        "Iconset `{$post->post_title}` was successfully imported.",
                        'success'
                    );
                }
            }
        }
        
        if (is_wp_error($result)) {
            icf_queue_notices(
                $result->get_error_messages(),
                'error'
            );
        }

        wp_redirect( admin_url( "admin.php?page=iconfinder-portfolio-iconsets" . icf_get_page_query() ) );
    }
    
    /**
	 * Sync local content with source.
	 *
	 * @since 1.1.0
	 */
	public function sync_content() {
	
	    // Sync local content with source

	    wp_redirect( admin_url( 'admin.php?page=' . ICF_PLUGIN_NAME ) );
	}
	
    	
	/**
	*  Save the plugin options
	*
	* @since    1.0.0
	*/
	public function options_update() {

		register_setting( ICF_PLUGIN_NAME, ICF_PLUGIN_NAME, array($this, 'validate') );
	}
	
	/**
	 * Performs the cache purge
	 * @param bool $redirect
	 * @since 1.0.0
	 */
	public function purge_cache($redirect=true) {

	    $action = get_val($_REQUEST, 'submit', 'purge-cache');
	    $action = strtolower(str_replace(' ', '-', $action));

	    if ($action == 'refresh-cache') {
            refresh_cache();
        }
        else if ($action == 'clear-cache') {
            purge_cache();
        }

        if ( $redirect === false ) { return; }
	    wp_redirect( admin_url( 'admin.php?page=' . ICF_PLUGIN_NAME ) );
	}

    /**
	 * Validate all options fields
	 * @param array $input
     * @return array
	 * @since 1.0.0
	 */
	public function validate($input) {
		$valid = array();
		
		$valid['api_client_id']       = get_val( $input, 'api_client_id', null );
		$valid['api_client_secret']   = get_val( $input, 'api_client_secret', null );
		$valid['username']            = get_val( $input, 'username', null );
        
        $valid['plugin_mode']         = get_val( $input, 'plugin_mode', 'basic' );
        $valid['use_powered_by_link'] = get_val( $input, 'use_powered_by_link', true );
        $valid['use_purchase_link']   = get_val( $input, 'use_purchase_link', true );
        $valid['posts_per_page']      = get_val( $input, 'posts_per_page', ICF_SEARCH_POSTS_PER_PAGE );
        $valid['currency_symbol']     = get_val( $input, 'currency_symbol', ICF_DEFAULT_CURRENCY );
        $valid['show_price']          = get_val( $input, 'show_price', true );
        
        // We need to have at least one preview size at all times, 
        // so if none are selected, use the default.
        $icon_preview_sizes = get_val( $input, 'icon_preview_sizes');
        if (! is_array($icon_preview_sizes) || empty($icon_preview_sizes)) {
            $icon_preview_sizes = array(icf_get_setting('icon_default_preview_size'));
        }
        $valid['icon_preview_sizes'] = $icon_preview_sizes;
        
        // We need to have an iconset preview size at all times.
        // If none is selected or set, use the default.
        $iconset_preview_size = get_val( $input, 'iconset_preview_size');
        if (empty($iconset_preview_size)) {
            $iconset_preview_size = icf_get_setting('iconset_default_preview_size');
        }
        $valid['iconset_preview_size'] = $iconset_preview_size;
        
        // If we are currently in basic mode, and changing to 
        // advanced mode, clear the cache. We don't want to leave 
        // garbage behind. If the user switches back to basic mode,
        // the plugin will create a new cache on the first 
        // page requests.
        if ($valid['plugin_mode'] === ICF_PLUGIN_MODE_ADVANCED) {
            if (icf_get_option('plugin_mode') === ICF_PLUGIN_MODE_BASIC) {
                $this->purge_cache( false );
            }
        }
        return $valid;
	}
	
}
