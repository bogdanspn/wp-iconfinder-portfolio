<?php

# if (! defined('WP_INC')) 
#    die('Nothing to see here.');

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
 * @param array $attrs
 * @param array $defaults
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
 * @since 1.0.0
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
    
function add_iconfinder_meta_boxes() {
    
    add_meta_box( 'collections_metabox', 'Iconsets', 'show_collections_metabox', 'collection', 'side', 'high');
    add_meta_box( 'iconsets_metabox', 'Collection', 'show_iconsets_metabox', 'iconset', 'side', 'high' );
    add_meta_box( 'icons_metabox', 'Iconset', 'show_icons_metabox', 'icon', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'add_iconfinder_meta_boxes' );

/**
 * Show the collections metabox on the edit post page.
 * @param object $post
 * 
 * @since 1.1.0
 */
function show_collections_metabox($post) {
  
    // TODO: Add collections metabox data.
}

/**
 * Show the iconsets metabox on the edit post page.
 * @param object $post
 * 
 * @since 1.1.0
 */
function show_iconsets_metabox($post) {
  
    $iconset_id = get_post_meta( $post->ID, 'iconset_id', true );
    $iconset_identifier = get_post_meta( $post->ID, 'iconset_identifier', true );
    $latest_sync = get_post_meta( $post->ID, 'latest_sync', true );
    $icons_count = get_post_meta( $post->ID, 'icons_count', true );
    if (! empty($iconset_id)) {
        echo "<p><strong>Iconset:</strong> {$iconset_identifier} (ID: {$iconset_id})</p>";
        echo "<p><strong>Icons Count:</strong> {$icons_count}</p>";
        echo "<p><a href=\"https://www.iconfinder.com/iconsets/{$iconset_id}\" target=\"_blank\">View on Iconfinder</a></p>";
        echo "<p><strong>Last Sync:</strong> $latest_sync</p>";
    }
    else {
        //TODO: Show defeault message
    }
}

/**
 * Show the icons metabox on the edit post page.
 * @param object $post
 * 
 * @since 1.1.0
 */
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
    /**
     * Tests a mixed variable for true-ness.
     * @param mixed $value
     * @return boolean
     * 
     * @since 1.0.0
     */
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
 * @param string $iconset_id
 * @retrn \WP_Post or \WP_Error
 * 
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
 * @retrn \WP_Post or \WP_Error
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
 * @retrn \WP_Post or \WP_Error
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
 * Get the icons for an iconset from an API call.
 * @param string $identifier
 * @return array
 * 
 * @since 1.1.0
 */
function get_icons_from_api($identifier) {

    $result = null;
    $data = iconfinder_call_api(
        Iconfinder_Portfolio_Public::get_api_url(array(
            'iconset' => $identifier
        ))
    );
    if (is_array($data) && isset($data['items'])) {
        $result = scrub_icons_list($data['items']);
    }
    else {
        $result = icf_append_error(
            $result, 
            null, 
            "Iconfinder API says - Icons for {$identifier} Not Found"
        );
    }
    return $result;
}

/**
 * Imports all previews for an icon.
 * @param array $iconset
 * @return array or \WP_Error
 * 
 * @since 1.1.0
 */
function import_icon_previews($iconset) {
    
    $result = null;
    
    // If we don't have the iconset_id, we can't do anything and 
    // something was done wrong before calling this method.
    if (! isset($iconset['iconset_id']) || empty($iconset['iconset_id'])) {
        return icf_append_error(
            $result, 
            null, 
            "Could not import iconset previews. Iconset not well-formed"
        );
    }
    
    $icons = get_icons_from_api($iconset['identifier']);

    // If there are no icons, nothing to do.
    if (! count($icons)) {
        return;
    }

    $import_sizes = icf_get_option('icon_preview_sizes');
    # icf_dump($import_sizes);
    $default_preview_size = icf_get_setting('icon_default_preview_size');

    $post_ids = array();
    foreach ($icons as $icon) {

        $icon_post_id = create_icon_post($icon, $iconset);
        $icon_post    = get_post($icon_post_id);
        $alt_text     = ucwords(implode(' ', $icon['tags'])) . ' Preview';

        if (is_wp_error($icon_post_id)) {
            $result = icf_append_error($result, $icon_post_id);
        }
        else {
            
            $post_ids[] = $icon_post_id;
            add_icon_post_meta($icon_post_id, $iconset, $icon);

            // Loop through the allowed sizes and attach them if they exist.
            foreach ($import_sizes as $key) {
                if (! is_array($icon)) { continue; }
                if (! isset($icon['previews'])) { continue; }
                if (! isset($icon['previews'][$key])) { continue; }
                if ($key === $default_preview_size) { continue; }

                $preview = $icon['previews'][$key];
                if (isset($preview['src']) && ! empty($preview['src'])) {
                    $src = media_sideload_image( $preview['src'], $icon_post_id, $alt_text, 'src' );
                    if (is_wp_error($src)) {
                        $result = icf_append_error($result, $src);
                    }
                    else {
                        $preview['src'] = $src;
                        add_post_meta( $icon_post_id, "preview-{$key}" , $preview['src'], true );
                        add_post_meta( $icon_post_id, "parent_post_id" , $iconset['post_id'], true );
                        $media = get_last_attachment($icon_post->ID);
                        update_post_meta($media->ID, '_wp_attachment_image_alt', $alt_text);
                    }
                }
            }
            
            // Set the featured image for the icon post.
            if (isset($icon['previews'][$default_preview_size])) {
                $preview = $icon['previews'][$default_preview_size];
                $src = media_sideload_image( $preview['src'], $icon_post_id, $alt_text, 'src' );
                add_post_meta( $icon_post_id, "preview-{$key}" , $preview['src'], true );
                add_post_meta( $icon_post_id, "parent_post_id" , $iconset['post_id'], true );
                $media = get_last_attachment($icon_post->ID);
                update_post_meta($media->ID, '_wp_attachment_image_alt', $alt_text);
                $thumb_id = set_preview($icon_post_id, $media);
                if (is_wp_error($thumb_id)) {
                    $result = icf_append_error($result, $thumb_id);
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
 * @param \WP_Post $image
 * @return integer or \WP_error
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
 * Upload and attach an image by URL
 * @param string $url
 * @param integer (optional) $post_id
 * @return integer or \WP_Error
 * 
 * @since 1.1.0
 */
function upload_and_attach_img($url, $post_id=null) {
    $filename = basename($url);
    $result = media_sideload_image( 
        $url, 
        $post_id, 
        "Icon preview image `{$filename}` for icon ID {$post_id}", 
        'src' 
    );
    if (! is_wp_error($result)) {
         add_post_meta( $post_id, "preview-{$post_id}" , $url, true );
    }
    return $result;
}

/**
 * Create a new iconset post.
 * @param type $attrs
 * @return integer or \WP_Error
 * 
 * @since 1.1.0
 */
function create_iconset_post($iconset_id, $attrs=array()) {
    
    $result = null;
    
    // If the post already exists, throw an error and return.
    if (icf_post_exists($iconset_id)) {
        return icf_append_error(
            $result,
            null,
            "Iconset with ID `{$iconset_id}` already exists. Use update_iconset_post instead"
        );
    }

    // Get the iconset from the API data. If we do not find any data 
    // from the API, obviously something went wrong, so return 
    // an error.
    
    $iconset = get_one_iconset($iconset_id);    

    if (! is_array($iconset) || ! isset($iconset['iconset_id'])) {
        return icf_append_error(
            $result,
            null,
            "Iconset with ID `{$iconset_id}` could not be found via the Iconfinder API"
        );
    }
    
    // We have all of the data we need so far, let's continue.
    
    // Try to insert the new iconset_post. If we can't insert the post, 
    // there is nothing else we can do set set the error and exit.
    
    $post_id = wp_insert_post(
        iconset_to_post($iconset)
    );
    $iconset_post = get_post($post_id);

    if (is_wp_error($post_id)) {
        $result = icf_append_error($result, $post_id);
    } 
    else {
        
        
        // Add the post_id to the Iconset data so we don't have to 
        // pass it as a separate argument while we are working on the 
        // impoart.
        
        $iconset['post_id'] = $post_id;        
        
        //TODO: if the collection doesn't exist, add it
        //TOD: add collection_id relationship field
        // @defered
        
        add_iconset_meta($post_id, $iconset);

        // Import the preview images

        // Loop through the allowed sizes and attach them if they exist.
        // $import_sizes = icf_get_setting('iconset_preview_sizes');
        
        $default_preview_size = icf_get_option(
            'iconset_preview_size', 
            icf_get_setting('iconset_default_preview_size')
        );
        
        //TODO: Consider importing more than a single iconset preview.
        
        // Let's convert the category name, which theoretically should 
        // be SEO-friendly to alt and title attributes to help 
        // improve the SEO of the preview image.
        
        $alt_text = '';
        foreach ($iconset['categories'] as $category) {
            $alt_text .= ' ' . ucwords($category['name']) ;
        }
        $alt_text .= ' Preview';
        
        // Import and set the featured image.
        
        $src = media_sideload_image( 
            ICONFINDER_CDN_URL . "data/iconsets/previews/{$default_preview_size}/{$iconset['identifier']}.png", 
            $post_id, 
            $alt_text, 
            'src'
        );
            
        // If there was an error we can't really continue with any 
        // further manipulation of the preview image attachment.
            
        if (is_wp_error($src)) {
            $result = icf_append_error($result, $src);
        }
        else {
            
            // Add the preview image URL as a custom metadata field.
            
            add_post_meta( $post_id, "preview-{$default_preview_size}" , $src, true );
            
            // Get the last attachment (the preview image) and set its
            // Alt tag to the one we created earlier.
            
            $media = get_last_attachment($iconset_post->ID);
            if (isset($media->ID)) {
                update_post_meta($media->ID, '_wp_attachment_image_alt', $alt_text);
            }
                
            // Set the featured image for the iconset post.
            
            $thumb_id = set_preview($post_id, get_last_attachment($post_id));
            if (is_wp_error($thumb_id)) {
                $result = icf_append_error($result, $thumb_id);
            }
        }
        
        // Import the icons and icon images. We want to capture the post_ids
        // of the icons that are imported so we can add their tags to 
        // the iconset so when you search iconsets, you are also, 
        // in effect, searching the metadata of the icons in the set.
		
        $icon_post_ids = import_icon_previews($iconset);
        
        // If the imports failed, we can't do anything else
        // with them, so skip it.
        
        if (is_wp_error($icon_post_ids)) {
            $result = icf_append_error($result, $icon_post_ids);
        }
        else {
            $iconset['tags'] = get_post_tags($icon_post_ids);
        }

        // Now we can add the tags of the individual icons to the iconset.
        
        add_iconset_terms($post_id, $iconset);
        
        if (! is_wp_error($result)) {
           $result = $post_id;
        }       
    }
    return $result;
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
        $terms = wp_get_post_terms($post_id, 'icon_tag');
        if (is_array($terms) && count($terms)) {
            foreach ($terms as $term) {
                if (! in_array($term->slug, $icon_tags)) {
                    $tags[] = $term->slug;
                }
            }
        }
    }
    return $tags;
}

/**
 * Create a new collection post.
 * @param type $attrs
 * @return integer or \WP_Error
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
 * @return integer or \WP_Error
 * 
 * @since 1.1.0
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
 * @return integer or \WP_Error
 * 
 * @since 1.1.0
 */
function update_iconset_post($iconset_id, $attrs) {    
    
    $result = null;
    $new_icon_ids = array();
    
    // API data
    
    $iconset      = get_one_iconset($iconset_id);
    $icons        = get_icons_from_api($iconset['identifier']);
    $icon_ids     = array_column($icons, 'icon_id');
    
    // WP Post imports
    
    $iconset_post = get_post_by_iconset_id($iconset_id);
    $icon_posts   = get_icons_by_iconset_id($iconset_id);

    // We do not have an iconset from the API, nothing we can do.
    
    if (empty($iconset)) {
        return icf_append_error($result, null, "No iconset was found to update");
    }
    
    // We do not have a local iconset post, we could create one,
    // but this is not the place. We cannot proceed.
    
    if (! is_object($iconset_post) || ! isset($iconset_post->ID)) {
        return icf_append_error($result, null, "No iconset_post was found to update");
    }
    
    // We have all the data we need to proceed.

    // First, update the WP iconset post.
    
    $result = update_post(
        get_post_by_iconset_id($iconset_id),
        iconset_to_post($iconset, $iconset_post)
    );
    
    // If we encounter an error, display the error and exit.
    
    if (is_wp_error($result)) {
        return $result;
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
        return icf_append_error(
            $result,
            null, 
            "No icons found for Iconset `{$iconset['identifier']}` with id `{$iconset_id}`"
        );
    }
    // We have some icons from the API.
    
    else {
        // Grab all of the icon posts.
        
        $icon_posts = get_icons_by_iconset_id($iconset_id);
        
        // Delete local copies that are no longer in the API results.
        
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
        
        // Loop through the API results
        // Add or update the remaining API results.
        
        foreach ($icons as $icon) {
            
            // See if we have a local copy.
            
            $icon_post = get_post_by_icon_id($icon['icon_id']);
            
            // We have a local copy, update it.
            
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
            update_post_tags($post_id, get_all_tags($new_icon_ids));
        }
    }
    return $result;
}

function update_post_tags($post_id, $new_tags) {
    $terms = wp_get_post_terms($post_id, 'icon_tag');
    if (is_array($terms) && count($terms)) {
        $old_tags = array();
        foreach ($terms as $term) {
            $old_tags[] = $tarm->slug;
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
            $result, 
            null, 
            "The post for Iconset ID {$iconset_id} could not be found"
        );
    }
    return $result;
}

 /**
  * Permanently delete an iconset post.
  * @param type $iconset_id
  * @return integer or \WP_Error
  * 
  * @since 1.1.0
  */
function delete_iconset_post($iconset_id) {
    
    $result = null;
    $post = get_post_by_iconset_id($iconset_id);
    if (is_object($post) && isset($post->ID)) {
        $attachments = get_all_attachments($post->ID);
        if (is_array($attachments) && count($attachments)) {
            foreach ($attachments as $attacment) {
                $delete = wp_delete_post( $attacment->ID, true );
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
            $result, 
            null, 
            "The post matching Iconset {$iconset_id} could not be deleted, because it was not found."
        );
    }
    if (! is_wp_error($result)) {
        $delete = delete_icons_in_iconset($iconset_id);
    }
    if (is_wp_error($delete)) {
        $result = icf_append_error($result, $delete);
    }
    if (! is_wp_error($result)) {
        $result = $post->ID;
    }
    return $result;
}

/**
 * Permanently delete a collection post.
 * @param integer $collection_id
 * @return integer or \WP_Error
 * 
 * @since 1.1.0
 */
function delete_collection_post($collection_id) {

    $result = null;
    $post = get_post_by_collection_id($collection_id);
    if (is_object($post) && isset($post->ID)) {
        $del_post_id = wp_delete_post( $post->ID, true );
        if (is_wp_error($del_post_id)) {
            $result = icf_append_error($result, $del_post_id);
        }
        else {
            $delete = delete_icons_in_iconset($iconset_id);
            if (is_wp_error($delete)) {
                $result = icf_append_error($result, $delete);
            }
        }
    }
    else {
        $result = icf_append_error(
            $result, 
            null, 
            "The post matching collection_id {$collection_id} could not be deleted, because it was not found."
        );
    }
    return $result;
}

/**
 * Permanently delete an icon post.
 * @param ingeger $icon_id
 * @return integer or \WP_Error
 * 
 * @since 1.1.0
 */
function delete_icon_post($icon_id) {
    
    $images = array();
    $result = null;
    $post = get_post_by_icon_id($icon_id);
    if (is_object($post) && isset($post->ID)) {
        $images = get_attached_media( 'image', $post->ID );
        
        // Try to delete the images first
        if (count($images)) {
            foreach ($images as $image) {
                $delete = wp_delete_post( $image->ID, true );
                if (is_wp_error($delete)) {
                    $result = icf_append_error($result, $delete);
                }
            }
        }
        // If there were no errors or no images, delete the post
        if (! is_wp_error($result)) {
            $delete = wp_delete_post( $post->ID, true );
            if (is_wp_error($delete)) {
                $result = icf_append_error($result, $delete);
            }
        }
    }
    else {
        $result = icf_append_error(
            $result, 
            null, 
            "Icon post matching icon_id `{$icon_id}` could not be found."
        );
    }
    return $result;
}

/**
 * Trash all icons in iconset AND the iconset itself.
 * @param integer $iconset_id
 * @return boolean or \WP_Error
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
        // Try to delete the images first.
        $images = get_attached_media( 'image', $post->ID );
        if (count($images)) {
            foreach ($images as $image) {
                $delete = wp_delete_post( $image->ID, true );
                if (is_wp_error($delete)) {
                    $result = icf_append_error($result, $delete);
                }
            }
        }
        // If there are no errors or no images, delete the icon post.
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
        $result = icf_append_error($result, null, "No post to update");
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
        'numberposts'	=> 1,
        'post_type'		=> $post_type,
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
            "Post with `{$meta_key}={$meta_value}` could not be found."
        );
    }
    return $result;
}

/**
 * Get all posts by custom field value.
 * @param mixed $meta_value
 * @param string $meta_key
 * @param string $post_type
 * @return object
 * 
 * @since 1.1.0
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
 * @param integer $iconset_id
 * @return array
 * 
 * @since 1.1.0
 */
function get_icons_by_iconset_id($iconset_id) {
    return icf_get_all_posts($iconset_id, 'iconset_id', 'icon');
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
    
    $term_ids[] = icf_set_categories( $post_id, $icon['categories']);
    $term_ids[] =  icf_set_tags( $post_id, $icon['tags']);
    
    return array(
        'meta_ids' => $meta_ids,
        'term_ids' => $term_ids
    );
}

/**
 * Adds iconset metadata.
 * @param integer $post_id
 * @param array $iconset
 */
function add_iconset_meta($post_id, $iconset) {
    add_post_meta( $post_id, 'iconset_id', $iconset['iconset_id'], true );
    add_post_meta( $post_id, 'iconset_identifier', $iconset['identifier'], true );
    add_post_meta( $post_id, 'latest_sync', date('Y-m-d H:i:s'), true );
    add_post_meta( $post_id, 'icons_count', $iconset['icons_count'] );
    add_post_meta( $post_id, 'iconset_type', $iconset['type'] );
    if ( isset($iconset['prices']) && is_array($iconset['prices']) ) {
        if ( isset( $iconset['prices'][0]['price'] ) ) {
            add_post_meta( $post_id, 'price', $iconset['prices'][0]['price'], true );
        }
    }
    if ( isset($iconset['prices']) && is_array($iconset['prices']) ) {
        if ( isset( $iconset['prices'][0]['license'] ) ) {
            $license = $iconset['prices'][0]['license'];
            add_post_meta( $post_id, 'license_url', $license['url'], true );
            add_post_meta( $post_id, 'license_name', $license['name'], true );
        }
    }
    if ( isset($iconset['styles']) && is_array($iconset['styles']) ) {
        $style = $iconset['styles'][0];
        foreach ($style as $key => $value) {
            add_post_meta( $post_id, "iconset_style_{$key}", $value, true );
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
 * 
 * @since 1.1.0
 */
function icf_set_tags($post_id, $tags) {
    create_new_terms($tags, 'icon_tag');
    wp_set_post_terms( $post_id, $tags, 'icon_tag', false);
}

/**
 * Set the categories for a post. Creates them if they don't already exist.
 * @param integer $post_id
 * @param array $categories array('identifier' => string, 'name' => string)
 * @return array or \WP_Error
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

function setup_search_posts() {
    global $wp_the_query;
    $wp_the_query->posts = icf_setup_posts($wp_the_query->posts);
}
# add_action('wp', 'setup_search_posts');