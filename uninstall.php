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
$nexus_wba_plugin_options = array(
    'nexus_woo_button_alignment_settings',
    'nexus_woo_button_alignment_version',
);

foreach ($nexus_wba_plugin_options as $nexus_wba_option) {
    delete_option($nexus_wba_option);
    delete_site_option($nexus_wba_option); // Multisite support
}
