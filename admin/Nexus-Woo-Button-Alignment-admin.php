<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two example hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://rensa.co.za
 * @since      1.0.1
 *
 * @package    Nexus_Woo_Button_Alignment
 * @subpackage Nexus_Woo_Button_Alignment/admin
 * @author     Rensa Nexus <support@rensa.co.za>
 */
class Nexus_WBA_Admin
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
     * @param    string    $plugin_name    The name of this plugin.
     * @param    string    $version        The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.1
     */
    public function enqueue_styles()
    {

        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/Nexus-Woo-Button-Alignment-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.1
     */
    public function enqueue_scripts()
    {

        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/Nexus-Woo-Button-Alignment-admin.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * Add the compatibility page under WooCommerce.
     */
    public function add_compatibility_page()
    {
        add_submenu_page(
            'woocommerce',
            __('Woo Button Alignment', 'nexus-woo-button-alignment-main'),
            __('Button Alignment', 'nexus-woo-button-alignment-main'),
            'manage_woocommerce',
            'nexus-wba-compatibility',
            array($this, 'render_compatibility_page')
        );
    }

    /**
     * Display a notice when a required platform version is unsupported.
     */
    public function display_compatibility_notice()
    {
        if (! current_user_can('manage_woocommerce') || ! $this->has_compatibility_issue()) {
            return;
        }

        $page_url = admin_url('admin.php?page=nexus-wba-compatibility');

        printf(
            '<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
            esc_html__('Nexus-Woo-Button-Alignment needs your attention: one or more compatibility requirements are not met.', 'nexus-woo-button-alignment-main'),
            esc_url($page_url),
            esc_html__('View compatibility check', 'nexus-woo-button-alignment-main')
        );
    }

    /**
     * Render the compatibility page.
     */
    public function render_compatibility_page()
    {
        if (! current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('You do not have permission to view this page.', 'nexus-woo-button-alignment-main'));
        }

        $results = $this->get_compatibility_results();
        $summary = $this->get_health_summary($results);
        $layout_details = $this->get_layout_details();
        $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '';
        $checked_at = current_time('mysql');
        require plugin_dir_path(__FILE__) . 'partials/Nexus-Woo-Button-Alignment-admin-display.php';
    }

    /**
     * Return the current environment checks.
     *
     * @return array<int, array<string, string>>
     */
    private function get_compatibility_results()
    {
        $theme = wp_get_theme();
        $woocommerce_version = defined('WC_VERSION') ? WC_VERSION : '';
        $is_block_theme = function_exists('wp_is_block_theme') && wp_is_block_theme();

        return array(
            array(
                'component' => __('WordPress', 'nexus-woo-button-alignment-main'),
                'value'     => get_bloginfo('version'),
                'required'  => __('5.0 or newer', 'nexus-woo-button-alignment-main'),
                'status'    => version_compare(get_bloginfo('version'), '5.0', '>=') ? 'pass' : 'fail',
            ),
            array(
                'component' => __('PHP', 'nexus-woo-button-alignment-main'),
                'value'     => PHP_VERSION,
                'required'  => __('7.4 or newer', 'nexus-woo-button-alignment-main'),
                'status'    => version_compare(PHP_VERSION, '7.4', '>=') ? 'pass' : 'fail',
            ),
            array(
                'component' => __('WooCommerce', 'nexus-woo-button-alignment-main'),
                'value'     => $woocommerce_version ? $woocommerce_version : __('Not detected', 'nexus-woo-button-alignment-main'),
                'required'  => __('3.0 or newer', 'nexus-woo-button-alignment-main'),
                'status'    => $woocommerce_version && version_compare($woocommerce_version, '3.0', '>=') ? 'pass' : 'fail',
            ),
            array(
                'component' => __('Active theme', 'nexus-woo-button-alignment-main'),
                'value'     => $theme->get('Name') . ' ' . $theme->get('Version'),
                'required'  => $is_block_theme ? __('Block theme detected', 'nexus-woo-button-alignment-main') : __('Classic theme detected', 'nexus-woo-button-alignment-main'),
                'status'    => 'info',
            ),
        );
    }

    /**
     * Determine whether a required compatibility check has failed.
     *
     * @return bool
     */
    private function has_compatibility_issue()
    {
        foreach ($this->get_compatibility_results() as $result) {
            if ('fail' === $result['status']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Summarize the compatibility results for the dashboard header.
     *
     * @param array<int, array<string, string>> $results Compatibility results.
     * @return array<string, string|int>
     */
    private function get_health_summary($results)
    {
        $failed = 0;

        foreach ($results as $result) {
            if ('fail' === $result['status']) {
                $failed++;
            }
        }

        $total = count($results);

        return array(
            'status' => $failed ? 'warning' : 'pass',
            'label'  => $failed ? __('Needs attention', 'nexus-woo-button-alignment-main') : __('Ready to use', 'nexus-woo-button-alignment-main'),
            'passed' => $total - $failed,
            'total'  => $total,
        );
    }

    /**
     * Describe the product layouts supported by the frontend assets.
     *
     * @return array<int, array<string, string>>
     */
    private function get_layout_details()
    {
        return array(
            array(
                'name'     => __('Classic WooCommerce grids', 'nexus-woo-button-alignment-main'),
                'selector' => 'ul.products > li.product',
                'status'   => __('Supported', 'nexus-woo-button-alignment-main'),
            ),
            array(
                'name'     => __('Product template blocks', 'nexus-woo-button-alignment-main'),
                'selector' => '.wc-block-product-template > li',
                'status'   => __('Supported', 'nexus-woo-button-alignment-main'),
            ),
            array(
                'name'     => __('Legacy product grid blocks', 'nexus-woo-button-alignment-main'),
                'selector' => '.wc-block-grid__products > li.wc-block-grid__product',
                'status'   => __('Supported', 'nexus-woo-button-alignment-main'),
            ),
        );
    }
}
