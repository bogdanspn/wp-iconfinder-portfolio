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
 * Make the API call
 */
function iconfinder_call_api($api_url, $cache_key='') {

    $response = null;
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
        
        if (trim($cache_key) != '') {
        	update_option( "iconfinder_portfolio_{$cache_key}", $response );
        }
    }
    catch(Exception $e) {
        throw new Exception($e);
    }
    
    if ($response == null && trim($cache_key) != '') {
        $response = get_option( "iconfinder_portfolio_{$cache_key}" );
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
function icfp_dump($what) {
    die ('<pre>' . print_r($what, true) . '</pre>');
}