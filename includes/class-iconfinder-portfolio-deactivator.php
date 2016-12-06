<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Fired during plugin deactivation
 *
 * @link       http://iconify.it
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/includes
 * @author     Scott Lewis <scott@iconify.it>
 */
class Iconfinder_Portfolio_Deactivator {

    /**
     * Deactivate the plugin
     *
     * Deactivates the plugin and removes all custom post types and stored data.
     *
     * @since    1.0.0
     */
    public static function deactivate() {

        self::remove_custom_post_types();
        self::remove_iconfinder_data();
        self::remove_cache_data();
        
        unregister_setting( ICF_PLUGIN_NAME, ICF_PLUGIN_NAME);
    }

    /**
     * Remove the Custom post types.
     * @since 1.1.0
     */
    public static function remove_custom_post_types() {
        
        unregister_post_type( 'colletions' );
        unregister_post_type( 'iconsets' );
        unregister_post_type( 'icons' );
    }

    /**
     * Removes stored data from the plugin.
     * @sincer 1.1.0
     */
    public static function remove_iconfinder_data() {
        
    }

    /**
     * Removes any cached data.
     * @sincer 1.1.0
     */
    public static function remove_cache_data() {
        $cache_keys = icf_get_cache_keys();
        
        foreach ( $cache_keys as $cache_key ) {
        
            delete_option( $cache_key );
        }
        
        update_option( 'icf_cache_keys', array() );
    }

}
