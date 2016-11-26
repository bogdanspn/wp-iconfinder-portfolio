<?php

/* 
 * DO NOT edit this file unless you know what you are doing. 
 * Bad things will happen.
 */

/**
 * Adds the Collection custom post type.
 * @since 1.1.0
 */
function add_collections_post_type() {
    $scope = 'iconfinder-portfolio';
    $labels = array(
        'name'                => _x( 'Collections', 'Iconfinder Collections', $scope ),
        'singular_name'       => _x( 'Collection', 'Iconfinder Collection', $scope ),
        'menu_name'           => __( 'Iconfinder Collections', $scope ),
        'parent_item_colon'   => __( 'Parent Collection', $scope ),
        'all_items'           => __( 'Collections', $scope ),
        'view_item'           => __( 'View Collection', $scope ),
        'add_new_item'        => __( 'Add New Collection', $scope ),
        'add_new'             => __( 'Add New', $scope ),
        'edit_item'           => __( 'Edit Collection', $scope ),
        'update_item'         => __( 'Update Collection', $scope ),
        'search_items'        => __( 'Search Collection', $scope ),
        'not_found'           => __( 'Not Found', $scope ),
        'not_found_in_trash'  => __( 'Not found in Trash', $scope ),
    );

    $args = array(
        'label'               => __( 'iconfinder-collections', $scope ),
        'description'         => __( 'Iconfinder Collections', $scope ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments'),
        'taxonomies'          => array( 'icon_category', 'icon_tag' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => ICF_PLUGIN_NAME,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 0,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'collection', $args );   
}

/**
 * Adds the Iconset custom post type.
 * @since 1.1.0
 */
function add_iconsets_post_type() {
    $scope = 'iconfinder-portfolio';
    $labels = array(
        'name'                => _x( 'Iconsets', 'Iconfinder Iconsets', $scope ),
        'singular_name'       => _x( 'Iconset', 'Iconfinder Iconset', $scope ),
        'menu_name'           => __( 'Iconfinder Iconsets', $scope ),
        'parent_item_colon'   => __( 'Parent Collection', $scope ),
        'all_items'           => __( 'Iconsets', $scope ),
        'view_item'           => __( 'View Iconset', $scope ),
        'add_new_item'        => __( 'Add New Iconset', $scope ),
        'add_new'             => __( 'Add New', $scope ),
        'edit_item'           => __( 'Edit Iconset', $scope ),
        'update_item'         => __( 'Update Iconset', $scope ),
        'search_items'        => __( 'Search Iconset', $scope ),
        'not_found'           => __( 'Not Found', $scope ),
        'not_found_in_trash'  => __( 'Not found in Trash', $scope ),
    );

    $args = array(
        'label'               => __( 'iconfinder-iconsets', $scope ),
        'description'         => __( 'Iconfinder Iconsets', $scope ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'custom-fields'),
        'taxonomies'          => array( 'icon_category', 'icon_tag' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => ICF_PLUGIN_NAME,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 1,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'iconset', $args );
}
  
/**
 * Adds the Icon custom post type.
 * @sincer 1.1.0
 */
function add_icons_post_type() {
    $scope = 'iconfinder-portfolio';
    $labels = array(
        'name'                => _x( 'Icons', 'Iconfinder Icons', $scope ),
        'singular_name'       => _x( 'Icon', 'Iconfinder Icon', $scope ),
        'menu_name'           => __( 'Iconfinder Icons', $scope ),
        'parent_item_colon'   => __( 'Parent Iconset', $scope ),
        'all_items'           => __( 'Icons', $scope ),
        'view_item'           => __( 'View Icon', $scope ),
        'add_new_item'        => __( 'Add New Icon', $scope ),
        'add_new'             => __( 'Add New', $scope ),
        'edit_item'           => __( 'Edit Icon', $scope ),
        'update_item'         => __( 'Update Icon', $scope ),
        'search_items'        => __( 'Search Icon', $scope ),
        'not_found'           => __( 'Not Found', $scope ),
        'not_found_in_trash'  => __( 'Not found in Trash', $scope ),
    );

    $args = array(
        'label'               => __( 'iconfinder-icons', $scope ),
        'description'         => __( 'Iconfinder Icons', $scope ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'custom-fields'),
        'taxonomies'          => array( 'icon_category', 'icon_tag' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => ICF_PLUGIN_NAME,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 2,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'icon', $args );
}

/**
 * Register the custom taxonomies.
 */
function register_icon_taxonomies() {
    // Register the icon categories taxonomy
	register_taxonomy(
		'icon_category',
		array('icon', 'iconset', 'collection'),
		array(
			'label' => __( 'Iconfinder Categories' ),
			'rewrite' => array( 'slug' => 'icon-category' ),
			'capabilities' => array(
				'manage_terms' => 'manage_custom_tax',
                'edit_terms'   => 'manage_custom_tax',
                'delete_terms' => 'manage_custom_tax',
                'assign_terms' => 'edit_posts'
			),
            'show_ui' => true,
            'show_in_menu' => true,
            'hierarchical' => true,
            'show_admin_column' => true,
            'publicly_queryable' => true
		)
	);
    // Register the icon tags taxonomy
    register_taxonomy(
		'icon_tag',
		array('icon', 'iconset', 'collection'),
		array(
			'label' => __( 'Iconfinder Tags' ),
			'rewrite' => array( 'slug' => 'icon-tags' ),
			'capabilities' => array(
				'manage_terms' => 'manage_custom_tax',
                'edit_terms'   => 'manage_custom_tax',
                'delete_terms' => 'manage_custom_tax',
                'assign_terms' => 'edit_posts'
			),
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'publicly_queryable' => true
		)
	);
}
add_action( 'init', 'register_icon_taxonomies' );