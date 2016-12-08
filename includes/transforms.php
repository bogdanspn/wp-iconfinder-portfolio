<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Converts an API iconset array to a WP post update array.
 * @param array $iconset
 * @param object $post
 * @return array
 */
function iconset_to_post($iconset, $post=null, $collection=null) {

    $default_content  = get_val($iconset, 'name', 'Untitled Iconset description');
    $post_title       = get_val($post, 'post_title', 'Untitled Iconset');
    $icon_post_title  = get_val($iconset, 'name', $post_title);
    $iconset_id       = get_val($iconset, 'iconset_id', null);
    $identifier       = get_val($iconset, 'identifier', $iconset_id);

    return array(
        'ID'             => get_val($iconset, 'ID', null),
        'post_title'     => $icon_post_title,
        'post_content'   => get_val($iconset, 'description', $default_content),
        'post_type'      => 'iconset',
        'post_status'    => 'publish',
        'post_author'    => get_current_user_id(),
        'comment_status' => '',
        'ping_status'    => '',
        'post_password'  => '',
        'to_ping'        =>  '',
        'pinged'         => '',
        'post_parent'    => get_val($collection, 'post_id', 0),
        'menu_order'     => 0,
        'guid'           => ICONFINDER_LINK_ICONSETS . $identifier,
        'import_id'      => 0,
        'context'        => '',
        'post_exceprt'   => '',
        'iconset_id'     => $iconset_id
    );
}

/**
 * Converts an API collection array to a WP post update array.
 * @param array $iconset
 * @return array
 */
function collection_to_post($collection) {
    return array(
        'ID'             => get_val($collection, 'collection_id', null),
        'post_title'     => get_val($collection, 'name', 'Untitled Collection'),
        'post_content'   => get_val($collection, 'description'),
        'post_type'      => 'collection',
        'post_status'    => 'publish',
        'post_author'    => get_current_user_id(),
        'comment_status' => '',
        'ping_status'    => '',
        'post_password'  => '',
        'to_ping'        =>  '',
        'pinged'         => '',
        'post_parent'    => 0,
        'menu_order'     => 0,
        'guid'           => '',
        'import_id'      => 0,
        'context'        => '',
        'post_exceprt'   => ''
    );
}

/**
 * Converts an API response array to a WP post update array.
 * @param array $icon
 * @param object $post
 * @param array $iconset
 * @return array
 */
function icon_to_post($icon, $post=null, $iconset=null) {
    
    $post_title = "Icon {$icon['icon_id']}";
    
    if (! empty($iconset) && isset($iconset['identifier']) ) {
        $post_title = "{$iconset['identifier']} - Icon {$icon['icon_id']}";
    }
    
    $post_content = get_val( $post, 'post_content', $post_title );

    $parent_permalink = null;
    $post_id = get_val($iconset, 'post_id', 0);
    if ($post_id) {
        $parent_permalink = get_the_permalink($post_id);
    }
    
    return array(
        'ID'             => get_val($post, 'ID'),
        'post_title'     => get_val( $post, 'post_title', $post_title ),
        'post_content'   => get_val($icon, 'description', $post_content),
        'post_type'      => 'icon',
        'post_status'    => 'publish',
        'post_author'    => get_current_user_id(),
        'comment_status' => '',
        'ping_status'    => '',
        'post_password'  => '',
        'to_ping'        =>  '',
        'pinged'         => '',
        'post_parent'    => get_val($iconset, 'post_id', 0),
        'menu_order'     => 0,
        'guid'           => $parent_permalink,
        'import_id'      => 0,
        'context'        => '',
        'post_exceprt'   => '',
        'icon_id'        => get_val($icon, 'icon_id')
    );
}