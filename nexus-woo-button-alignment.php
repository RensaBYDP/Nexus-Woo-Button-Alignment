<?php

/**
 * The plugin bootstrap file for Nexus-Woo-Button-Alignment.
 *
 * This file loads plugin metadata, dependencies, activation/deactivation
 * handlers, and starts the plugin execution.
 *
 * @link              https://rensa.co.za
 * @since             1.0.0
 * @package           Nexus_Woo_Button_Alignment
 *
 * @wordpress-plugin
 * Plugin Name:          Nexus-Woo-Button-Alignment
 * Plugin URI:           https://rensa.co.za
 * Description:          A lightweight plugin that aligns WooCommerce product buttons for a clean, consistent grid layout.
 * Version:              1.0.1
 * Author:               Rensa Nexus
 * Author URI:           https://rensa.co.za
 * Requires at least:    5.0
 * Requires PHP:         7.4
 * Requires Plugins:     woocommerce
 * License:              GPL-2.0-or-later
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          nexus-woo-button-alignment-main
 * Domain Path:          /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('NEXUS_WBA_VERSION', '1.0.1');

/**
 * The code that runs during plugin activation.
 */
function nexus_wba_activate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-Nexus-Woo-Button-Alignment-activator.php';
    Nexus_WBA_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function nexus_wba_deactivate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-Nexus-Woo-Button-Alignment-deactivator.php';
    Nexus_WBA_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'nexus_wba_activate');
register_deactivation_hook(__FILE__, 'nexus_wba_deactivate');

/**
 * Load the core plugin class.
 */
require plugin_dir_path(__FILE__) . 'includes/class-Nexus-Woo-Button-Alignment.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function nexus_wba_run()
{
    if (! class_exists('WooCommerce')) {
        if (is_admin()) {
            add_action('admin_notices', 'nexus_wba_missing_woocommerce_notice');
        }
    }

    $plugin = new Nexus_WBA();
    $plugin->run();
}

/**
 * Display an admin notice when WooCommerce is unavailable.
 */
function nexus_wba_missing_woocommerce_notice()
{
    echo '<div class="notice notice-error"><p>' . esc_html__('nexus-woo-button-alignment requires WooCommerce to be installed and active.', 'Nexus-Woo-Button-Alignment-main') . '</p></div>';
}

add_action('plugins_loaded', 'nexus_wba_run', 20);
