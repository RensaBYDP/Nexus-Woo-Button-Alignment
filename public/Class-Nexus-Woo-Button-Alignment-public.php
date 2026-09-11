<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two example hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://rensa.co.za
 * @since      1.0.1
 *
 * @package    Nexus_Woo_Button_Alignment
 * @subpackage Nexus_Woo_Button_Alignment/public
 * @author     Rensa Nexus <support@rensa.co.za>
 */
class Nexus_WBA_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.1
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.1
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.1
     * @param    string    $plugin_name    The name of the plugin.
     * @param    string    $version        The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.1
     */
    public function enqueue_styles()
    {

        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/Nexus-Woo-Button-Alignment-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.1
     */
    public function enqueue_scripts()
    {

        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/Nexus-Woo-Button-Alignment-public.js',
            array('jquery'),
            $this->version,
            true
        );
    }

    /**
     * Add wrapper for woo-height script.
     *
     * @since    1.0.1
     */
    public function wooalign_product_link_open()
    {
        echo wp_kses_post('<div class="woo-height">');
    }

    public function wooalign_product_link_close()
    {
        echo wp_kses_post('</div>');
    }
}
