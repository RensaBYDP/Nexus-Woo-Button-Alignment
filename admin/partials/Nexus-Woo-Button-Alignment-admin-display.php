<?php

if (! defined('ABSPATH')) {
    exit;
}

$results = isset($results) && is_array($results) ? $results : array();
$images_url = plugin_dir_url(dirname(__FILE__)) . 'images/';

/**
 * Admin area view for Nexus-Woo-Button-Alignment.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://rensa.co.za
 * @since      1.0.1
 *
 * @package    Nexus_Woo_Button_Alignment
 * @subpackage Nexus_Woo_Button_Alignment/admin/partials
 */
?>

<div class="wrap nexus-wba-compatibility">
    <img
        class="nexus-wba-header-banner"
        src="<?php echo esc_url($images_url . 'header-banner.png'); ?>"
        alt="<?php esc_attr_e('Woo Button Alignment', 'nexus-woo-button-alignment-main'); ?>">

    <div class="nexus-wba-intro">
        <img
            class="nexus-wba-logo"
            src="<?php echo esc_url($images_url . 'Woo-Button-Alignment-Logo.png'); ?>"
            alt="<?php esc_attr_e('Woo Button Alignment logo', 'nexus-woo-button-alignment-main'); ?>">
        <div>
            <h1><?php esc_html_e('Woo Button Alignment Compatibility', 'nexus-woo-button-alignment-main'); ?></h1>
            <p><?php esc_html_e('Nexus-Woo-Button-Alignment keeps WooCommerce product-grid buttons aligned across standard classic and block-based layouts.', 'nexus-woo-button-alignment-main'); ?></p>
        </div>
    </div>

    <p><?php esc_html_e('This check reports the environment detected by the plugin. A passing platform check does not guarantee compatibility with themes that replace WooCommerce product templates.', 'nexus-woo-button-alignment-main'); ?></p>

    <table class="widefat striped nexus-wba-compatibility-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Component', 'nexus-woo-button-alignment-main'); ?></th>
                <th><?php esc_html_e('Detected', 'nexus-woo-button-alignment-main'); ?></th>
                <th><?php esc_html_e('Requirement', 'nexus-woo-button-alignment-main'); ?></th>
                <th><?php esc_html_e('Status', 'nexus-woo-button-alignment-main'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result) : ?>
                <tr>
                    <td><?php echo esc_html($result['component']); ?></td>
                    <td><?php echo esc_html($result['value']); ?></td>
                    <td><?php echo esc_html($result['required']); ?></td>
                    <td><span class="nexus-wba-status nexus-wba-status-<?php echo esc_attr($result['status']); ?>"><?php echo esc_html(ucfirst($result['status'])); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>