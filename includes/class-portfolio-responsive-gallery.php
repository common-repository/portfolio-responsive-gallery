<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Portfolio_Responsive_Gallery
 * @subpackage Portfolio_Responsive_Gallery/includes
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
 * @package    Portfolio_Responsive_Gallery
 * @subpackage Portfolio_Responsive_Gallery/includes
 * @author     Portfolio Team <info@ays-pro.com>
 */
class Portfolio_Responsive_Gallery {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Portfolio_Responsive_Gallery_Loader    $loader    Maintains and registers all hooks for the plugin.
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
        if (defined('PRG_NAME_VERSION')) {
            $this->version = PRG_NAME_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'portfolio-responsive-gallery';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Portfolio_Responsive_Gallery_Loader. Orchestrates the hooks of the plugin.
     * - Portfolio_Responsive_Gallery_i18n. Defines internationalization functionality.
     * - Portfolio_Responsive_Gallery_Admin. Defines all hooks for the admin area.
     * - Portfolio_Responsive_Gallery_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        if (!class_exists('WP_List_Table')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
        }

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-portfolio-responsive-gallery-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-portfolio-responsive-gallery-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-portfolio-responsive-gallery-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-portfolio-responsive-gallery-public.php';

        /*
         * The class is responsible for showing portfolios in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/lists/class-portfolio-responsive-gallery-list-table.php';

        /*
         * The class is responsible for showing portfolio attribures in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/lists/class-portfolio-responsive-gallery-attributes-list-table.php';

        $this->loader = new Portfolio_Responsive_Gallery_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Portfolio_Responsive_Gallery_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Portfolio_Responsive_Gallery_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Portfolio_Responsive_Gallery_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Add menu item
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');


        $this->loader->add_action('wp_ajax_ays_get_attr_for_project', $plugin_admin, 'ays_get_attr_for_project');
        $this->loader->add_action('wp_ajax_nopriv_ays_get_attr_for_project', $plugin_admin, 'ays_get_attr_for_project');

        // Add Settings link to the plugin
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');
        $this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Portfolio_Responsive_Gallery_Public($this->get_plugin_name(), $this->get_version());

        //$this->loader->add_action('init', $plugin_public, 'ays_initialize_portfolio_shortcode');
	    $this->loader->add_action('wp_ajax_ays_portfolio_load_project', $plugin_public, 'ays_portfolio_load_project');
	    $this->loader->add_action('wp_ajax_nopriv_ays_portfolio_load_project', $plugin_public, 'ays_portfolio_load_project');

	    $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
	    $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');


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
     * @return    Portfolio_Responsive_Gallery_Loader    Orchestrates the hooks of the plugin.
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
