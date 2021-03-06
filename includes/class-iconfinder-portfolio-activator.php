<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Fired during plugin activation
 *
 * @link       http://iconfinder.com
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/includes
 * @author     Iconfinder <support@iconfinder.com>
 */
class Iconfinder_Portfolio_Activator {

    /**
     * Active the plugin.
     *
     * Activate the plugin and create the necessary content types for storing 
     * iconfinder porfolio data locally (to make is searchable).
     *
     * @since    1.1.0
     */
    public static function activate() {
        register_setting( 
            ICF_PLUGIN_NAME, 
            ICF_PLUGIN_NAME, 
            'register_default_settings'
        );
    }
}