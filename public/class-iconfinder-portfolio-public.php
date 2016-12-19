<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://iconfinder.com
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
 * @author     Iconfinder <support@iconfinder.com>
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
        add_shortcode('iconfinder_search',    array( __CLASS__, 'iconfinder_search_shortcode' ));
        add_shortcode('icons_searchform',     array( __CLASS__, 'shortcode_icons_searchform' ));
        add_shortcode('iconsets_searchform',  array( __CLASS__, 'shortcode_iconsets_searchform' ));
    }

    public function load_search_engine() {
        if (! class_exists('Gee_Search_Plus_Engine')) {
            //TODO: How should this be handled?
            return;
        }
        if (! is_admin()) {
            if (is_search() || is_iconfinder()) {
                new Gee_Search_Plus_Engine();
            }
        }
    }

    /**
     * Determine correct API URl from the shortcode attrs
     * @param array $attrs The shortcode attrs
     * @return string $api_url
     * @since 1.0.0
     */
    public static function get_api_url($attrs) {

        $collection = get_val($attrs, 'collection');
        $iconset    = get_val($attrs, 'iconset');

        $args = array();
        $channel = 'iconsets';

        if (! empty($iconset)) {
            $channel = 'icons';
            $args = array('identifier' => $iconset);
        }
        else if (! empty($collection)) {
            $channel = 'collection';
            $args = array('identifier' => $collection);
        }

        return get_api_url(get_api_path($channel, $args), array('count' => ICONFINDER_API_MAX_COUNT));
    }

    private static function define_iconfinder() {
        if (! defined('IS_ICONFINDER')) {
            define('IS_ICONFINDER', true);
        }
    }

    /**
     * Render the Iconfinder Search shortcodes
     * @param array $attrs
     * @return string
     * @since 1.0.0
     */
    public static function iconfinder_search_shortcode( $attrs ) {

        self::define_iconfinder();

        if (! icf_is_advanced_mode()) {
            return null;
        }

        $content = null;

        $defaults = array(
            'type' => 'icon'
        );

        $attrs = shortcode_atts($defaults, $attrs );

        $search_type = get_val($attrs, 'type');

        if ($search_type === 'iconset') {
            $template = icf_locate_template(
                icf_get_setting( 'iconset_search_shortcode_template' )
            );
        }
        else {
            $template = icf_locate_template(
                icf_get_setting( 'icon_search_shortcode_template' )
            );
        }

        $search_args = array( 's' => get_query_var('s') );

        //TODO: Update this to accept more than one iconset_id
        if ( $search_iconset_id = get_val( $attrs, 'iconset' ) ) {
            $search_args['search_iconset_id'] = $search_iconset_id;
        }

        if (! empty($template)) {
            $content = do_buffer( $template, $search_args );
        }
        return $content;
    }

    /**
     * Conditionally adds the sort clause to the query args
     * based on the prioritization.
     * @param array $options
     * @return array
     * @since 1.1.0
     */
    private static function get_sort_clause($options) {

        $query_args = array();
        $sort_by    = get_val($options, 'sort_by');
        $sort_order = strtoupper(get_val($options, 'sort_order', 'DESC'));

        if (! empty($sort_by)) {
            if ($sort_by === 'iconset_id') {
                $query_args['order_by'] = 'meta_value';
                $query_args['meta_key'] = 'iconset_id';
            }
            else {
                $sort_by = $sort_by == 'name' ? 'title' : $sort_by;
                $query_args['orderby'] = $sort_by;
            }
            $query_args['order'] = $sort_order == 3 ? 'DESC' : 'ASC';
        }
        return $query_args;
    }

    /**
     * Remove the omitted iconsets from the result set.
     * @param array $options
     * @param array $posts
     * @return array
     * @since 1.1.0
     */
    private static function scrub_omitted($options, $posts) {
        $scrubbed = array();
        $omit = str_to_array(get_val($options, 'omit'));
        foreach ($posts as $post) {
            $iconset_id = get_post_meta($post->ID, 'iconset_id', true);
            if (! in_array($iconset_id, $omit)) {
                $scrubbed[] = $post;
            }
        }
        return $scrubbed;
    }

    /**
     * Conditionally adds the taxonomy clause to the query args
     * based on the prioritization.
     * @param array $options
     * @return array
     * @since 1.1.0
     */
    private static function get_taxonomy_clause($options) {

        $query_args = array();
        $style      = get_val($options, 'style');
        $sets       = str_to_array(get_val($options, 'sets'));
        $categories = str_to_array(get_val($options, 'categories'));
        $tags       = str_to_array(get_val($options, 'tags'));
        $type       = get_val($options, 'type');
        $iconset_id = get_val($options, 'iconset');

        $style      = coerce_style_values($style);

        $query_type = null;
        if (! empty($iconset_id)) {
            $query_args['post_type'] = 'iconset';
            $query_args['meta_key'] = 'iconset_id';
            $query_args['meta_value'] = $iconset_id;
            $query_type = 'meta_query';
        }
        else if (! empty($sets)) {
            $query_args['meta_query'] = array(
                array(
                    'key' => 'iconset_id',
                    'value' => $sets,
                    'compare' => 'IN',
                )
            );
            $query_type = 'meta_query';
        }
        // Add categories query
        else if (! empty($categories)) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'icon_category',
                    'field' => 'slug',
                    'terms' => $categories,
                    'operator' => 'IN',
                )
            );
            $query_type = 'tax_query';
        }
        // Add tags query
        else if (! empty($tags)) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'icon_tag',
                    'field' => 'slug',
                    'terms' => $tags,
                    'operator' => 'IN',
                )
            );
            $query_type = 'tax_query';
        }
        // Add the style query
        else if (! empty($style)) {
            $query_args['meta_query'] = array(array(
                'key' => 'iconset_style_identifier',
                'value' => $style,
                'compare' => 'IN'
            ));
            $query_type = 'meta_query';
        }

        if (! empty($type)) {
            $added_query = array(
                'key' => 'is_premium',
                'value' => '1'
            );
            if ($type === 'free') {
                $added_query['compare'] = 'NOT IN';
            }
            if ($query_type === 'tax_query') {
                $query_args['meta_query'] = $added_query;
            }
            else {
                if (! isset($query_args['meta_query'])) {
                    $query_args['meta_query'] = array($added_query);
                }
                else {
                    $query_args['meta_query'][] = $added_query;
                }
            }
        }
        return $query_args;
    }

    /**
     * Executes the shortcode logic in advanced mode.
     * @global \WP_Query $wp_query
     * @param array $attrs
     * @return string
     * @since 1.1.0
     */
    public static function iconfinder_portfolio_shorcode_advanced( $attrs ) {
        global $wp_query;

        self::define_iconfinder();

        $content  = null;
        $theme    = null;
        $options  = get_shortcode_attrs($attrs);

        // The $options array is merged with the system default 
        // settings so we check the $attrs array first for the 
        // `count` to see if the shortcode passed a value.

        $count = get_val(
            $attrs,
            'count',
            icf_get_option(
                'search_posts_per_page',
                ICF_SEARCH_POSTS_PER_PAGE
            )
        );

        $theme = get_val($options, 'theme');
        $theme_args = array(
            'img_size'   => get_val($options, 'img_size', null),
            'show_price' => get_val($options, 'show_price', true),
            'show_links' => get_val($options, 'show_links', true),
            'paginate'   => is_true(get_val($options, 'paginate', false))
        );

        icf_set_theme_vars($theme_args);

        $query_args = array(
            'post_type' => 'iconset',
            'posts_per_page' => $count
        );

        $query_args = array_merge($query_args, self::get_sort_clause($options));

        // Sets take top priority

        $query_args = array_merge($query_args, self::get_taxonomy_clause($options));

        // Retrieve and massage our posts.

        $scrubbed = self::scrub_omitted(
            $attrs,
            icf_setup_posts(
                query_posts($query_args)
            )
        );

        $post_count            = count($scrubbed);
        $wp_query->posts       = $scrubbed;
        $wp_query->post        = $post_count ? $scrubbed[0] : null ;
        $wp_query->found_posts = $post_count;
        $wp_query->post_count  = $post_count;

        $post_type = get_val($query_args, 'post_type', 'iconset');

        /**
         * The theme hierarchy is:
         *
         * - /wp-content/theme/{theme-name}/{shortcode-theme-param}-shortcode-{post_type}.php
         * - /wp-content/plugins/iconfinder-portfolio/public/partials/{shortcode-theme-param}-shortcode-{post_type}.php
         * - /wp-content/theme/{theme-name}/shortcode-{post_type}.php
         * - /wp-content/plugins/iconfinder-portfolio/public/partials/shortcode-{post_type}.php
         */

        $template = null;

        /**
         * Try the templates specified by the shortcode.
         */

        if (! empty($theme)) {
            $template = icf_locate_template("{$theme}-shortcode-{$post_type}.php", true);
        }

        /**
         * If the shortcode did not specify a template, try the theme directory.
         */
        if (empty($template)) {
            $template = icf_locate_template("shortcode-{$post_type}.php", true);
        }

        if (! empty($template)) {
            $content = do_buffer($template, $theme_args);
            wp_reset_query();
            return $content;
        }
        return null;
    }

    /**
     * Outputs the shortcode for the icon search form
     * @return void
     */
    public static function shortcode_icons_searchform( $attrs=array() ) {

        icon_searchform( $attrs );
    }

    /**
     * Outputs the shortcode for the iconset search form
     * @return void
     */
    public static function shortcode_iconsets_searchform( $attrs=array() ) {

        iconset_searchform( $attrs );
    }

    /**
     * Render the Iconfinder Portfolio shortcodes
     * @param array $attrs
     * @return string html output
     * @since 1.0.0
     */
    public static function iconfinder_portfolio_shortcode( $attrs ) {

        //TODO: Yikes! Split this long-assed function into shorter functions.

        self::define_iconfinder();

        // If the plugin is in advanced mode, we use a completely different approach.

        if (icf_is_advanced_mode() && get_val($attrs, 'mode') != 'simple') {
            return self::iconfinder_portfolio_shorcode_advanced($attrs);
        }

        //$iconsets            = array();
        $valid_sort_fields   = array('published_at', 'identifier', 'name', 'iconset_id', 'title');
        $valid_sort_orders   = array(SORT_ASC, SORT_DESC);
        $valid_license_types = array(ICONFINDER_TYPE_PREMIUM, ICONFINDER_TYPE_FREE);
        //$options             = array();

        $_options = get_option(ICF_PLUGIN_NAME);
        $username = get_val($_options, 'username');

        $attrs = get_shortcode_attrs($attrs);

        $count      = get_val($attrs, 'count', ICONFINDER_API_MAX_COUNT);
        $style      = get_val($attrs, 'style');
        $type       = get_val($attrs, 'type');
        $sets       = str_to_array(get_val($attrs, 'sets'));
        $categories = str_to_array(get_val($attrs, 'categories'));
        $theme      = get_val($attrs, 'theme');
        $sort_by    = get_val($attrs, 'sort_by');
        $sort_order = strtoupper(get_val($attrs, 'sort_order')) === 'ASC' ? SORT_ASC : SORT_DESC;
        $omit       = str_to_array(get_val($attrs, 'omit'));
        $collection = get_val($attrs, 'collection');
        // $iconset    = get_val($attrs, 'iconset');

        // Make sure a few variables are the expect type or value

        $sort_by    = $sort_by == 'date' ? 'published_at' : $sort_by;
        $sort_by    = $sort_by == 'title' ? 'name' : $sort_by;

        $options = array(
            'show_links' => intval(get_val($attrs, 'show_links', true)),
            'show_price' => intval(get_val($attrs, 'show_price', true))
        );

        $img_size = coerce_img_size(get_val($attrs, 'img_size', 'normal'));

        /**
         * 1. Determine the data type & path
         *    a. iconset > collection > sets
         *    b. categories
         *    c. styles
         *    d. type
         *    e. omit
         * 2. Check for cached data
         * 3. If no cached data, retrieve the data
         * 4. Filter the data as needed
         * 5. Save the data to cache
         */

        $channel = 'iconsets';
        $identifier = null;

        if (! empty($collection)) {
            $channel = 'collection';
            $identifier = $collection;
        }

        $api_path = get_api_path($channel, array('identifier' => $identifier));

        $cache_key = get_api_cache_key($api_path);

        $data = icf_get_cache($cache_key);

        if (empty($data)) {
            // If the channel is not collection or iconset, we need all iconsets.

            if ($channel === 'iconsets') {
                $data = get_all_iconsets();
            }
            else {
                try {
                    $data = iconfinder_call_api(
                        get_api_url($api_path),
                        $cache_key
                    );
                    icf_update_cache( $cache_key, $data );
                }
                catch (Exception $e) {
                    $data = icf_get_cache($cache_key);
                    if (has_api_data($data)) {
                        $data['from_cache'] = 1;
                    }
                    else {
                        icf_queue_notices(
                            ICONFINDER_SERVER_ERROR_MSG,
                            'error'
                        );
                        return ICONFINDER_SERVER_ERROR_MSG;
                    }
                }
            }
        }

        $content = array(
            'type' => get_val($data, 'data_type', 'iconsets'),
            'items' => array()
        );

        if ($content['type'] == 'icons') {
            $icons = scrub_icons_list($data['items']);
            foreach ($icons as $icon) {
                if (in_array($icon['icon_id'], $omit)) continue;

                $icon['permalink']  = ICONFINDER_URL;
                $icon['permalink'] .= "icons/{$icon['icon_id']}";
                $icon['permalink'] .= (! empty($username) ? "?ref={$username}" : "");

                array_push($content['items'], $icon);
            }
        }
        else if ($content['type'] == 'iconsets') {

            $iconsets = array();

            if (isset($data['items'])) {

                // If the shortcode lists specific iconset_ids, they take precendence
                // over any other content indicators. We filter outside of the main
                // loop because there is no need iterating over the entire data set
                // if we're only looking for a few items.

                if (! empty($sets)) {
                    $filtered = filter_by_iconsets($data['items'], $sets);
                    $data['items'] = $filtered;
                }

                if (isset($data['items']) && is_array($data['items'])) {
                    foreach ($data['items'] as &$iconset) {

                        if (in_array($iconset['iconset_id'], $omit)) continue;

                        $iconset['permalink']  = ICONFINDER_URL;
                        $iconset['permalink'] .= "iconsets/{$iconset['identifier']}";
                        $iconset['permalink'] .=  (! empty($username) ? "?ref={$username}" : "");

                        $iconset['preview'] = get_iconfinder_preview_url($img_size, $iconset['identifier']);

                        // Rather than check the existance of each level of the array,
                        // just suppress any errors and move on

                        $iconset['price'] = @intval($iconset['prices'][0]['price']);

                        // Filter by iconset_ids

                        // Filter by style

                        if (empty($sets)) {
                            if (! empty($style)) {
                                $is_match = in_array(
                                    $style,
                                    array_column($iconset['styles'], 'identifier')
                                );
                                if (! $is_match) { continue; }
                            }

                            // Filter by license type

                            if (in_array($type, $valid_license_types)) {
                                $iconset_type = is_true($iconset['is_premium'])
                                    ? ICONFINDER_TYPE_PREMIUM
                                    : ICONFINDER_TYPE_FREE ;

                                if ($iconset_type !== $type) { continue; }
                            }

                            // Filter by categories

                            if (count($categories)) {
                                $iconset_categories = array_column($iconset['categories'], 'identifier');
                                if (! is_array($iconset_categories) || ! is_array($categories)) {
                                    continue;
                                }
                                $is_match = count(array_intersect(
                                    $iconset_categories, $categories
                                ));
                                if (! $is_match) { continue; }
                            }
                            array_push($iconsets, $iconset);
                        }
                    }
                }

                if (! count($iconsets)) {
                    $iconsets = $data['items'];
                }
            }

            if (in_array($sort_by, $valid_sort_fields) && in_array($sort_order, $valid_sort_orders)) {
                $iconsets = iconfinder_sort_array($iconsets, $sort_by, $sort_order);
            }
            $content['items'] = $iconsets;
        }
        else {
            icf_queue_notices(
                "Unsupported data type",
                'error'
            );
        }

        if ($count > 0) {
            $content['items'] = array_slice($content['items'], 0, $count);
        }

        return self::theme($content, $theme, $options);
    }

    /**
     * Apply the custom or default theme to the output
     * @param string $theme - The theme name
     * @return string The HTML output
     *
     * @since 1.0.0
     */
    private static function theme($content, $theme='default', $options=array('show_price'=>1, 'show_links'=>1)) {
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