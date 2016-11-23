<?php

/**
 * This is a collection of utility functions used globally throughout the plugin.
 *
 * @link       http://iconify.it
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
 * @author     Scott Lewis <scott@iconify.it>
 */ 

function iconfinder_footer_link() {
    $_options = get_option('iconfinder-portfolio');
    $username = get_val($_options, 'username');
    $ref = "";
    if (! empty($username)) {
        $ref = "?ref={$username}";
    }
    echo "<p class=\"powered-by-iconfinder\"><a href=\"https://iconfinder.com/{$ref}\">Powered by Iconfinder</a></p>";
 } 
add_action( 'wp_footer', 'iconfinder_footer_link', 1 );

/**
 * Grab only the data we need from the API response
 * @param <array> $raw
 * @return <array>
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
            if (isset($raster['formats']) && count(isset($raster['formats']))) {
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
 * @param <array> $attrs
 * @param <array> $defaults
 * @return <array>
 * 
 * @since 1.1.0
 */
function get_shortcode_attrs($attrs, $defaults) {
    $cleaned = shortcode_atts(
        array(
            'id'         => '',
            'username'   => get_val($defaults, 'username'),
            'count'      => get_val($defaults, 100),
            'style'      => '',
            'type'       => '',
            'collection' => '',
            'iconset'    => '',
            'sets'       => '',
            'categories' => '',
            'theme'      => '',
            'sort_by'    => get_val($defaults, 'name'),
            'sort_order' => get_val($defaults, SORT_DESC),
            'omit'       => '',
            'img_size'   => get_val($defaults, 'medium-2x'),
            'show_links' => get_val($defaults, 1),
            'show_price' => get_val($defaults, 1)
    ), $attrs );
    array_map('strtolower', $cleaned);
    return $cleaned;
}

/**
 * Returns the requested value or default if empty
 * @param <mixed> $subject
 * @param <string> $key
 * @param <mixed> $default
 * @return <mixed>
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
 * Converts a shortcode comma-delimited list into a PHP array
 * 
 * @param <string> $str
 * @param <string> $delim The delimiter to use
 * @return <array> 
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
 * 
 * @since 1.0.0
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
 * @param $username - String username
 * @param $attrs    - An array of the API path segments
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
 * @since 1.0.0
 */
function icf_update_cache_keys($new_key) {

    $cache_keys = icf_get_cache_keys();
    
    if ( ! in_array( $new_key, $cache_keys ) )  {
        array_push( $cache_keys, $new_key );
    	$saved = update_option( 'icf_cache_keys', $cache_keys, 'no' );
    }
}

/**
 * Make the API call
 */
function iconfinder_call_api($api_url, $cache_key='', $from_cache=true) {

    $response = null;
    
    // Always try the local cache first. If we get a hit, just return the stored data.
    
    if ( $from_cache && $response = get_option( $cache_key ) ) {
        
        $response['from_cache'] = 1;
        return $response;
    }
    
    // If there is no cached data, make the API cale.
    
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
        
        // a bit kludgy, but I want to normalize the response fields here 
        // instead of having a bunch of conditional checks elesewhere.
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
        
        if (trim($cache_key) != '') {
        	if ( update_option( $cache_key, $response ) ) {
        	    $stored_keys = get_option( 'icf_cache_keys', array() );
        	    if ( ! in_array( $cache_key, $stored_keys ) )  {
        			array_push( $stored_keys, $cache_key );
    				$saved = update_option('icf_cache_keys', $stored_keys, 'no');
    			}
        	}
        	
        }
    }
    catch(Exception $e) {
        throw new Exception($e);
    }
    
    if ($response == null && trim($cache_key) != '') {
        $response = get_option( $cache_key );
    }
    
    return $response;
}

function iconfinder_sort_array($array, $sort_by, $sort_order) {
        
    $sort_array = array();
    
    foreach($array as $key => $val) {
        $sort_array[$key] = $val[$sort_by];
    }
    array_multisort($sort_array, $sort_order, $array);
    return $array;
}

/**
 * Sort iconsets by a specific field
 * @param <Array> $array - The iconsets to sort
 * @param <String> $on - The field to sort on
 * @param <String> $order - The sort order
 */
/*
function sort_array($array, $on, $order) {

    usort($array, function($a, $b) use ($on, $order) {
        if ($order == SORT_ASC) return $a[$on] < $b[$on];
        return $a[$on] > $b[$on];
    });
    return $array;
}
*/

/**
 * This is a debug function and ideally should be removed from the production code.
 */
function icf_dump($what) {
    die ('<pre>' . print_r($what, true) . '</pre>');
}


function iconfinder_conditional_actions() {
    $options = get_option('iconfinder-portfolio');

    $plugin_mode = strtolower(get_val( $options, 'plugin_mode', 'basic' ));
    if (empty($plugin_mode)) {
        $plugin_mode = 'basic';
    }
    if ($plugin_mode === 'advanced' ) {
        add_collections_post_type();
        add_iconsets_post_type();
        add_icons_post_type();
    }
}
add_action( 'init', 'iconfinder_conditional_actions' );
    
function add_iconfinder_meta_boxes() {
    
    add_meta_box( 'collections_metabox', 'Iconsets', 'show_collections_metabox', 'collection', 'side', 'high');
    add_meta_box( 'iconsets_metabox', 'Collection', 'show_iconsets_metabox', 'iconset', 'side', 'high' );
    add_meta_box( 'icons_metabox', 'Iconset', 'show_icons_metabox', 'icon', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'add_iconfinder_meta_boxes' );
 
function show_collections_metabox($post) {
  
    echo 'List iconsets here';
}

function show_iconsets_metabox($post) {
  
    $iconset_id = get_post_meta( $post->ID, 'iconset_id', true );
    $iconset_identifier = get_post_meta( $post->ID, 'iconset_identifier', true );
    $latest_sync = get_post_meta( $post->ID, 'latest_sync', true );
    if (! empty($iconset_id)) {
        echo "<p><strong>Iconset:</strong> {$iconset_identifier} (ID: {$iconset_id})</p>";
        echo "<p><a href=\"https://www.iconfinder.com/iconsets/{$iconset_id}\" target=\"_blank\">View on Iconfinder</a></p>";
        echo "<p><strong>Last Sync:</strong> $latest_sync</p>";
    }
    else {
        //TODO: Show defeault message
    }
}

function show_icons_metabox($post) {
  
    $icon_id    = get_post_meta( $post->ID, 'icon_id', true );
    $iconset_id = get_post_meta( $post->ID, 'iconset_id', true );
    $iconset_identifier = get_post_meta( $post->ID, 'iconset_identifier', true );
    $latest_sync = get_post_meta( $post->ID, 'latest_sync', true );
    if (! empty($iconset_id)) {
        echo "<p><strong>Iconset:</strong> {$iconset_identifier} (ID: {$iconset_id})</p>";
        echo "<p><a href=\"https://www.iconfinder.com/iconsets/{$iconset_id}\" target=\"_blank\">View on Iconfinder</a></p>";
        echo "<p><strong>Last Sync:</strong> $latest_sync</p>";
    }
    if (! empty($icon_id)) {
        echo "<p><strong>Icon ID: </strong>{$icon_id}</p>";
        echo "<p><a href=\"https://www.iconfinder.com/icons/{$icon_id}\" target=\"_blank\">View on Iconfinder</a></p>";
    }
    else {
        //TODO: Show default message
    }
}

if (! function_exists('is_true')) {
    function is_true($value) {
        $bools = array(1, '1', 'true', true, 'yes');
        if (in_array(strtolower($value), $bools, true)) {
            return true;
        }
        return false;
    }
}

/**
 * Get an Iconfinder WP iconset custom post type by iconset_id.
 * @param <string> $iconset_id
 * @retrn <object> The iconset custom post type
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
 * @param <string> $collection_id
 * @retrn <object> The collection custom post type
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
 * @param <string> $icon_id
 * @retrn <object> The icon custom post type
 */
function get_post_by_icon_id($icon_id) {
    return icf_get_post(
        $icon_id,
        'icon_id',
        'icon'
    );
}

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

function get_icons_from_api($identifier) {
    //TODO: get all icons for this iconset via API
    $data = iconfinder_call_api(
        Iconfinder_Portfolio_Public::get_api_url(array(
            'iconset' => $identifier
        ))
    );
    $icons = array();
    if (is_array($data) && isset($data['items'])) {
        $icons = scrub_icons_list($data['items']);
    }
    return $icons;
}

/**
 * Imports all previews for an icon.
 * @param array $iconset
 * @return boolean
 */
function import_icon_previews($iconset) {
    
    if (! isset($iconset['iconset_id']) || empty($iconset['iconset_id'])) {
        return false;
    }
    
    $error_count = 0;
    $iconset_id  = $iconset['iconset_id'];
    $icons       = get_icons_from_api($iconset['identifier']);

    if (count($icons)) {

        $import_sizes = icf_get_setting('icon_import_sizes');
        $default_preview_size = icf_get_setting('icon_default_preview_size');
        
        foreach ($icons as $icon) {

            $icon_post_ID = create_icon_post($icon, $iconset);
            
            if (is_wp_error($icon_post_ID)) {
                $error_count++;
            }
            else {
                
                add_post_meta( $icon_post_ID, 'icon_id', $icon['icon_id'], true );
                add_post_meta( $icon_post_ID, 'iconset_id', $iconset_id, true );
                add_post_meta( $icon_post_ID, 'iconset_identifier', $iconset['identifier'], true );
                add_post_meta( $icon_post_ID, 'is_premium', $icon['is_premium'], true );
                add_post_meta( $icon_post_ID, 'price', $icon['price']['price'], true );
                add_post_meta( $icon_post_ID, 'license', $icon['price']['license']['url'], true );
                add_post_meta( $icon_post_ID, 'latest_sync', date('Y-m-d H:i:s'), true );
                
                icf_set_categories($icon_post_ID, $icon['categories']);
                icf_set_tags($icon_post_ID, $icon['tags']);

                // Loop through the allowed sizes and attach them if they exist.
                foreach ($import_sizes as $key) {
                    if (! isset($icon['previews'][$key])) {
                        continue;
                    }
                    $preview = $icon['previews'][$key];
                    if (isset($preview['src']) && ! empty($preview['src'])) {
                        $src = media_sideload_image( 
                            $preview['src'], 
                            $icon_post_ID, 
                            "Icon preview image {$key} for icon ID {$icon_post_ID}", 
                            'src' 
                        );
                        if (is_wp_error($src)) {
                            $error_count++;
                        }
                        else {
                            $preview['src'] = $src;
                            add_post_meta( $icon_post_ID, "preview-{$key}" , $preview['src'], true );
                        }
                    }
                }
                // Set the featured image for the icon post.
                if (isset($icon['previews'][$default_preview_size])) {
                    $image = get_last_attachment($icon_post_ID);
                    if (is_object($image) && isset($image->ID)) {
                        if (! set_post_thumbnail( $icon_post_ID, $image->ID )) {
                            $error_count++;
                        }
                    }
                }
            }
        }
    }
    if ($error_count > 0) {
        icf_queue_notices(
            "The icons for Iconset `{$iconset['name']}` with ID `{$iconset_id}` could not be deleted.",
            'error'
        );
    }
    return $count;
}

/**
 * Add categories to the iconset.
 * @param integer $post_id
 * @param mixed $iconset
 */
function add_iconset_terms($post_id, $iconset) {
    if (isset($iconset['categories']) && count($iconset['categories'])) {
                
        icf_set_categories($post_id, $iconset['categories']);
        $tags = array();
        foreach ($iconset['categories'] as $category) {
            $tags = array_merge(
                $tags, 
                str_to_words($category['identifier'], '-')
            );
        }
        icf_set_tags($post_id, $tags);
    }      
}

/**
 * Create a new iconset post.
 * @param type $attrs
 * @return type
 */
function create_iconset_post($iconset_id, $attrs=array()) {

    $stored_iconset_post = get_post_by_iconset_id($iconset_id);    
    if (! empty($stored_iconset_post)) {
        icf_queue_notices(
            "Iconset `{$stored_iconset_post->post_title}` with ID `{$iconset_id}` already exists. Use update_iconset_post instead", 
            'error'
        );
        return;
    }
    
    $iconset = get_one_iconset($iconset_id);
    
    if (isset($iconset['iconset_id'])) {
        $post_id = wp_insert_post(
            iconset_to_post($iconset)
        );

        if (is_wp_error($post_id)) {
            icf_queue_notices(
                $post_id->get_error_messages(),
                'error'
            );
        } 
        else {
            //TODO: if the collection doesn't exist, add it
            //TOD: add collection_id relationship field
            
            add_post_meta( $post_id, 'iconset_id', $iconset_id, true );
            add_post_meta( $post_id, 'iconset_identifier', $iconset['identifier'], true );
            add_post_meta( $post_id, 'latest_sync', date('Y-m-d H:i:s'), true );
            
            add_iconset_terms($post_id, $iconset);                  
            
            $iconset['post_id'] = $post_id;
            
            $result = import_icon_previews($iconset);
            if (! $result) {
                icf_queue_notices(
                    "There was an error importing the previews for Iconset with id `{$iconset['identifier']}`",
                    'error'
                );
            }
            else {
                icf_queue_notices(
                    "Iconset `{$iconset['identifier']}` with ID `{$iconset['iconset_id']}` was successfully created",
                    'success'
                ); 
            }         
        }
    }
    else {
        icf_queue_notices(
            "Iconset with ID `{$iconset_id}` could not be found via the Iconfinder API",
            'error'
        );
    }
    return $post_id;
}

function icf_get_setting($key, $default=null) {
    
    $settings = _icf_settings();
    $value = $default ;
    if (! empty($key) && isset($settings[$key])) {
        $value = $settings[$key];
    }
    return $value;
}        

/**
 * Create a new collection post.
 * @param type $attrs
 * @return type
 */
function create_collection_post($attrs) {
    
    return wp_insert_post(
        collection_to_post($attrs)
    );
}

/**
 * Create a new icon post.
 * @param <array> $icon
 * @return <mixed>
 */
function create_icon_post($icon, $iconset=null) {
    # icf_dump($api_icon);
    # icf_dump(icon_to_post($api_icon));
    $post_id = wp_insert_post(
        icon_to_post($icon, null, $iconset)
    );
    if (is_wp_error($post_id)) {
        icf_queue_notices(
            $post_id->get_error_messages(),
            'error'
        );
    }
    return $post_id;
}

/**
 * Update an existing iconset post.
 * @param type $iconset_id
 * @param array $attrs
 * @return boolean
 */
function update_iconset_post($iconset_id, $attrs) {    
    
    $error_count = 0;
    
    // API data
    $iconset      = get_one_iconset($iconset_id);
    $icons        = get_icons_from_api($iconset['identifier']);
    $icon_ids     = array_column($icons, 'icon_id');
    
    // WP Post imports
    $iconset_post = get_post_by_iconset_id($iconset_id);
    $icon_posts   = get_icons_by_iconset_id($iconset_id);

    // We do not have an iconset from the API, nothing we can do.
    if (empty($iconset)) {
        return;
    }
    
    // We do not have a local iconset post, we could create one,
    // but this is not the place. We cannot proceed.
    if (! is_object($iconset_post) || ! isset($iconset_post->ID)) {
        return;
    }
    
    // We have all the data we need to proceed.

    // First, update the WP iconset post.
    $result = update_post(
        get_post_by_iconset_id($iconset_id),
        iconset_to_post($iconset, $iconset_post)
    );
    // If we encounter an error, display the error and exit.
    if (is_wp_error($result)) {
        icf_queue_notices(
            $result->get_error_messages(),
            'error'
        );
        return;
    }
    
    add_iconset_terms($iconset_post->ID, $iconset);
    
    // If we don't have any icons, there are two possible causes:
    // 1. There was an error communicating with Iconfinder, so we exit.
    // 2. The icons were deleted on Iconfinder. We don't want to assume 
    //    this, however, so it's best to just exit. The proper way to 
    //    Remove icons is to delete the iconset on Iconfinder.
    //    We will just play it safe and assume there was an error 
    //   and just exit without doing any damage.
    
    if (! count($icons)) {
        icf_queue_notices(
            "No icons found for Iconset `{$iconset['identifier']}` with id `{$iconset_id}`",
            'error'
        );
        return;
    }
    // We have some icons from the API.
    else {
        // Grab all of the icon posts.
        $icon_posts = get_icons_by_iconset_id($iconset_id);
        // Delete local copies that are no longer in the API results.
        if (count($icon_posts)) {
            foreach ($icon_posts as $icon_post) {
                if (! in_array($icon_post->icon_id, $icon_ids)) {
                    if (! delete_icon_post($icon_post->icon_id)) {
                        $error_count++;
                    }
                }
            }
        }
        // Loop through the API results
        // Add or update the remaining API results.
        foreach ($icons as $icon) {
            // See if we have a local copy.
            $icon_post = get_post_by_icon_id($icon['icon_id']);
            // We have a local copy, update it.
            if (! empty($icon_post)) {
                if (! update_icon_post($icon['icon_id'], $icon)) {
                    $error_count++;
                }
            }
            // We don't have a local copy, create it.
            else {
                if (! create_icon_post($icon)) {
                    $error_count++;
                }
            }
        }
    }
    if ($error_count > 0) {
        icf_queue_notices(
            "There was an error updating Iconset `{$iconset['identifier']}`",
            'error'
        );
    }
    else {
        icf_queue_notices(
            "Iconset `{$iconset['identifier']}` was successfully updated.",
            'success'
        );
    }
    return $error_count === 0;
}

/**
 * Update an existing collection post.
 * @param type $collection_id
 * @param array $attrs
 * @return boolean
 */
function update_collection_post($collection_id, $attrs) {
    
    return update_post(
        get_post_by_collection_id($collection_id),
        collection_to_post($attrs)
    );
}
 
 /**
  * Update an existing icon post.
  * @param type $icon_id
  * @param array $attrs
  * @return boolean
  */
function update_icon_post($icon_id, $attrs) {
    
    $result = false;
    $post = get_post_by_icon_id($icon_id);
    if (is_object($post)) {
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
    return $result;
}

 /**
  * Move iconset post to trash.
  * @param type $iconset_id
  */
function delete_iconset_post($iconset_id) {
    
    $post = get_post_by_iconset_id($iconset_id);
    if (! empty($post)) {
        $result = wp_delete_post( $post->ID, true );
    }
    if (is_wp_error($result)) {
        icf_queue_notices(
            $result->get_error_messages(),
            'error'
        );
    }
    else {
        delete_icons_in_iconset($iconset_id);
        icf_queue_notices(
            "Iconset_post `{$post->post_title}` was permanently deleted",
            'success'
        );
    }
    //TODO: Handle errors
}

/**
 * Move collection post to trash.
 * @param type $collection_id
 */
function delete_collection_post($collection_id) {

    $result = null;
    $post = get_post_by_collection_id($collection_id);
    if (! empty($post)) {
         $result = wp_delete_post( $post->ID, true );
    }
    if (is_wp_error($result)) {
        icf_queue_notices(
            $result->get_error_messages(),
            'error'
        );
    }
    else {
        delete_icons_in_iconset($iconset_id);
        icf_queue_notices(
            "Collection_post `{$post->post_title}` was permanently deleted",
            'success'
        );
    }
    return $result;
}

/**
 * Move icon post to trash.
 * @param type $icon_id
 */
function delete_icon_post($icon_id) {
    
    $images = array();
    $error_count = 0;
    $post = get_post_by_icon_id($icon_id);
    if (! empty($post)) {
        $images = get_attached_media( 'image', $post->ID );
        
        // Try to delete the images first
        if (count($images)) {
            foreach ($images as $image) {
                if (is_wp_error(wp_delete_post( $image->ID, true ))) {
                    $error_count++;
                }
            }
        }
        // If there were no errors or no images, delete the post
        if ($error_count === 0) {
            if (is_wp_error(wp_delete_post( $post->ID, true ))) {
                $error_count++;
            }
        }
        if ($error_count > 0) {
            icf_queue_notices(
                "Some images for Icon `{$icon_id}` could not be deleted.",
                'error'
            );
        }
    }
    return $error_count === 0;
}

/**
 * Trash all icons in iconset AND the iconset itself.
 * @param <integer> $iconset_id
 */
function delete_icons_in_iconset($iconset_id) {
    $posts = get_icons_by_iconset_id($iconset_id);
    $iconset_post = get_post_by_iconset_id($iconset_id);
    if (! count( $posts )) {
       return true; 
    }
    $error_count = 0;
    foreach ( $posts as $post ) {
        // Try to delete the images first.
        $images = get_attached_media( 'image', $post->ID );
        if (count($images)) {
            foreach ($images as $image) {
                if (is_wp_error(wp_delete_post( $image->ID, true ))) {
                    $error_count++;
                }
            }
        }
        // If there are no errors or no images, delete the post.
        if ($error_count === 0) {
            if (is_wp_error( wp_delete_post( $post->ID, true ))) {
                $error_count++;
            }
        }
    }
    if ($error_count > 0) {
        icf_queue_notices(
            "Some images for Iconset `{$iconset_post->post_title}` could not be deleted.",
            'error'
        );
    }
    return $error_count === 0;
}

/**
 * Update a post with keyed array.
 * @param type $post
 * @param type $updates_array
 * @return type
 */
function update_post($post, $updates_array) {
    $result = null;
    if (is_object($post) && isset($post->ID)) {
        $updates_array['ID'] = $post->ID;
        $result = wp_update_post($updates_array, true);
        if (is_wp_error($result)) {
            icf_queue_notices($result->get_error_messages(), 'error');
        }
    }
    return $result;
}

/**
 * Saves error message strings as transient to be displayed by action callback.
 * @param <mixed> $notices
 */
function icf_queue_notices($notices, $type='success') {
    if (! is_array($notices)) {
        $notices = array($notices);
    }
    $message = "";
    foreach ($notices as $notice) {
        $message .= "{$notice}<br/>";
    }
    return set_transient( ICF_PLUGIN_NAME . '_' . $type, $message, HOUR_IN_SECONDS );
}

/**
 * Get a single iconset matching the iconset id
 * @staticvar array $iconsets
 * @param <integer> $iconset_id
 * @return <array>
 */
function get_one_iconset($iconset_id) {
    static $iconsets = array();
    if (! count($iconsets)) {
        $iconsets = iconfinder_call_api(
            Iconfinder_Portfolio_Public::get_api_url(array())
        );
    }
    $result = null;
    foreach ($iconsets['items'] as $iconset) {
        if ($iconset['iconset_id'] == $iconset_id) {
            $result = $iconset;
        }
    }
    return $result;
}

/**
 * Get a single post by custom field value.
 * @param <mixed> $meta_value
 * @param <string> $meta_key
 * @param <string> $post_type
 * @return <object>
 */
function icf_get_post($meta_value, $meta_key, $post_type) {
    $result = null;
    $posts = get_posts(array(
        'numberposts'	=> 1,
        'post_type'		=> $post_type,
        'meta_key'      => $meta_key,
        'meta_value'    => $meta_value
    ));
    if (count($posts)) {
        $result = $posts[0];
        $result->$meta_key = $meta_value;
    }
    return $result;
}

/**
 * Get all posts by custom field value.
 * @param <mixed> $meta_value
 * @param <string> $meta_key
 * @param <string> $post_type
 * @return <object>
 */
function icf_get_all_posts($meta_value, $meta_key, $post_type) {
    $posts = get_posts(array(
        'numberposts'	=> -1,
        'post_type'		=> $post_type,
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
 * @param <integer> $iconset_id
 * @return <array>
 */
function get_icons_by_iconset_id($iconset_id) {
    return icf_get_all_posts($iconset_id, 'iconset_id', 'icon');
}

/**
 * Creates the new terms for an icon or iconset.
 * @param array $terms
 * @param string $taxonomy
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
 */
function icf_set_tags($post_id, $tags) {
    create_new_terms($tags, 'icon_tag');
    wp_set_post_terms( $post_id, $tags, 'icon_tag', false);
}

/**
 * Set the categories for a post. Creates them if they don't already exist.
 * @param integer $post_id
 * @param array $categories array('identifier' => string, 'name' => string)
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
    $result = wp_set_post_terms( $post_id, $post_categories, 'icon_category', false);
    if (is_wp_error($result)) {
        icf_queue_notices(
            $result->get_error_messages(),
            'error'
        );
    }
}

/**
 * Splits a character-delimited string into words.
 * @param string $str
 * @param string $delim
 * @return array
 */
function str_to_words($str, $delim='-') {
    return array_map('trim', explode($delim, $str));
}

/**
 * Add JS onclick to a delete button/link.
 * 
 * @since 1.1.0
 */
function onclick_confirm_delete() {
    echo ' onclick="return confirm(\'' . ICF_CONFIRM_DELETE . '\');"';
}

/**
 * Add JS onclick to a the Update button.
 * 
 * @since 1.1.0
 */
function onclick_confirm_update() {
    echo ' onclick="return confirm(\'' . ICF_CONFIRM_UPDATE . '\');"';
}



/**
 * Show a success notice.
 * @param <string> $live
 */
function icf_success_notice($live='') {
    $message = $live;
    if (empty($message)) {
        $message = get_transient( ICF_PLUGIN_NAME . '_success' );
    }
    delete_transient( ICF_PLUGIN_NAME . '_success' );
    
    if (! empty($message)) {
    ?>
    <div class="updated notice">
        <p><?php _e( $message, ICF_PLUGIN_NAME ); ?></p>
    </div>
    <?php
    }
}
add_action( 'admin_notices' , 'icf_success_notice' );

/**
 * Show an error notice.
 * @param <string> $live Message to show now
 */
function icf_error_notice($live='') {
    $message = $live;
    if (empty($message)) {
        $message = get_transient( ICF_PLUGIN_NAME . '_error' );
    }
    delete_transient( ICF_PLUGIN_NAME . '_error' );

    if (! empty($message)) {
    ?>
    <div class="error notice">
        <p><?php _e( $message, ICF_PLUGIN_NAME ); ?></p>
    </div>
    <?php
    }
}
add_action( 'admin_notices' , 'icf_error_notice' );