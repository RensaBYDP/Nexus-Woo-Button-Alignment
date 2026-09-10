<?php

/**
 * Plugin Name: Nexus Woo Button Alignment
 * Plugin URI: https://github.com/yourname/Nexus-Woo-Button-Alignment
 * Description: Automatically aligns WooCommerce buttons on shop, category, and product loop pages for a cleaner and more consistent layout.
 * Version: 1.0.2
 * Author: Rensa, Build Your Digital Playground
 * Author URI: https://github.com/RensaBYDP
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

class Nexus_Woo_Button_Alignment
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            'nexus-woo-button-alignment',
            plugin_dir_url(__FILE__) . 'assets/style.css',
            array(),
            '1.0.0'
        );
    }
}

new Nexus_Woo_Button_Alignment();
