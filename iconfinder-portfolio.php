<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://iconify.it
 * @since             1.0.0
 * @package           Iconfinder_Portfolio
 *
 * @wordpress-plugin
 * Plugin Name:       Iconfinder Portfolio
 * Plugin URI:        http://iconfinder.com
 * Description:       A plugin for displaying content from the Iconfinder API in a WordPress site.
 * Version:           2.0.0
 * Author:            Scott Lewis
 * Author URI:        http://iconify.it
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iconfinder-portfolio
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-iconfinder-portfolio-activator.php
 */
function activate_iconfinder_portfolio() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-iconfinder-portfolio-activator.php';
    Iconfinder_Portfolio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-iconfinder-portfolio-deactivator.php
 */
function deactivate_iconfinder_portfolio() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-iconfinder-portfolio-deactivator.php';
    Iconfinder_Portfolio_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_iconfinder_portfolio' );
register_deactivation_hook( __FILE__, 'deactivate_iconfinder_portfolio' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-iconfinder-portfolio.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_iconfinder_portfolio() {

    $plugin = new Iconfinder_Portfolio();
    $plugin->run();

}
run_iconfinder_portfolio();