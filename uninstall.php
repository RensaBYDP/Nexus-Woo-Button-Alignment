<?php

/**
 * Uninstall handler for Nexus-Woo-Button-Alignment.
 *
 * This file is executed when the plugin is deleted from WordPress.
 * It removes any plugin-specific settings or stored data.
 *
 * @package Nexus_Woo_Button_Alignment
 */

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Remove plugin options (future‑proof).
 * Add any options here if the plugin stores settings later.
 */
$plugin_options = array(
    'nexus_woo_button_alignment_settings',
    'nexus_woo_button_alignment_version',
);

foreach ($plugin_options as $option) {
    delete_option($option);
    delete_site_option($option); // Multisite support
}
