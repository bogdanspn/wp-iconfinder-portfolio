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
    
    public function load_search_engine() {
        if (! class_exists('Gee_Search_Plus_Engine')) {
            //TODO: How should this be handled?
            return;
        }
        $gee_search_plus = new Gee_Search_Plus_Engine();
    }
    
    /**
     * Determine correct API URl from the shortcode attrs
     * @param $attrs - The shortcode attrs
     * @since 1.0.0
     */
    public static function get_api_url($attrs) {
    
        $_options = get_option('iconfinder-portfolio');
        
        $api_client_id     = get_val($_options, 'api_client_id');
        $api_client_secret = get_val($_options, 'api_client_secret');
        $username          = get_val($_options, 'username');
        $collection        = get_val($attrs, 'collection');
        $iconset           = get_val($attrs, 'iconset');
        
        $channel = 'iconsets';
        $identifier = null;
        if (! empty($iconset)) {
            $channel = 'icons';
            $identifier = $iconset;
        }
        else if (! empty($collection)) {
            $channel = 'collections';
            $identifier = $collection;
        }
        $api_path = attrs_to_api_path($username, $channel, $identifier);

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
        $item_id             = null;
        $options             = array();

        $_options = get_option('iconfinder-portfolio');
        $username = get_val($_options, 'username');
        
        $attrs = get_shortcode_attrs($attrs, array('username'=>$username));

        $id         = get_val($attrs, 'id');
        $count      = get_val($attrs, 'count', 100);
        $style      = get_val($attrs, 'style');
        $type       = get_val($attrs, 'type');
        $sets       = str_to_array(get_val($attrs, 'sets'));
        $categories = str_to_array(get_val($attrs, 'categories'));
        $theme      = get_val($attrs, 'theme');
        $sort_by    = get_val($attrs, 'sort_by');
        $sort_order = strtoupper(get_val($attrs, 'sort_order')) === "ASC" ? SORT_ASC : SORT_DESC;
        $omit       = str_to_array(get_val($attrs, 'omit'));
        $img_size   = get_val($attrs, 'img_size', 'normal');
        $collection = get_val($attrs, 'collection');
        $show_links = get_val($attrs, 'show_links', true);
        $show_price = get_val($attrs, 'show_price', true);
        $iconset    = get_val($attrs, 'iconset');
        $img_size   = get_val($attrs, 'img_size', 'medium-2x');
        
        // Make sure a few variables are the expect type or value
        if (! in_array($show_links, array('1', '0', 'true', 'false'))) {
            $show_links = 1;
        }
        else if ($show_links == 'true') {
            $show_links = 1;
        }
        else if ($show_links == 'false') {
            $show_links = 0;
        }
        
        if (! in_array($show_price, array('1', '0', 'true', 'false'))) {
            $show_price = 1;
        }
        else if ($show_price == 'true') {
            $show_price = 1;
        }
        else if ($show_price == 'false') {
            $show_price = 0;
        }
        
        $sort_by = $sort_by == "date" ? "published_at" : $sort_by;
        
        $options = array(
            'show_links' => $show_links,
            'show_price' => $show_price
        );
        
        if ($img_size === 'medium') {
            $img_size = 'medium-2x';
        }
        else if (in_array($img_size, array('small', 'normal', 'default'))) {
            $img_size = 'medium';
        }
        else if (! in_array($img_size, $valid_img_sizes)) {
            $img_size = 'medium';
        }
        
        $data = null;
        $identifier = null;
        $channel = 'iconsets';
        
        if (! empty($iconset)) {
            $channel = 'icons';
            $identifier = $iconset;
        }
        else if (! empty($collection)) {
            $channel = 'collections';
            $identifier = $collection;
        }
        
        $cache_key = icf_get_cache_key(
            $username,
            $channel,
            $identifier
        );
        
        try {
            $data = iconfinder_call_api(
                self::get_api_url($attrs), 
                $cache_key
            );
        }      
        catch (Exception $e) {
            icf_queue_notices(
                ICONFINDER_SERVER_ERROR_MSG,
                'error'
            );
            return ICONFINDER_SERVER_ERROR_MSG;
        }  
        
        $content = array(
            'type' => get_val($data, 'data_type', 'iconsets'),
            'items' => array()
        );
        
        if ($content['type'] == 'icons') {
            $icons = scrub_icons_list($data['items']);
            # icf_dump($icons);
            foreach ($icons as $icon) {
                if (in_array($icon['icon_id'], $omit)) continue;
                
                $icon['permalink'] = ICONFINDER_URL . "icons/{$icon['icon_id']}" . (! empty($username) ? "?ref={$username}" : "");
                    
                array_push($content['items'], $icon);
            }
        }
        else if ($content['type'] == 'iconsets') {
            $iconsets = array();
            foreach ($data['items'] as &$iconset) {

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
                $iconsets = $data['items'];
            }

            // The call to array_sort_dep is required for now for compatibility with older versions of PHP
            if (in_array($sort_by, $valid_sort_fields) && in_array($sort_order, $valid_sort_orders)) {
                # $iconsets = self::sort_array($iconsets, $sort_by, $sort_order);
                $iconsets = iconfinder_sort_array($iconsets, $sort_by, $sort_order);
            }
            $content['items'] = $iconsets;
        }
        else {
            # throw new Exception("Unsupported data type");
            icf_queue_notices(
                "Unsupported data type",
                'error'
            );
        }
        
        if ($count > 0) {
            $content['items'] = array_slice($content['items'], 0, $count);
        }
                
        return self::apply_theme($content, $theme, $options);
    }
    
    /**
     * Process the list of icons and format the data array as it is expected
     * by the template.
     * @param array $data
     * 
     * @since 1.1.0
     */
    private static function format_icons_data($data=array()) {
        
        
    }
    
    /**
     * Process the array of icon sets and format the data array as it is
     * expected by the template.
     * @param array $data
     * 
     * @since 1.1.0
     */
    private static function format_iconsets_data($data) {
        
        
    }
    
    /**
     * 
     * @param type $data
     * @param type $styles
     * 
     * @since 1.1.0
     */
    private static function filter_by_styles($data, $styles) {
        
        
    }
    
    /**
     * 
     * @param type $data
     * @param type $categories
     * 
     * @since 1.1.0
     */
    private static function filter_by_categories($data, $categories) {
        
        
    }
    
    /**
     * 
     * @param type $data
     * @param type $categories
     * 
     * @since 1.1.0
     */
    private static function filter_by_license($data, $categories) {
        
        
    }
    
    /**
     * 
     * @param type $data
     * @param type $categories
     * 
     * @since 1.1.0
     */
    private static function filter_by_iconset_id($data, $categories) {
        
        
    }
    
    /**
     * Apply the custom or default theme to the output
     * @param <String> $theme - The theme name
     * @return <Strong> The HTML output
     * 
     * @since 1.0.0
     */
    private static function apply_theme($content, $theme='default', $options=array('show_price'=>1, 'show_links'=>1)) {
        $output = "";
        
        $items = get_val($content, 'items', array());
        
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