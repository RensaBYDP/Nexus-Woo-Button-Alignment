<?php

/**
 * Plugin Name: Nexus Woo Button Alignment
 * Plugin URI: https://github.com/RensaBYDP/Nexus-Woo-Button-Alignment
 * Description: Automatically aligns WooCommerce buttons on shop, category, and product loop pages for a cleaner and more consistent layout.
 * Version: 1.0.1
 * Author: Rensa, Build Your Digital Playground
 * Author URI: https://github.com/RensaBYDP
 * Requires at least: 5.0
 * Tested up to: 6.7
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('NEXUS_WOO_BUTTON_ALIGNMENT_VERSION')) {
    define('NEXUS_WOO_BUTTON_ALIGNMENT_VERSION', '1.0.1');
}

if (!defined('NEXUS_WOO_BUTTON_ALIGNMENT_FILE')) {
    define('NEXUS_WOO_BUTTON_ALIGNMENT_FILE', __FILE__);
}

if (!class_exists('Nexus_Woo_Updater')) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-nexus-woo-updater.php';
}

if (!class_exists('Nexus_Woo_Button_Alignment')) {
    class Nexus_Woo_Button_Alignment
    {
        public function __construct()
        {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
            $this->init_updater();
        }

        public function enqueue_styles()
        {
            if (!class_exists('WooCommerce')) {
                return;
            }

            $is_product_loop = false;

            if (function_exists('is_shop') && is_shop()) {
                $is_product_loop = true;
            }

            if (function_exists('is_product_category') && is_product_category()) {
                $is_product_loop = true;
            }

            if (function_exists('is_product_tag') && is_product_tag()) {
                $is_product_loop = true;
            }

            if (function_exists('is_product_taxonomy') && is_product_taxonomy()) {
                $is_product_loop = true;
            }

            if (function_exists('is_post_type_archive') && is_post_type_archive('product')) {
                $is_product_loop = true;
            }

            if (function_exists('is_tax') && is_tax(array('product_cat', 'product_tag'))) {
                $is_product_loop = true;
            }

            if (!$is_product_loop) {
                return;
            }

            wp_enqueue_style(
                'nexus-woo-button-alignment',
                plugin_dir_url(__FILE__) . 'assets/style.css',
                array(),
                NEXUS_WOO_BUTTON_ALIGNMENT_VERSION
            );
        }

        protected function init_updater()
        {
            if (!class_exists('Nexus_Woo_Updater')) {
                return;
            }

            new Nexus_Woo_Updater(
                __FILE__,
                'RensaBYDP',
                'Nexus-Woo-Button-Alignment',
                NEXUS_WOO_BUTTON_ALIGNMENT_VERSION
            );
        }
    }
}

add_action('plugins_loaded', function () {
    if (!class_exists('WooCommerce')) {
        return;
    }

    if (!class_exists('Nexus_Woo_Button_Alignment')) {
        return;
    }

    new Nexus_Woo_Button_Alignment();
}, 20);
