<?php

if (! defined('ABSPATH')) {
    exit;
}

$results = isset($results) && is_array($results) ? $results : array();
$summary = isset($summary) && is_array($summary) ? $summary : array(
    'status' => 'warning',
    'label'  => __('Unable to check', 'nexus-woo-button-alignment-main'),
    'passed' => 0,
    'total'  => 0,
);
$layout_details = isset($layout_details) && is_array($layout_details) ? $layout_details : array();
$shop_url = isset($shop_url) ? $shop_url : '';
$checked_at = isset($checked_at) ? $checked_at : '';
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

    <section class="nexus-wba-health nexus-wba-health-<?php echo esc_attr($summary['status']); ?>">
        <div>
            <span class="nexus-wba-eyebrow"><?php esc_html_e('Compatibility health', 'nexus-woo-button-alignment-main'); ?></span>
            <h2><?php echo esc_html($summary['label']); ?></h2>
            <p><?php printf(esc_html__('%1$d of %2$d environment checks passed.', 'nexus-woo-button-alignment-main'), (int) $summary['passed'], (int) $summary['total']); ?></p>
        </div>
        <div class="nexus-wba-health-actions">
            <?php if ($shop_url) : ?>
                <a class="button button-primary" href="<?php echo esc_url($shop_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('View shop', 'nexus-woo-button-alignment-main'); ?></a>
            <?php endif; ?>
            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nexus-wba-compatibility')); ?>"><?php esc_html_e('Refresh check', 'nexus-woo-button-alignment-main'); ?></a>
        </div>
    </section>

    <div class="nexus-wba-section-heading">
        <h2><?php esc_html_e('Platform details', 'nexus-woo-button-alignment-main'); ?></h2>
        <span><?php printf(esc_html__('Checked %s', 'nexus-woo-button-alignment-main'), esc_html($checked_at)); ?></span>
    </div>

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

    <div class="nexus-wba-dashboard-grid">
        <section class="nexus-wba-panel">
            <h2><?php esc_html_e('Supported layout modes', 'nexus-woo-button-alignment-main'); ?></h2>
            <p><?php esc_html_e('These are the product-grid structures recognized by the alignment assets. A custom theme may require additional selectors.', 'nexus-woo-button-alignment-main'); ?></p>
            <ul class="nexus-wba-layout-list">
                <?php foreach ($layout_details as $layout) : ?>
                    <li>
                        <span class="nexus-wba-status nexus-wba-status-pass"><?php echo esc_html($layout['status']); ?></span>
                        <strong><?php echo esc_html($layout['name']); ?></strong>
                        <code><?php echo esc_html($layout['selector']); ?></code>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="nexus-wba-panel">
            <h2><?php esc_html_e('Tools and troubleshooting', 'nexus-woo-button-alignment-main'); ?></h2>
            <p><?php esc_html_e('If buttons remain misaligned, clear page cache and confirm that the shop uses one of the supported product-grid structures.', 'nexus-woo-button-alignment-main'); ?></p>
            <ul class="nexus-wba-link-list">
                <li><a href="<?php echo esc_url(admin_url('admin.php?page=wc-status')); ?>"><?php esc_html_e('Open WooCommerce system status', 'nexus-woo-button-alignment-main'); ?></a></li>
                <li><a href="<?php echo esc_url(admin_url('themes.php')); ?>"><?php esc_html_e('Review active theme', 'nexus-woo-button-alignment-main'); ?></a></li>
                <li><a href="<?php echo esc_url(admin_url('plugins.php')); ?>"><?php esc_html_e('Review active plugins', 'nexus-woo-button-alignment-main'); ?></a></li>
            </ul>
        </section>
    </div>
</div>