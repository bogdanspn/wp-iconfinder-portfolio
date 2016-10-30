<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://iconify.it
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/public
 * @author     Scott Lewis <scott@iconify.it>
 */
class Iconfinder_Portfolio_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->add_shortcode();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/iconfinder-portfolio-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/iconfinder-portfolio-public.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Add the Iconfinder Portfolio shortcode hook
	 * 
	 * @since 1.0.0
	 */
	public function add_shortcode() {

		add_shortcode('iconfinder_portfolio', array( __CLASS__, 'iconfinder_portfolio_shortcode' ));
	}
	
	/**
	 * Render the Iconfinder Portfolio shortcodes
	 * 
	 * @since 1.0.0
	 */
	 public static function iconfinder_portfolio_shortcode( $attrs ) {
	 
	    $iconsets = array();

		$_options = get_option('iconfinder-portfolio');
		
		$user_id           = isset($_options['user_id']) ? $_options['user_id'] : null;
		$client_id         = isset($_options['api_client_id']) ? $_options['api_client_id'] : null;
		$api_client_secret = isset($_options['api_client_secret']) ? $_options['api_client_secret'] : null;
		$username          = isset($_options['username']) ? $_options['username'] : null;
		
		$attrs = shortcode_atts(
			array(
				'userid'  => $user_id,
				'count'   => 20,
				'channel' => 'iconsets',
				'style'   => '',
				'type'    => '',
				'collection' => '',
				'sets' => '',
				'categories' => ''
	    ), $attrs );
	    
	    $id         = $attrs['id'];
	    $channel    = $attrs['channel'];
	    $count      = $attrs['count'];
	    $style      = $attrs['style'];
	    $type       = $attrs['type'];
	    $sets       = ! empty($attrs['sets']) ? explode(',', $attrs['sets']) : array();
	    $categories = ! empty($attrs['categories']) ? explode(',', $attrs['categories']) : array();
	    $collection = $attrs['collection'];
	    
	    $data = json_decode(
		    wp_remote_retrieve_body(
			    wp_remote_get( 
					ICONFINDER_API_URL . "users/{$user_id}/iconsets?client_id={$api_client_id}&client_secret={$api_client_secret}&count=100", 
					array( 'sslverify' => false )
				)
			), 
			true
		);
		
		#TODO: Filter by license type
		#TODO: Filter by collection
		
	    $data = $data['iconsets'];

	    foreach ($data as &$iconset) {
	        $iconset['permalink'] = "http://iconfinder.com/iconsets/{$iconset['identifier']}" . (! empty($username) ? "?ref={$username}" : "");
			$iconset['preview']   = "https://cdn4.iconfinder.com/data/iconsets/previews/medium/{$iconset['identifier']}.png";
			$iconset['price']     = $iconset['prices'][0]['price'];

			if (count($sets)) {
				if (in_array($iconset['iconset_id'], $sets)) {
			    	array_push($iconsets, $iconset);
			    }
			}
		    else {
			    if ($style != "") {
			    	$is_match = false;
			        foreach ($iconset['styles'] as $iconset_style){
			            if ($iconset_style['identifier'] ==  $style) {
			                $is_match = true;
			            }
			        }
			        if (! $is_match) continue;
			    }
			    if ($type != "") {
			    	$is_match = false;
			        $iconset_type = intval($iconset['is_premium']) == 1 ? 'premium' : 'free' ;
			    	if ($iconset_type == $type) {
			    	    $is_match = true;
			    	}
			    	if (! $is_match) continue;
			    }
			    if (count($categories)) {
			    	$is_match = false;
			    	foreach ($iconset['categories'] as $iconset_category){
			            if (in_array($iconset_category['identifier'], $categories)) {
			                $is_match = true;
			            }
			        }
			        if (! $is_match) continue;
			    }
			    array_push($iconsets, $iconset);
		    }
	    }
	    
	    if (! count($iconsets)) {
	        $iconsets = array_slice($data, 0, $count);
	    }
	    
	    # die('<pre>' . print_r($iconsets, true) . '</pre>');

		include plugin_dir_path( __FILE__ ) . '/partials/iconfinder-portfolio-public-display.php';
	}

}