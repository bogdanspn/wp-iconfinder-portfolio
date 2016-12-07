<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://iconfinder.com
 * @since      1.0.0
 *
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Iconfinder_Portfolio
 * @subpackage Iconfinder_Portfolio/includes
 * @author     Iconfinder <support@iconfinder.com>
 */
class Iconfinder_Portfolio {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Iconfinder_Portfolio_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'iconfinder-portfolio';
        $this->version = '2.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        define('ICF_PLUGIN_NAME',    $this->plugin_name);
        define('ICF_PLUGIN_VERSION', $this->version);

        $_options = get_option( ICF_PLUGIN_NAME );
        $plugin_mode = get_val($_options, 'plugin_mode', ICF_PLUGIN_MODE_DEFAULT);
        define('ICF_PLUGIN_MODE', $plugin_mode);

        define('ICF_PLUGIN_PATH',   plugin_dir_path( dirname( __FILE__ ) ));
        define('ICF_PUBLIC_PATH',   ICF_PLUGIN_PATH . 'public/');
        define('ICF_TEMPLATE_PATH', ICF_PLUGIN_PATH . 'public/partials/');
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Iconfinder_Portfolio_Loader. Orchestrates the hooks of the plugin.
     * - Iconfinder_Portfolio_i18n. Defines internationalization functionality.
     * - Iconfinder_Portfolio_Admin. Defines all hooks for the admin area.
     * - Iconfinder_Portfolio_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * Global settings file.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/iconfinder-portfolio-settings.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-iconfinder-portfolio-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-iconfinder-portfolio-i18n.php';

        /**
         * Global utility functions file.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-post-types.php';

        /**
         * Load the custom search engine.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-search-plus.php';

        /**
         * Global utility functions file.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/icf-theme.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/utils.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/iconfinder-portfolio-functions.php';

        /**
         * Data transformation functions file.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/transforms.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-iconfinder-portfolio-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-iconfinder-portfolio-public.php';

        $this->loader = new Iconfinder_Portfolio_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Iconfinder_Portfolio_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Iconfinder_Portfolio_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Iconfinder_Portfolio_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        $this->loader->add_action('admin_init', $plugin_admin, 'options_update');

        $this->loader->add_action( 'admin_post_iconsets_admin_pagination', $plugin_admin, 'iconsets_admin_pagination' );

        // Add the purge_cache form handler hook
        $this->loader->add_action( 'admin_post_purge_cache', $plugin_admin, 'purge_cache' );
        $this->loader->add_action( 'admin_post_update_iconset_data', $plugin_admin, 'process_iconset_admin_post' );

        // Add menu item
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );


        // Add Settings link to the plugin
        $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
        $this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Iconfinder_Portfolio_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'init', $plugin_public, 'load_search_engine' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Iconfinder_Portfolio_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}