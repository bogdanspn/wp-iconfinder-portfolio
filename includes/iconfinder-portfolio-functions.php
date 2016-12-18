<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * This is a collection of utility functions used globally throughout the plugin.
 *
 * @link       http://iconfinder.com
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/includes
 */

/**
 * Global utility functions.
 *
 * @since      1.0.0
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/includes
 * @author     Iconfinder <support@iconfinder.com>
 */ 

function iconfinder_footer_link() {
    $_options = get_option('iconfinder-portfolio');
    $username = get_val($_options, 'username');
    $use_link = get_val($_options, 'use_powered_by_link');
    if (is_true($use_link)) {
        $ref = "";
        if (! empty($username)) {
            $ref = "?ref={$username}";
        }
        echo "<p class=\"powered-by-iconfinder\"><a href=\"https://iconfinder.com/{$ref}\">Powered by Iconfinder</a></p>";
    }
 } 
add_action( 'wp_footer', 'iconfinder_footer_link', 1 );

/**
 * Grab only the data we need from the API response
 * 
 * @since 1.1.0
 * 
 * @param array $raw
 * @return array
 */
function scrub_icons_list($raw) {
    $icons = array();
    foreach ($raw as $item) {
        $icon = array(
            'icon_id'      => get_val($item, 'icon_id'),
            'tags'         => get_val($item, 'tags', array()),
            'style'        => array(),
            'previews'     => array(),
            'is_premium'   => get_val($item, 'is_premium', false),
            'published_at' => get_val($item, 'published_at', date('Y-m-d H:i:s')),
            'type'         => get_val($item, 'type'),
            'price'        => null,
            'categories'   => array()
        );
        $styles = get_val($item, 'styles', array());
        if (count($styles) && isset($styles[0]['identifier'])) {
            $icon['style'] = $styles[0]['identifier'];
        }
        $categories = get_val($item, 'categories', array());
        foreach ($categories as $category) {
            array_push($icon['categories'], $category['identifier']);
        }
        $prices = get_val($item, 'prices', array());
        if (count($prices)) {
            $icon['price'] = $prices[0];
        }
        $rasters = get_val($item, 'raster_sizes', array());
        foreach ($rasters as $raster) {
            $preview_url = null;
            if (isset($raster['formats']) && count($raster['formats'])) {
                $format = $raster['formats'][0];
                if (isset($format['preview_url'])) {
                    $preview_url = $format['preview_url'];
                }
            }
            $icon['previews']['@'.$raster['size']] = array(
                'width'  => $raster['size_width'],
                'height' => $raster['size_height'],
                'src'    => $preview_url,
                'size'   => $raster['size']
            );
        }
        array_push($icons, $icon);
    }
    return $icons;
}
    
/**
 * Merge shortcode attrs with default attrs array
 * @param array $attrs
 * @return array
 * 
 * @since 1.1.0
 */
function get_shortcode_attrs($attrs) {

    return array_map(
        'strtolower',
        shortcode_atts(icf_get_setting('shortcode_defaults'), $attrs )
    );
}

/**
 * Returns the requested value or default if empty
 * @param mixed $subject
 * @param string $key
 * @param mixed $default
 * @return mixed
 * 
 * @since 1.1.0
 */
function get_val($subject, $key, $default=null) {
    $value = $default;
    if (is_array($subject)) {
        if (isset($subject[$key])) {
            $value = $subject[$key];
        }
    }
    else if (is_object($subject)) {
        if (isset($subject->$key)) {
            $value = $subject->$key;
        }
    }
    else if (! empty($subject)) {
        $value = $subject;
    }
    return $value;
}

/**
 * Returns a strictly typed and scrubbed string or number from an array or object.
 * @param array|object $subject The array or object to search
 * @param string $key The key within the object or array to get
 * @param string $type
 * @param |null $default
 * @return string|number|null
 */
function get_scrubbed($subject, $key, $type='string', $default=null) {
    $scrubbed = null;
    $value = get_val($subject, $key, $default);
    if ($type == 'string') {
        if (is_string($value) && strlen($value) < pow(2, 16) ) {
            $scrubbed = sanitize_text_field($value);
        }
    }
    else if ($type == 'number') {
        if (is_numeric($value)) {
            if (is_float($value)) {
                $scrubbed = floatval($value);
            }
            else {
                $scrubbed = intval($value);
            }
        }
    }
    return $scrubbed;
}

/**
 * Converts a shortcode comma-delimited list into a PHP array
 * 
 * @param string $str
 * @param string $delim The delimiter to use
 * @return array 
 * 
 * @since 1.1.0
 */
function str_to_array($str, $delim=',') {
    $value = array();
    if (! empty($str)) {
        $value = explode($delim, $str);
    }
    array_map('trim', $value);
    return $value;
}

/**
 * Determines api path from shortcode attrs
 * @param string|null $username
 * @param string $channel
 * @param string|null $identifier
 * @return string
 */
function attrs_to_api_path($username=null, $channel='iconsets', $identifier=null) {

    $api_path = "users/{$username}/iconsets";
    
    $channel = strtolower($channel);
    
    if ($channel === 'collections') {
        $api_path = "collections/{$identifier}/iconsets";
    }
    else if ($channel === 'icons') {
        $api_path = "iconsets/{$identifier}/icons";
    }
    return $api_path;
}

/**
 * Get the appropriate cache key for the API call. These need to be 
 * standardized throughout the plugin so we have a single function
 * to created them based on the specific call. We use the username 
 * in the key to insure uniqueness.
 *
 * Possible values:
 *
 *    - icf_{username}_iconsets
 *    - icf_{username}_iconsets_{iconset_id} // Not yet implemented
 *    - icf_{username}_collections
 *    - icf_{username}_collections_{collection_id}
 *    - icf_{username}_categories
 *    - icf_{username}_styles
 *
 * @param string $username the api username
 * @param string $channel The channel name
 * @param string $item_id The item identifier
 * @return string
 *
 * @since 1.0.0
 */
function icf_get_cache_key($username='', $channel='', $item_id='') {

    $cache_key = "icf_{$username}_{$channel}";
    
    if ($item_id != '') {
        $cache_key .= "_{$item_id}";
    }
    
    return $cache_key;
}

function get_api_cache_key($api_path) {
    return implode('_', explode('/', $api_path));
}

/**
 * Get all stored cache keys
 *
 * @since 1.0.0
 */
function icf_get_cache_keys() {
        
    return get_option( 'icf_cache_keys', array() );
}

/**
 * Update the registry of stored cache keys
 *
 * In order to avoid doing a bunch of lookups to determine which stored options
 * belong to us, we keep a registry each time a new item is cached. When we
 * need to clear the cache, we can grab the keys then loop through them and
 * clear each one.
 *
 * @param string $new_key
 * @return bool
 */
function icf_update_cache_keys($new_key) {

    $cache_keys = icf_get_cache_keys();
    $saved = false;

    if (! empty($new_key)) {
        if ( ! in_array( $new_key, $cache_keys ) )  {
            array_push( $cache_keys, $new_key );
            $saved = update_option( 'icf_cache_keys', $cache_keys, 'no' );
        }
    }
    return $saved;
}

/**
 * Checks to see if an API response has any icons or iconsets
 * @param $response
 * @return bool
 */
function has_api_data($response) {
    if (empty($response)) { return false;  }
    if (! isset($response['iconsets']) && ! isset($response['items'])) {
        return false;
    }
    if (empty($response['iconsets']) && empty($response['items'])) {
        return false;
    }
    return true;
}

/**
 * Purge the api cache.
 */
function purge_cache() {

    $cache_keys = icf_get_cache_keys();

    if (is_array($cache_keys)) {
        foreach ( $cache_keys as $cache_key ) {
            delete_option( $cache_key );
        }
        update_option( 'icf_cache_keys', array() );
    }
}

/**
 * Refresh the iconsets cache
 */
function refresh_cache() {
    purge_cache();
    $api_path = get_api_path('iconsets');
    icf_update_cache(get_api_cache_key($api_path), get_all_iconsets());
}

/**
 * Gets the stored api credentials. We use a static var so we
 * don't need to hit the DB every time we need them during a process.
 * @return array
 */
function get_api_credentials() {
    static $auth;
    if (null === $auth) {
        $options = get_option( ICF_PLUGIN_NAME );
        $auth = array(
            'api_client_id'     => get_val($options, 'api_client_id'),
            'api_client_secret' => get_val($options, 'api_client_secret'),
            'username'          => get_val($options, 'username')
        );
    }
    return $auth;
}

/**
 * Verifies that we have valid api credentials. We use a static var so we
 * don't need to hit the DB every time we need them during a process.
 * @param null $credentials
 * @return bool
 */
function verify_credentials($credentials=null) {
    if (empty($credentials)) {
        $credentials = get_api_credentials();
    }
    if (empty(get_val($credentials, 'api_client_id'))) {
        return false;
    }
    if (empty(get_val($credentials, 'api_client_secret'))) {
        return false;
    }
    if (empty(get_val($credentials, 'username'))) {
        return false;
    }
    return true;
}

/**
 * Gets the stored api username. We use a static var so we
 * don't need to hit the DB every time we need them during a process.
 * @return string
 */
function api_username() {
    static $username;
    if (null === $username) {
        $auth = get_api_credentials();
        $username = get_val($auth, 'username');
    }
    return $username;
}

/**
 * Gets the stored api client ID. We use a static var so we
 * don't need to hit the DB every time we need them during a process.
 * @return string
 */
function api_client_id() {
    static $client_id;
    if (null === $client_id) {
        $auth = get_api_credentials();
        $client_id = get_val($auth, 'api_client_id');
    }
    return $client_id;
}

/**
 * Gets the stored api client secret. We use a static var so we
 * don't need to hit the DB every time we need them during a process.
 * @return string
 */
function api_client_secret() {
    static $client_secret;
    if (null === $client_secret) {
        $auth = get_api_credentials();
        $client_secret = get_val($auth, 'api_client_secret');
    }
    return $client_secret;
}

/**
 * We don't want to have to build the path every time we
 * need to make an api call so let's just create a helper.
 *
 * @example
 *
 *      $path = get_api_path('icons', array('identifier' => 'dog-activities'));
 *      result: https://api.iconfinder.com/v2/iconsets/dog-activities/icons
 *
 * @param $which
 * @param array $args
 * @return string
 */
function get_api_path($which, $args=array()) {

    if (! is_array($args)) $args = array();

    $username = get_val( $args, 'username', api_username() );

    $path = array( $which );
    if ($which === 'iconsets') {
        /**
         * https://api.iconfinder.com/v2/users/iconify/iconsets
         */
        $path = array('users', $username, 'iconsets');
    }
    else if ($which === 'collections') {
        /**
         * https://api.iconfinder.com/v2/users/iconify/collections
         */
        $path = array('users', $username, 'collections');
    }
    else if ($which === 'collection') {
        /**
         * https://api.iconfinder.com/v2/collections/$identifier/iconsets
         */
        $identifier = get_val($args, 'identifier');
        $path = array('collections', $identifier, 'iconsets');
    }
    else if ($which === 'icons') {
        /**
         * https://api.iconfinder.com/v2/iconsets/dog-activities-extended-license/icons
         */
        $identifier = get_val($args, 'identifier');
        $path = array( 'iconsets', $identifier, 'icons' );
    }
    return implode('/', $path);
}

/**
 * Get the full api url for an api call. You must  pass the path for the REST request,
 * as well as any additional arguments such as 'count' and 'after' to the request.
 *
 * @example
 *
 *     $path = get_api_path('icons', array('identifier' => 'dog-activities'));
 *     $url  = get_api_url($path, array('after' => '2352', 'count' => 20));
 *
 * @see get_api_path
 * @see https://developer.iconfinder.com/
 *
 * @param array $path
 * @param array $query_args
 * @return null|string|WP_Error
 */
function get_api_url($path, $query_args=array()) {

    $result = null;

    if (! is_array($query_args)) $query_args = array();

    $auth = get_api_credentials();

    if (! verify_credentials($auth)) {
        $result = new WP_Error('error', 'No valid API credentials');
    }
    else {

        $query_args = array_merge(array(
            'client_id'     => get_val($auth, 'api_client_id'),
            'client_secret' => get_val($auth, 'api_client_secret'),
            'count'         => get_val($query_args, 'count', ICONFINDER_API_MAX_COUNT)
        ), $query_args);

        $query_string = "?" . http_build_query($query_args);

        $result = ICONFINDER_API_URL . $path . $query_string ;
    }
    return $result;
}


/**
 * Get the icons for an iconset from an API call.
 * @param string $identifier
 * @return array
 *
 * @example https://api.iconfinder.com/v2/iconsets/dog-activities/icons
 *
 * @since 1.1.0
 */
function get_icons_from_api($identifier) {

    $result = null;

    $path = get_api_path('icons', array('identifier' => $identifier));

    $data = iconfinder_call_api(get_api_url(
        $path, array()
    ));

    if (is_array($data) && isset($data['items'])) {
        $result = scrub_icons_list($data['items']);
    }
    else {
        $result = new WP_Error('error', "Iconfinder API says - Icons for {$identifier} Not Found");
    }
    return $result;
}

/**
 * Makes the api call.
 * @param $api_url The url to which to make the call
 * @param string $cache_key A unique key matching the call path for caching the results
 * @param bool $from_cache Whether or not to pull requests from the cache first
 * @return array|mixed|null|object
 * @throws Exception
 */
function iconfinder_call_api( $api_url, $cache_key = '', $from_cache = false ) {

    // Always try the local cache first. If we get a hit, just return the stored data.

    $response = null;

    if ( $from_cache ) {
        $response = icf_get_cache( $cache_key );
    }
    
    // If there is no cached data, make the API cale.
    
    if ( empty($response) || ! $from_cache ) {
        try {
            $response = json_decode(
                wp_remote_retrieve_body(
                    wp_remote_get(
                        $api_url,
                        array('sslverify' => ICONFINDER_API_SSLVERIFY)
                    )
                ),
                true
            );

            if (isset($response['code'])) {
                throw new Exception("[{$response['code']}] - {$response['message']}");
            }
            else if (isset($response['detail'])) {
                throw new Exception("[Exception] - {$response['detail']}");
            }

            /**
             * a bit kludgy, but I want to normalize the response fields here
             * instead of having a bunch of conditional checks elsewhere.
             */
            if (isset($response['iconsets']) && ! isset($response['items'])) {
                $response['items'] = $response['iconsets'];
                $response['data_type'] = 'iconsets';
                unset($response['iconsets']);
            }
            else if (isset($response['icons']) && ! isset($response['items'])) {
                $response['items'] = $response['icons'];
                $response['data_type'] = 'icons';
                unset($response['icons']);
            }

            $response['from_cache'] = 0;

            icf_update_cache($cache_key, $response);

            if (trim($cache_key) != '') {
                if ( update_option( $cache_key, $response ) ) {
                    $stored_keys = get_option( 'icf_cache_keys', array() );
                    if ( ! in_array( $cache_key, $stored_keys ) )  {
                        array_push( $stored_keys, $cache_key );
                        update_option('icf_cache_keys', $stored_keys, 'no');
                    }
                }
            }
        }
        catch(Exception $e) {
            throw new Exception($e);
        }
    }
    
    if ($response == null && trim($cache_key) != '') {
        $response = get_option( $cache_key );
    }
    
    return $response;
}

/**
 * @param $cache_key
 * @return mixed
 */
function icf_get_cache($cache_key) {
    $response = get_option($cache_key);
    if (has_api_data($response)) {
        $response['from_cache'] = 1;
        return $response;
    }
    return $response;
}

/**
 * @param $cache_key
 * @param $data
 */
function icf_update_cache($cache_key, $data) {
    if (trim($cache_key) != '') {
        if (update_option($cache_key, $data)) {
            $stored_keys = get_option('icf_cache_keys', array());
            if (!in_array($cache_key, $stored_keys)) {
                array_push($stored_keys, $cache_key);
                update_option('icf_cache_keys', $stored_keys, 'no');
            }
        }
    }
}

/**
 * Sort a keyed array by a particular field.
 * @param array $array
 * @param string $sort_by
 * @param string $sort_order
 * @return array
 * 
 * @since 1.0.0
 */
function iconfinder_sort_array($array, $sort_by, $sort_order) {
        
    $sort_array = array();
    
    foreach($array as $key => $val) {
        $sort_array[$key] = $val[$sort_by];
    }
    array_multisort($sort_array, $sort_order, $array);
    return $array;
}

/**
 * Filter the entire dataset of iconsets searching for
 * specific iconset_ids.
 * @param array $iconsets The whole dataset
 * @param array $sets An array of iconset_ids to find
 * @return array
 */
function filter_by_iconsets($iconsets, $sets) {
    $filtered = array();
    if (is_array($iconsets) && count($iconsets)) {
        foreach ($iconsets as $iconset) {
            if (in_array($iconset['iconset_id'], $sets)) {
                array_push($filtered, $iconset);
            }
        }
    }
    return $filtered;
}

/**
 * Conditionally show the advanced menu options.
 * @since 1.1.0
 */
function iconfinder_conditional_actions() {

    if (icf_is_advanced_mode()) {
        add_collections_post_type();
        add_iconsets_post_type();
        add_icons_post_type();
    }
}
add_action( 'init', 'iconfinder_conditional_actions' );

/**
 * Get an Iconfinder WP iconset custom post type by iconset_id.
 * @param string $iconset_id
 * @return object|\WP_Post|\WP_Error
 * @since 1.1.0
 */
function get_post_by_iconset_id($iconset_id) {
    return icf_get_post(
        $iconset_id,
        'iconset_id',
        'iconset'
    );
}

/**
 * Get an Iconfinder WP collection custom post type by collection_id.
 * @param string $collection_id
 * @return object|\WP_Post|\WP_Error
 * 
 * @since 1.1.0
 */
function get_post_by_collection_id($collection_id) {
    return icf_get_post(
        $collection_id,
        'collection_id',
        'collection'
    );
}

/**
 * Get an Iconfinder WP icon custom post type by collection_id.
 * @param string $icon_id
 * @return object|\WP_Post|\WP_Error
 * 
 * @since 1.1.0
 */
function get_post_by_icon_id($icon_id) {
    return icf_get_post(
        $icon_id,
        'icon_id',
        'icon'
    );
}

/**
 * Get the last attachment for a post.
 * @param integer $post_id
 * @return object
 * 
 * @since 1.1.0
 */
function get_last_attachment($post_id) {
    
    $result = null;

    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'post_parent' => $post_id,
        'orderby'     => 'ID'
    ));

    if (is_array($attachments) && count($attachments)) {
        $result = $attachments[0];
    }
    return $result;
}

/**
 * Get all attachments for a post.
 * @param integer $post_id
 * @return array or \WP_Error
 * 
 * @since 1.1.0
 */
function get_all_attachments($post_id) {
    
    return get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'post_parent' => $post_id,
        'orderby'     => 'ID'
    ));
}

/**
 * Imports all previews for an icon.
 * @param array $iconset
 * @return array|\WP_Error|null
 * 
 * @since 1.1.0
 */
function import_iconset_icons($iconset) {
    
    $result = null;

    /**
     * If we don't have the iconset_id, we can't do anything and
     * something was done wrong before calling this method.
     */
    if (! isset($iconset['iconset_id']) || empty($iconset['iconset_id'])) {
        return icf_append_error(
            $result, 
            null, 
            __( 'Could not import iconset previews. Iconset not well-formed', ICF_PLUGIN_NAME )
        );
    }
    
    $icons = get_icons_from_api($iconset['identifier']);

    /**
     * If there are no icons, nothing to do.
     */
    if (! count($icons)) {
        return null;
    }

    $icon_preview_sizes = icf_get_setting( 'icon_preview_sizes' );

    $post_ids = array();

    foreach ($icons as $icon) {

        $icon_post_id = create_icon_post($icon, $iconset);

        if (is_wp_error($icon_post_id)) {
            $result = icf_append_error($result, $icon_post_id);
        }
        else {
            
            $post_ids[] = $icon_post_id;
            add_icon_post_meta( $icon_post_id, $iconset, $icon );

            /**
             * Loop through the allowed sizes and attach them if they exist.
             */
            if (is_array($icon_preview_sizes) && count($icon_preview_sizes)) {
                foreach ($icon_preview_sizes as $key) {

                    if (! is_array($icon)) { continue; }
                    if (! isset($icon['previews'])) { continue; }
                    if (! isset($icon['previews'][$key])) { continue; }

                    $preview = $icon['previews'][$key];

                    if (isset($preview['src']) && ! empty($preview['src'])) {
                        add_post_meta( $icon_post_id, "preview_image_{$key}", $preview['src'], true );
                        add_post_meta( $icon_post_id, "parent_post_id" , $iconset['post_id'], true );
                    }
                }
            }
        }
    }    
    
    if (! is_wp_error($result)) {
        $result = $post_ids;
    }
    return $result;
}

/**
 * Attaches post featured image
 * @param integer $post_id
 * @param object|\WP_Post $image
 * @return int|\WP_error
 */
function set_preview($post_id, $image) {
    $result = null;
    if (is_post($image)) {
        $thumb_id = set_post_thumbnail( $post_id, $image->ID );
        if (is_wp_error($thumb_id)) {
            $result = icf_append_error($result, $thumb_id);
        }
        else {
            $result = $thumb_id;
        }
    }
    return $result;
}

/**
 * Create a new iconset post.
 * @param $iconset_id
 * @param array $attrs
 * @return int|null|WP_Error
 */
function create_iconset_post($iconset_id, $attrs=array()) {
    
    $result = null;

    $attrs = ! is_array($attrs) ? array() : $attrs ;

    /**
     * If the post already exists, throw an error and return.
     */

    if (icf_post_exists($iconset_id)) {
        return icf_append_error(
            $result, null,
            __( "Iconset with ID `{$iconset_id}` already exists. Use update_iconset_post instead", ICF_PLUGIN_NAME )
        );
    }

    /**
     * Get the iconset from the API data. If we do not find any data
     * from the API, obviously something went wrong, so return
     * an error.
     */

    $iconset = empty($attrs) ? get_one_iconset($iconset_id) : $attrs;

    $identifier = get_val( $iconset, 'identifier' );

    if (! is_array($iconset) || ! isset($iconset['iconset_id'])) {
        return icf_append_error(
            $result, null,
            __( "Iconset with ID `{$iconset_id}` could not be found via the Iconfinder API", ICF_PLUGIN_NAME )
        );
    }

    /**
     * We have all of the data we need so far, let's continue.
     */

    /**
     * Try to insert the new iconset_post. If we can't insert the post,
     * there is nothing else we can do set set the error and exit.
     */

    $post_id = wp_insert_post(
        iconset_to_post($iconset)
    );
    $iconset_post = get_post($post_id);

    if (is_wp_error($post_id)) {
        $result = icf_append_error($result, $post_id);
    } 
    else {

        /**
         * Add the post_id to the Iconset data so we don't have to
         * pass it as a separate argument while we are working on the
         * import.
         */
        
        $iconset['post_id'] = $post_id;
        $iconset['guid']    = $iconset_post->guid;
        
        //TODO: if the collection doesn't exist, add it
        //TOD: add collection_id relationship field
        //@deferred
        
        add_iconset_meta($post_id, $iconset);

        /**
         * Add links to previews as meta_data
         */

        $iconset_preview_sizes = icf_get_setting( 'iconset_preview_sizes' );

        foreach ($iconset_preview_sizes as $size) {
            add_post_meta(
                $post_id,
                "preview_image_{$size}",
                get_iconfinder_preview_url($size, $identifier),
                true
            );
        }

        //TODO: Consider importing more than a single iconset preview.

        /**
         * Let's convert the category name, which theoretically should
         * be SEO-friendly to alt and title attributes to help
         * improve the SEO of the preview image.
         *
         */

        $category_names = array_column( $iconset['categories'], 'name' );
        $alt_text = terms_to_sentence('Iconsets', $category_names, 12);
        add_post_meta( $post_id, "image_alt" , $alt_text, true );

        /**
         * Import the icons and icon images. We want to capture the post_ids
         * of the icons that are imported so we can add their tags to
         * the iconset so when you search iconsets, you are also,
         * in effect, searching the metadata of the icons in the set.
         */
                
        $icon_post_ids = import_iconset_icons($iconset);

        /**
         * If the imports failed, we can't do anything else with them, so skip it.
         */

        if (is_wp_error($icon_post_ids)) {
            $result = icf_append_error($result, $icon_post_ids);
        }
        else {
            $iconset['tags'] = get_post_tags($icon_post_ids);
        }

        /**
         * Now we can add the tags of the individual icons to the iconset.
         */

        add_iconset_terms($post_id, $iconset);
        
        if (! is_wp_error($result)) {
           $result = $post_id;
        }       
    }
    return $result;
}

/**
 * Builds the URL to iconset preview images. We take an indirect approach because
 * if the paths to resources on the Iconfinder side change, we only want to have to
 * update a single file.
 * @param $size
 * @param $identifier
 * @return mixed
 */
function get_iconfinder_preview_url($size, $identifier) {

    return str_replace(
        array(ICF_TOKEN_SIZE, ICF_TOKEN_IDENTIFIER),
        array($size, $identifier),
        ICF_ICONSET_PREVIEW_URL
    );
}

/**
 * Get all tags for posts in an array of post IDs.
 * @param array $post_ids
 * @return array
 */
function get_all_tags($post_ids) {
    $tags = array();
    if (! is_array($post_ids)) {
        $post_ids = array($post_ids);
    }
    foreach ($post_ids as $post_id) {
        $terms = wp_get_post_terms( $post_id, 'icon_tag' );
        if (is_array($terms) && count($terms)) {
            foreach ($terms as $term) {
                if (! in_array($term->slug, $tags)) {
                    $tags[] = $term->slug;
                }
            }
        }
    }
    return $tags;
}

/**
 * Create a new collection post.
 * @param array $attrs
 * @return int|\WP_Error
 * 
 * @since 1.1.0
 */
function create_collection_post($attrs) {
    
    return wp_insert_post(
        collection_to_post($attrs)
    );
}

/**
 * Create a new icon post from an array of values.
 * @param array $icon
 * @param null|array $iconset
 * @return int|WP_Error
 */
function create_icon_post($icon, $iconset=null) {

    return wp_insert_post(
        icon_to_post($icon, null, $iconset)
    );
}

/**
 * Update an existing iconset post from an array.
 * @param integer $iconset_id
 * @param array $attrs
 * @return int|\WP_Error
 * 
 * @since 1.1.0
 */
function update_iconset_post($iconset_id, $attrs) {    
    
    $result = null;
    $new_icon_ids = array();

    /**
     * API data
     */
    
    $iconset  = empty($attrs) ? get_one_iconset($iconset_id) : $attrs ;
    $icons    = get_icons_from_api($iconset['identifier']);
    $icon_ids = array_column($icons, 'icon_id');

    /**
     * WP Post imports
     */
    
    $iconset_post = get_post_by_iconset_id($iconset_id);

    /**
     * We do not have an iconset from the API, nothing we can do.
     */
    
    if (empty($iconset)) {
        return icf_append_error($result, null, __( "No iconset was found to update", ICF_PLUGIN_NAME ));
    }

    /**
     * We do not have a local iconset post, we could create one,
     * but this is not the place. We cannot proceed.
     */

    if (! is_object($iconset_post) || ! isset($iconset_post->ID)) {
        return icf_append_error($result, null, __( "No iconset was found to update", ICF_PLUGIN_NAME ));
    }

    /**
     * We have all the data we need to proceed.
     */

    /**
     * First, update the WP iconset post.
     */
    
    $result = update_post(
        get_post_by_iconset_id($iconset_id),
        iconset_to_post($iconset, $iconset_post)
    );

    /**
     * If we encounter an error, display the error and exit.
     */
    
    if (is_wp_error($result)) {
        return $result;
    }
    
    add_iconset_terms($iconset_post->ID, $iconset);

    /**
     * If we don't have any icons, there are two possible causes:
     * 1. There was an error communicating with Iconfinder, so we exit.
     * 2. The icons were deleted on Iconfinder. We don't want to assume
     *    this, however, so it's best to just exit. The proper way to
     *    Remove icons is to delete the iconset on Iconfinder.
     *    We will just play it safe and assume there was an error
     *    and just exit without doing any damage.
     */

    if (! count($icons)) {
        return icf_append_error(
            $result, null,
            __( "No icons found for Iconset `{$iconset['identifier']}` with id `{$iconset_id}`", ICF_PLUGIN_NAME )
        );
    }
    // We have some icons from the API.
    else {

        /**
         * Grab all of the icon posts.
         */
        
        $icon_posts = get_icons_by_iconset_id($iconset_id);

        /**
         * Delete local copies that are no longer in the API results.
         */
        
        if (count($icon_posts)) {
            foreach ($icon_posts as $icon_post) {
                if (! in_array($icon_post->icon_id, $icon_ids)) {
                    $del_post_id = delete_icon_post($icon_post->icon_id);
                    if (is_wp_error($del_post_id)) {
                        $result = icf_append_error($result, $del_post_id);
                    }
                }
            }
        }

        /**
         * Loop through the API results, Add or update the remaining API results.
         */
        
        foreach ($icons as $icon) {

            /**
             * See if we have a local copy.
             */
            
            $icon_post = get_post_by_icon_id($icon['icon_id']);

            /**
             * We have a local copy, update it.
             */
            
            if (! empty($icon_post)) {
                $update_post_id = update_icon_post($icon['icon_id'], $icon);
                if (is_wp_error($update_post_id)) {
                    $result = icf_append_error($result, $update_post_id);
                }
            }
            // We don't have a local copy, create it.
            else {
                $create_post_id = create_icon_post($icon);
                $new_icon_ids[] = $create_post_id;
                if (is_wp_error($create_post_id)) {
                    $result = icf_append_error($result, $create_post_id);
                }
            }
        }
        if (count($new_icon_ids)) {
            update_post_tags($iconset_post->ID, get_all_tags($new_icon_ids));
        }
    }
    return $result;
}

/**
 * @param $post_id
 * @param $new_tags
 */
function update_post_tags($post_id, $new_tags) {
    $terms = wp_get_post_terms($post_id, 'icon_tag');
    $old_tags = array();
    if (is_array($terms) && count($terms)) {
        foreach ($terms as $term) {
            $old_tags[] = $term->slug;
        }
    }
    $diff = array();
    foreach ($new_tags as $tag) {
        $tag = strtolower($tag);
        if (! in_array($tag, $old_tags)) {
            $diff[] = $tag;
        }
    }
    icf_set_tags($post_id, $diff);
}

/**
 * Get all tags for posts in an array of post IDs.
 * @param array $post_ids
 * @return array
 */
function get_post_tags($post_ids) {
    $tags = array();
    if (! is_array($post_ids)) {
        $post_ids = array($post_ids);
    }
    foreach ($post_ids as $post_id) {
        $terms = wp_get_post_terms($post_id, 'icon_tag');
        if (is_array($terms) && count($terms)) {
            foreach ($terms as $term) {
                if (! in_array($term->slug, $tags)) {
                    $tags[] = $term->slug;
                }
            }
        }
    }
    return $tags;
}

/**
 * Update an existing collection post.
 * @param integer $collection_id
 * @param array $attrs
 * @return integer or \WP_Error
 * 
 * @since 1.1.0
 */
function update_collection_post($collection_id, $attrs) {
    
    return update_post(
        get_post_by_collection_id($collection_id),
        collection_to_post($attrs)
    );
}
 
 /**
  * Update an existing icon post.
  * @param integer $icon_id
  * @param array $attrs
  * @return integer or \WP_Error
  * 
  * @since 1.1.0
  */
function update_icon_post($icon_id, $attrs) {
    
    $result = null;
    $iconset_id = null;
    $post = get_post_by_icon_id($icon_id);

    if (is_object($post) && isset($post->ID)) {
        $iconset_id = get_post_meta($post->ID, 'iconset_id');
        $iconset = null;
        if (! empty($iconset_id)) {
            $iconset = get_one_iconset($iconset_id);
        }
        $result = update_post(
            $post,
            icon_to_post($attrs, $post, $iconset)
        );
    }
    else {
        $result = icf_append_error(
            $result, null,
            __( "The post for Iconset ID {$iconset_id} could not be found", ICF_PLUGIN_NAME )
        );
    }
    return $result;
}

 /**
  * Permanently delete an iconset post.
  * @param int $iconset_id
  * @return int|\WP_Error
  * 
  * @since 1.1.0
  */
function delete_iconset_post($iconset_id) {
    
    $result = null;
    try {
        $post = get_post_by_iconset_id($iconset_id);
        if (is_object($post) && isset($post->ID)) {
            $attachments = get_all_attachments($post->ID);
            if (is_array($attachments) && count($attachments)) {
                foreach ($attachments as $attachment) {
                    if (! is_object($attachment) || ! isset($attachment->ID)) {
                        continue;
                    }
                    $delete = wp_delete_post( $attachment->ID, true );
                    if (is_wp_error($delete)) {
                        $result = icf_append_error($result, $delete);
                        $delete = null;
                    }
                }
            }
            $result = wp_delete_post( $post->ID, true );
        }
        else {
            $result = icf_append_error(
                $result, null,
                __( "The post matching Iconset {$iconset_id} could not be deleted, because it was not found.", ICF_PLUGIN_NAME )
            );
        }
        $delete = null;
        if (! is_wp_error($result)) {
            $delete = delete_icons_in_iconset($iconset_id);
        }
        if (is_wp_error($delete)) {
            $result = icf_append_error($result, $delete);
        }
        if (! is_wp_error($result) && isset($post->ID) ) {
            $result = $post->ID;
        }
    }
    catch(Exception $ex) {
        $result = icf_append_error(
            $result, null,
            $ex->getMessage()
        );
    }
    return $result;
}

/**
 * Permanently delete a collection post.
 * @param int $collection_id
 * @return int|\WP_Error
 * 
 * @since 1.1.0
 */
function delete_collection_post($collection_id) {

    //TODO: This is not fully-implemented as of 12/3/2016

    $result = $collection_id;
    return $result;
}

/**
 * Permanently delete an icon post.
 * @param int $icon_id
 * @return int|\WP_Error
 * 
 * @since 1.1.0
 */
function delete_icon_post($icon_id) {
    
    $result = null;
    $post = get_post_by_icon_id($icon_id);
    if (is_object($post) && isset($post->ID)) {
        $images = get_attached_media( 'image', $post->ID );

        /**
         * Try to delete the images first
         */
        if (count($images)) {
            foreach ($images as $image) {
                $delete = wp_delete_post( $image->ID, true );
                if (is_wp_error($delete)) {
                    $result = icf_append_error($result, $delete);
                }
            }
        }
        /**
         * If there were no errors or no images, delete the post
         */
        if (! is_wp_error($result)) {
            $delete = wp_delete_post( $post->ID, true );
            if (is_wp_error($delete)) {
                $result = icf_append_error($result, $delete);
            }
        }
    }
    else {
        $result = icf_append_error(
            $result, null,
            __( "Icon post matching icon_id `{$icon_id}` could not be found.", ICF_PLUGIN_NAME )
        );
    }
    return $result;
}

/**
 * Trash all icons in iconset AND the iconset itself.
 * @param int $iconset_id
 * @return bool|\WP_Error
 * 
 * @since 1.1.0
 */
function delete_icons_in_iconset($iconset_id) {
    
    $result = null;
    $posts = get_icons_by_iconset_id($iconset_id);
    if (! count( $posts )) {
       return true; 
    }

    foreach ( $posts as $post ) {
        /**
         * Try to delete the images first.
         */
        $images = get_attached_media( 'image', $post->ID );
        if (count($images)) {
            foreach ($images as $image) {
                $delete = wp_delete_post( $image->ID, true );
                if (is_wp_error($delete)) {
                    $result = icf_append_error($result, $delete);
                }
            }
        }
        /**
         * If there are no errors or no images, delete the icon post.
         */
        if (! is_wp_error($result)) {
            $delete = wp_delete_post( $post->ID, true );
            if (is_wp_error( $delete )) {
                $result = icf_append_error($result, $delete);
            }
        }
    }
    return $result;
}

/**
 * Update a post with values from a keyed array.
 * @param object $post
 * @param array $updates_array
 * @return boolean or \WP_Error
 * 
 * @since 1.1.0
 */
function update_post($post, $updates_array) {
    $result = null;
    if (is_object($post) && isset($post->ID)) {
        $updates_array['ID'] = $post->ID;
        $result = wp_update_post($updates_array, true);
    }
    else {
        $result = icf_append_error($result, null, __( "No post to update", ICF_PLUGIN_NAME ));
    }
    return $result;
}

/**
 * Get a single iconset matching the iconset id.
 * @staticvar array $iconsets
 * @param integer $iconset_id
 * @return integer
 * 
 * @since 1.1.0
 */
function get_one_iconset($iconset_id) {
    static $iconsets = array();
    if (! count($iconsets)) {
        $iconsets = get_all_iconsets();
    }
    $result = null;
    if (isset($iconsets['items'])) {
        foreach ($iconsets['items'] as $iconset) {
            if ($iconset['iconset_id'] == $iconset_id) {
                $result = $iconset;
            }
        }
    }
    return $result;
}

/**
 * List N number of iconsets by a specific user.
 * @param string    $username   The username of the user whose iconsets we want.
 * @param int       $count      The number of iconsets to list
 * @return array
 */
function icf_get_user_iconsets( $username, $count=-1 ) {

    $result = get_all_iconsets( $username );

    if (isset($result['items'])) {
        $result = $result['items'];
        if ($count != -1) {
            $result = array_slice( $result, 0, $count );
        }
    }

    return $result;
}

/**
 * Get all iconsets.
 * @param string    $username   Optional username for who to get all iconsets.
 * @return array|mixed|null|object
 */
function get_all_iconsets( $username=null ) {
    static $iconsets = array();
    $items    = array();
    if (empty($iconsets) || ! empty($username)) {

        $path = get_api_path('iconsets', array( 'username' => $username ));

        $batch = iconfinder_call_api(
            get_api_url($path, array('count' => ICONFINDER_API_MAX_COUNT))
        );
        $total_count = get_val($batch, 'total_count') + 1;
        $page_count = ceil($total_count / ICONFINDER_API_MAX_COUNT);
        $iconsets = $batch;
        for ($i=0; $i<$page_count; $i++) {
            $last_id = null;
            if (isset($batch['items']) && count($batch['items'])) {
                $n = count($batch['items'])-1;
                if (isset($batch['items'][$n]['iconset_id'])) {
                    $last_id = $batch['items'][$n]['iconset_id'];
                    # $count = max(1, $total_count - ( ICONFINDER_API_MAX_COUNT * ($i + 1) ));
                    $batch = iconfinder_call_api(
                        get_api_url($path, array( 'after' => $last_id, 'count' => ICONFINDER_API_MAX_COUNT ))
                    );
                    if (is_array($iconsets['items']) && is_array($batch['items'])) {
                        $iconsets['items'] = array_merge($iconsets['items'], $batch['items']);
                    }
                }
            }
        }
        if (isset($iconsets['items'])) {
            $ids = array();
            foreach ($iconsets['items'] as $item) {
                if (! in_array($item['iconset_id'], $ids)) {
                    $items[] = $item;
                }
            }
            $iconsets['items'] = $items;
            $iconsets['item_count'] = count($iconsets['items']);
        }
    }
    $result = $iconsets;
    if (! empty($username)) $iconsets = array();
    return $result;
}

/**
 * Get a single post by custom field value.
 * @param mixed $meta_value
 * @param string $meta_key
 * @param string $post_type
 * @return object
 * 
 * @since 1.1.0
 */
function icf_get_post($meta_value, $meta_key, $post_type) {
    $result = null;
    $posts = get_posts(array(
        'numberposts'   => 1,
        'post_type'     => $post_type,
        'meta_key'      => $meta_key,
        'meta_value'    => $meta_value
    ));
    if (count($posts)) {
        $result = $posts[0];
        $result->$meta_key = $meta_value;
    }
    else {
        $result = icf_append_error(
            $result, null, 
            __( "Post with `{$meta_key}={$meta_value}` could not be found.", ICF_PLUGIN_NAME )
        );
    }
    return $result;
}

/**
 * Get all posts by custom field value.
 * @param mixed $meta_value
 * @param string $meta_key
 * @param string $post_type
 * @return array|null
 * 
 * @since 1.1.0
 */
function icf_get_all_posts($meta_value, $meta_key, $post_type) {
    $posts = get_posts(array(
        'numberposts'        => -1,
        'post_type'                => $post_type,
        'meta_key'      => $meta_key,
        'meta_value'    => $meta_value
    ));
    if (count($posts)) {
        foreach ($posts as &$post) {
             $post->$meta_key = $meta_value;
        }
    }
    return $posts;
}

/**
 * Get all icons in an iconset by iconset_id.
 * @param integer $iconset_id
 * @return array
 * 
 * @since 1.1.0
 */
function get_icons_by_iconset_id($iconset_id) {
    return icf_get_all_posts($iconset_id, 'iconset_id', 'icon');
}

/**
 * Retrieve all icons for a specific iconset.
 * @param int       $post_parent        The ID of the parent (iconset) post
 * @param array     $more_args          Keyed array of query arguments to filter the results
 * @param bool      $refresh            Whether or not to refresh any statically stored results.
 *
 * @return mixed
 */
function get_icon_posts_by_post_parent( $post_parent, $more_args=array(), $refresh=false ) {

    /** @staticvar  $posts */
    static $icons;

    /**
     * If we have previously requested icons for this iconset,
     * And the set is not empty, and we have not requested to
     * refresh the static var, we can save some CPU cycles
     * by returning the previously queried posts.
     */
    if ( isset($icons["_{$post_id}"]) && ! $refresh ) {
        $stored = $icons["_{$post_id}"];
        if ( count($stored) ) {
            return $stored;
        }
    }

    $query_args = array_merge(array(
        'post_type'      => 'icon',
        'post_parent'    => $post_parent,
        'post_status'    => 'publish',
        'posts_per_page' => -1
    ), $more_args);

    /**
     * Update the staticvar for next time.
     */
    $icons = query_posts($query_args);

    return $icons;
}

/**
 * Add the post metadata.
 * @param integer $post_id
 * @param array $iconset
 * @param array $icon
 * @return array
 */
function add_icon_post_meta($post_id, $iconset, $icon) {
    
    $meta_ids = array();
    $term_ids = array();
    
    $meta_ids[] = add_post_meta( $post_id, 'icon_id', $icon['icon_id'], true );
    $meta_ids[] = add_post_meta( $post_id, 'iconset_id', $iconset['iconset_id'], true );
    $meta_ids[] = add_post_meta( $post_id, 'iconset_identifier', $iconset['identifier'], true );
    $meta_ids[] = add_post_meta( $post_id, 'is_premium', $icon['is_premium'], true );
    $meta_ids[] = add_post_meta( $post_id, 'price', $icon['price']['price'], true );
    $meta_ids[] = add_post_meta( $post_id, 'license', $icon['price']['license']['url'], true );
    $meta_ids[] = add_post_meta( $post_id, 'latest_sync', date('Y-m-d H:i:s'), true );

    $alt_text = terms_to_sentence('Icons', $icon['tags'], 12);
    add_post_meta( $post_id, "image_alt", $alt_text, true );
    
    $term_ids[] = icf_set_categories( $post_id, $icon['categories']);
    $term_ids[] = icf_set_tags( $post_id, $icon['tags']);
    
    return array(
        'meta_ids' => $meta_ids,
        'term_ids' => $term_ids
    );
}

/**
 * Converts an array of image tags into a proper sentence.
 * @param sring $subject
 * @param array|string $words
 * @param int $lenth
 *
 * @return string
 */
function terms_to_sentence($subject, $words, $max=12) {

    /**
     * If $words is not an array, no need to go further.
     */
    if (! is_array($words)) {
        return "{$subject} related to {$words}";
    }

    /**
     * Let's make sure we don't exceed the max length.
     */
    if (count($words) > $max) {
        $words = array_slice($words, 0, $max);
    }

    /**
     * If any of the 'words' have an ampersand or 'and', let's add more words.
     */
    $more_words = array();
    foreach ($words as $word) {
        $more_words = array_merge($more_words, explode('&', $word) );
    }

    /**
     * Look for ' and ' and convert to comma-separated words.
     */
    $words = $more_words;
    foreach ($words as $word) {
        $more_words = array_merge($more_words, explode(' and ', $word) );
    }

    /**
     * Trim any trailing/leading white space to be neat.
     */
    $words = array_map( 'trim', $more_words );

    /**
     * Grab all but the last word ...
     */

    $first = implode(', ', array_slice( $words, 0, count($words) -1 ));

    /**
     * Let's add the Oxford comma.
     * (Yes, we are that anal ... and pedantic) :-)
     */
    if (count($words) > 2) $first .= ",";

    /**
     * Get the very last word.
     */
    $last  = end($words);

    /**
     * And put them together in a sentence
     * of which the Queen herself would be proud.
     */
    return "{$subject} related to {$first} and {$last}";
}

/**get_val($iconset, 'is_premium')
 * Adds iconset metadata.
 * @param integer $post_id
 * @param array $iconset
 */
function add_iconset_meta($post_id, $iconset) {

    add_post_meta( $post_id, 'iconset_id', get_val($iconset, 'iconset_id'), true );
    add_post_meta( $post_id, 'iconset_identifier', get_val($iconset, 'identifier'), true );
    add_post_meta( $post_id, 'latest_sync', date('Y-m-d H:i:s'), true );
    add_post_meta( $post_id, 'icons_count', get_val($iconset, 'icons_count') );
    add_post_meta( $post_id, 'iconset_type', get_val($iconset, 'type') );
    add_post_meta( $post_id, 'is_premium', get_val($iconset, 'is_premium'), true );
    add_post_meta( $post_id, 'guid', get_val($iconset, 'guid'), true );
    if ( isset($iconset['prices']) && is_array($iconset['prices']) ) {
        if ( isset( $iconset['prices'][0]['price'] ) ) {
            add_post_meta( $post_id, 'price', $iconset['prices'][0]['price'], true );
            add_post_meta( $post_id, 'currency', $iconset['prices'][0]['currency'], true );
        }
    }
    if ( isset($iconset['prices']) && is_array($iconset['prices']) ) {
        if ( isset( $iconset['prices'][0]['license'] ) ) {
            $license = $iconset['prices'][0]['license'];
            foreach ($license as $key => $value) {
                add_post_meta( $post_id, "license_{$key}", $value, true );
            }
        }
    }
    if ( isset($iconset['styles']) && is_array($iconset['styles']) ) {
        if (count($iconset['styles'])) {
            $style = $iconset['styles'][0];
            foreach ($style as $key => $value) {
                add_post_meta( $post_id, "iconset_style_{$key}", $value, true );
            }
        }
    }
    add_iconset_terms($post_id, $iconset);
}

/**
 * Add categories to the iconset.
 * @param integer $post_id
 * @param mixed $iconset
 * 
 * @since 1.1.0
 */
function add_iconset_terms($post_id, $iconset) {

    if (! isset($iconset['tags'])) {
        $iconset['tags'] = array();
    }
    if (isset($iconset['categories']) && count($iconset['categories'])) {
                
        icf_set_categories($post_id, $iconset['categories']);
        foreach ($iconset['categories'] as $category) {
            $iconset['tags'][] = $category['identifier'];
            $_tags = str_to_words($category['identifier'], '-');
            foreach ($_tags as $_tag) {
                $iconset['tags'][] = $_tag;
            }
        }
    }
    icf_set_tags($post_id, $iconset['tags']);
}

/**
 * Creates the new terms for an icon or iconset.
 * @param array $terms
 * @param string $taxonomy
 * 
 * @since 1.1.0
 */
function create_new_terms($terms, $taxonomy) {
    foreach ($terms as $term) {
        if (is_array($term)) {
            $name = $term['name'];
            $slug = $term['identifier'];
        }
        else {
            $name = ucwords($term);
            $slug = sanitize_title($term);
        }
        $tax_name = ucwords(str_replace('_', ' ', $taxonomy));
        if (! term_exists( $name, $taxonomy )) {
            wp_insert_term(
                ucwords($name),
                $taxonomy,
                array(
                    'description'=> "{$tax_name} {$name}",
                    'slug' => $slug
                )
            );
        }
    }
}

/**
 * Set the tags for a post. Creates them if they don't already exist.
 * @param integer $post_id
 * @param array $tags
 * @return array|\WP_Error
 * 
 * @since 1.1.0
 */
function icf_set_tags($post_id, $tags) {
    create_new_terms($tags, 'icon_tag');
    return wp_set_post_terms( $post_id, $tags, 'icon_tag', false);
}

/**
 * Set the categories for a post. Creates them if they don't already exist.
 * @param integer $post_id
 * @param array $categories array('identifier' => string, 'name' => string)
 * @return array|\WP_Error
 * 
 * @since 1.1.0
 */
function icf_set_categories($post_id, $categories) {
    
    $post_categories = array();
    create_new_terms($categories, 'icon_category');
    foreach ($categories as $category) {
        if (is_string($category)) {
            $slug = sanitize_title($category);
        }
        else {
            $slug = $category['identifier'];
        }
        $term = term_exists( $slug, 'icon_category' );
        if (! empty($term) && isset($term['term_id'])) {
            array_push($post_categories, $term['term_id']);
        }
    }
    return wp_set_post_terms( $post_id, $post_categories, 'icon_category', false);
}