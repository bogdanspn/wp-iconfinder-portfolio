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
        wp_enqueue_style( 'owl', plugin_dir_url( __FILE__ ) . 'css/owl.carousel.css', array(), 2.0, 'all' );
        wp_enqueue_style( 'owl-theme', plugin_dir_url( __FILE__ ) . 'css/owl.carousel.theme.css', array(), 2.0, 'all' );
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
        wp_enqueue_script( 'owl', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.js', array( 'jquery' ), 2.0, false );

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
     * Determine correct API URl from the shortcode attrs
     * @param $attrs - The shortcode attrs
     * @since 1.0.0
     */
    private static function get_api_url($attrs) {
    
        $_options = get_option('iconfinder-portfolio');
        
        $api_client_id     = isset($_options['api_client_id']) ? $_options['api_client_id'] : null;
        $api_client_secret = isset($_options['api_client_secret']) ? $_options['api_client_secret'] : null;
        $username          = isset($_options['username']) ? $_options['username'] : null;
        $collection        = isset($attrs['collection']) ? $attrs['collection'] : null ;
        $api_path          = attrs_to_api_path($username, $collection);

        $api_url = ICONFINDER_API_URL . 
            "{$api_path}?client_id={$api_client_id}&client_secret={$api_client_secret}" . 
            "&count=" . ICONFINDER_API_MAX_COUNT;
            
        return $api_url;
    }
    
    /**
     * Render the Iconfinder Portfolio shortcodes
     * 
     * @since 1.0.0
     */
     public static function iconfinder_portfolio_shortcode( $attrs ) {
     
        $iconsets            = array();
        $valid_sort_fields   = array('published_at', 'identifier', 'name', 'iconset_id');
        $valid_sort_orders   = array(SORT_ASC, SORT_DESC);
        $valid_license_types = array(ICONFINDER_TYPE_PREMIUM, ICONFINDER_TYPE_FREE);
        $valid_img_sizes     = array('normal', 'large');

        $_options = get_option('iconfinder-portfolio');
        
        $username = isset($_options['username']) ? $_options['username'] : null;
        
        $attrs = shortcode_atts(
            array(
                'username'   => $username,
                'count'      => 0,
                'style'      => '',
                'type'       => '',
                'collection' => '',
                'sets'       => '',
                'categories' => '',
                'theme'      => '',
                'sort_by'    => '',
                'sort_order' => SORT_DESC,
                'omit'       => '',
                'img_size'   => 'large'
        ), $attrs );
        
        // Test
        array_map('strtolower', $attrs);
        
        $id         = $attrs['id'];
        $count      = $attrs['count'];
        $style      = $attrs['style'];
        $type       = $attrs['type'];
        $sets       = ! empty($attrs['sets']) ? explode(',', $attrs['sets']) : array();
        $categories = ! empty($attrs['categories']) ? explode(',', $attrs['categories']) : array();
        $theme      = $attrs['theme'];
        $sort_by    = $attrs['sort_by'];
        $sort_order = strtoupper($attrs['sort_order']) == "ASC" ? SORT_ASC : SORT_DESC;
        $omit       = ! empty($attrs['omit']) ? explode(',', $attrs['omit']) : array();
        $img_size   = $attrs['img_size'];
        $collection = $attrs['collection'];
        $categories = array_map('trim', $categories);
        $sets       = array_map('trim', $sets);
        $omit       = array_map('trim', $omit);
        
        if (! in_array($img_size, $valid_img_sizes)) {
            $img_size = 'normal';
        }
        
        $img_size = $img_size == 'normal' ? 'medium' : 'medium-2x';
        
        /*
        Coerce-user-friendly values to DB field names. This is just a nicety to make the shortcode values 
        easier to remember and to type.
        */
        
        $sort_by = $sort_by == "date" ? "published_at" : $sort_by;
        
        $data = null;
        
        $item_id = '';
        $channel = 'iconsets';
        if (trim($collection) != '') {
            $channel = 'collections';
            $item_id = $collection;
        }
        
        $cache_key = icf_get_cache_key(
            $username,
            $channel,
            $item_id
        );
        
        try {
            $data = iconfinder_call_api(
                self::get_api_url($attrs), 
                $cache_key
            );
        }      
        catch (Exception $e) {
            return ICONFINDER_SERVER_ERROR_MSG;
        }  
        
        $raw = $data['items'];
        
        foreach ($raw as &$iconset) {
        
            if (in_array($iconset['iconset_id'], $omit)) continue;
        
            $iconset['permalink'] = ICONFINDER_URL . "iconsets/{$iconset['identifier']}" . (! empty($username) ? "?ref={$username}" : "");
            $iconset['preview']   = ICONFINDER_CDN_URL . "data/iconsets/previews/{$img_size}/{$iconset['identifier']}.png";
            
            // Rather than check the existance of each level of the array, 
            // just trap any possible exceptions/null values and move on
            $iconset['price'] = null;
            try {
                $iconset['price'] = $iconset['prices'][0]['price'];
            }
            catch(Exception $e) {/*Exit Gracefully*/}
            
            if (count($sets)) {
                if (in_array($iconset['iconset_id'], $sets)) {
                    array_push($iconsets, $iconset);
                }
            }
            else {
                // Filter by style
                if ($style != "") {
                    $is_match = false;
                    foreach ($iconset['styles'] as $iconset_style){
                        if ($iconset_style['identifier'] ==  $style) {
                            $is_match = true;
                        }
                    }
                    if (! $is_match) continue;
                }
                // Filter by license type
                if (in_array($type, $valid_license_types)) {
                    $is_match = false;
                    $iconset_type = intval($iconset['is_premium']) == 1 ? ICONFINDER_TYPE_PREMIUM : ICONFINDER_TYPE_FREE ;
                    if ($iconset_type == $type) {
                        $is_match = true;
                    }
                    if (! $is_match) continue;
                }
                // Filter by categories
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
            $iconsets = $raw;
        }
        
        // The call to array_sort_dep is required for now for compatibility with older versions of PHP
        if (in_array($sort_by, $valid_sort_fields) && in_array($sort_order, $valid_sort_orders)) {
            # $iconsets = self::sort_array($iconsets, $sort_by, $sort_order);
            $iconsets = iconfinder_sort_array($iconsets, $sort_by, $sort_order);
        }
        
        if ($count > 0) {
            $iconsets = array_slice($iconsets, 0, $count);
        }
                
        return self::apply_theme($iconsets, $theme);
    }
    
    /**
     * Apply the custom or default theme to the output
     * @param <String> $theme - The theme name
     * @return <Strong> The HTML output
     */
    private static function apply_theme($items, $theme='default') {
        $output = "";
        
        $theme_file = null;
        if ($theme != "") {
            $theme_file = plugin_dir_path( __FILE__ ) . "/partials/theme-{$theme}.php";
        }
        if ($theme == 'default' || $theme_file == null || ! file_exists($theme_file)) {
            $theme_file = plugin_dir_path( __FILE__ ) . '/partials/iconfinder-portfolio-public-display.php';
        }
        
        ob_start();
        include $theme_file;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}