<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://rensa.co.za
 * @since      1.0.1
 *
 * @package    Nexus_Woo_Button_Alignment
 * @subpackage Nexus_Woo_Button_Alignment/includes
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
 * @since      1.0.1
 * @package    Nexus_Woo_Button_Alignment
 * @subpackage Nexus_Woo_Button_Alignment/includes
 * @author     Rensa Nexus <support@rensa.co.za>
 */
class Nexus_WBA
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.1
     * @access   protected
     * @var      Nexus_WBA_Loader    $loader
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.1
     * @access   protected
     * @var      string    $plugin_name
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.1
     * @access   protected
     * @var      string    $version
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.1
     */
    public function __construct()
    {

        if (defined('NEXUS_WBA_VERSION')) {
            $this->version = NEXUS_WBA_VERSION;
        } else {
            $this->version = '1.0.3';
        }

        $this->plugin_name = 'nexus-woo-button-alignment';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.1
     * @access   private
     */
    private function load_dependencies()
    {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-Nexus-Woo-Button-Alignment-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-Nexus-Woo-Button-Alignment-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/Nexus-Woo-Button-Alignment-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/Class-Nexus-Woo-Button-Alignment-public.php';

        $this->loader = new Nexus_WBA_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @since    1.0.1
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Nexus_WBA_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_translations');
    }

    /**
     * Register admin hooks.
     *
     * @since    1.0.1
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Nexus_WBA_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_compatibility_page');
        $this->loader->add_action('admin_notices', $plugin_admin, 'display_compatibility_notice');
    }

    /**
     * Register public-facing hooks.
     *
     * @since    1.0.1
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Nexus_WBA_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Get the plugin name.
     *
     * @return string
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * Get the plugin version.
     *
     * @return string
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Register all plugin hooks with WordPress.
     */
    public function run()
    {
        $this->loader->run();
    }
}
