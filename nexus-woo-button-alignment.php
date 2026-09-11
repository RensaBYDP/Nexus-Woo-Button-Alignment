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
 * Author:               Renier
 * Author URI:           https://rensa.co.za
 * License:              GPL-2.0-or-later
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          nexus-woo-button-alignment
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
function activate_nexus_wba()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-Nexus-Woo-Button-Alignment-activator.php';
    Nexus_WBA_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_nexus_wba()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-Nexus-Woo-Button-Alignment-deactivator.php';
    Nexus_WBA_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_nexus_wba');
register_deactivation_hook(__FILE__, 'deactivate_nexus_wba');

/**
 * Load the core plugin class.
 */
require plugin_dir_path(__FILE__) . 'includes/class-Nexus-Woo-Button-Alignment.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_nexus_wba()
{

    $plugin = new Nexus_WBA();
    $plugin->run();
}
run_nexus_wba();
