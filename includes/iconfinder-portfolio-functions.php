<?php

/**
 * This is a collection of utility functions used globally thruoghout the plugin.
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

/**
 * Determines api path from shortcode attrs
 * 
 * @since 1.0.0
 */
function attrs_to_api_path($username='', $collection='') {

    $api_path = "users/{$username}/iconsets";
    
    if ($collection != "") {
        $api_path = "collections/{$collection}/iconsets";
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
function iconfinder_call_api($api_url, $cache_key='') {

    $response = null;
    
    // Always try the local cache first. If we get a hit, just return the stored data.
    
    if ( $response = get_option( $cache_key ) ) {
        
        $response['from_cache'] = 1;
        return $response;
    }
    
    // If there is no cached data, make the API cale.
    
    try {
        $response = json_decode(
            wp_remote_retrieve_body(
                wp_remote_get( 
                    $api_url, 
                    array('sslverify' => ICONFINDER_API_SSLEVERIFY)
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
            unset($response['iconsets']);
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