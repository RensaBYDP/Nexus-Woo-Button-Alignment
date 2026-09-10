<?php

/**
 * Plugin Name: Nexus Woo Button Alignment
 * Plugin URI: https://github.com/RensaBYDP/Nexus-Woo-Button-Alignment
 * Description: Automatically aligns WooCommerce buttons on shop, category, and product loop pages for a cleaner and more consistent layout.
 * Version: 1.0.0
 * Author: Rensa, Build Your Digital Playground
 * Author URI: https://github.com/RensaBYDP
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

define('NEXUS_WOO_BUTTON_ALIGNMENT_VERSION', '1.0.3');
define('NEXUS_WOO_BUTTON_ALIGNMENT_FILE', __FILE__);

require_once plugin_dir_path(__FILE__) . 'includes/class-nexus-woo-updater.php';

class Nexus_Woo_Button_Alignment
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        $this->init_updater();
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            'nexus-woo-button-alignment',
            plugin_dir_url(__FILE__) . 'assets/style.css',
            array(),
            NEXUS_WOO_BUTTON_ALIGNMENT_VERSION
        );
    }

    protected function init_updater()
    {
        new Nexus_Woo_Updater(
            __FILE__,
            'RensaBYDP',
            'Nexus-Woo-Button-Alignment',
            NEXUS_WOO_BUTTON_ALIGNMENT_VERSION
        );
    }
}

new Nexus_Woo_Button_Alignment();
