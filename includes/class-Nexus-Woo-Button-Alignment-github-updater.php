<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Provide WordPress updates from GitHub Releases.
 *
 * @package Nexus_Woo_Button_Alignment
 */
class Nexus_WBA_GitHub_Updater
{
    private string $plugin_file;
    private string $plugin_basename;
    private string $plugin_slug;
    private string $current_version;
    private string $repository;

    public function __construct(string $plugin_file, string $current_version, string $repository)
    {
        $this->plugin_file = $plugin_file;
        $this->plugin_basename = plugin_basename($plugin_file);
        $this->plugin_slug = dirname($this->plugin_basename);
        $this->current_version = $current_version;
        $this->repository = trim($repository, '/');

        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_information'), 20, 3);
        add_filter('upgrader_source_selection', array($this, 'normalize_source_directory'), 10, 4);
    }

    /**
     * Add a newer GitHub release to the WordPress update transient.
     *
     * @param object $transient WordPress update transient.
     * @return object
     */
    public function check_for_update($transient)
    {
        if (! is_object($transient) || empty($transient->checked)) {
            return $transient;
        }

        $release = $this->get_latest_release();
        if (! $release || empty($release['version']) || ! version_compare($release['version'], $this->current_version, '>')) {
            return $transient;
        }

        $update = new stdClass();
        $update->slug = $this->plugin_slug;
        $update->plugin = $this->plugin_basename;
        $update->new_version = $release['version'];
        $update->url = $release['url'];
        $update->package = $release['package'];
        $update->tested = '6.8';
        $update->requires_php = '7.4';

        $transient->response[$this->plugin_basename] = $update;

        return $transient;
    }

    /**
     * Supply details for the WordPress plugin update modal.
     *
     * @param false|object $result Existing plugin information result.
     * @param string       $action API action.
     * @param object       $args API request arguments.
     * @return false|object
     */
    public function plugin_information($result, $action, $args)
    {
        if ('plugin_information' !== $action || empty($args->slug) || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        $release = $this->get_latest_release();
        if (! $release) {
            return $result;
        }

        $info = new stdClass();
        $info->name = 'Nexus-Woo-Button-Alignment';
        $info->slug = $this->plugin_slug;
        $info->version = $release['version'];
        $info->author = '<a href="https://rensa.co.za">Rensa Nexus</a>';
        $info->homepage = 'https://github.com/' . $this->repository;
        $info->requires = '5.0';
        $info->requires_php = '7.4';
        $info->tested = '6.8';
        $info->download_link = $release['package'];
        $info->sections = array(
            'description' => 'Align WooCommerce product buttons across classic and supported block-based product grids.',
            'changelog' => nl2br(esc_html($release['body'])),
        );

        return $info;
    }

    /**
     * Normalize GitHub source archives to the installed plugin directory name.
     *
     * @param string $source        Extracted source directory.
     * @param string $remote_source Temporary remote source directory.
     * @param object $upgrader      Upgrader instance.
     * @param array  $hook_extra    Upgrade context.
     * @return string|WP_Error
     */
    public function normalize_source_directory($source, $remote_source, $upgrader, $hook_extra)
    {
        if (empty($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_basename) {
            return $source;
        }

        $destination = trailingslashit(dirname($source)) . $this->plugin_slug;
        if ($source !== $destination && ! file_exists($destination) && @rename($source, $destination)) {
            return $destination;
        }

        return $source;
    }

    /**
     * Fetch and cache the latest stable GitHub release.
     *
     * @return array<string, string>|false
     */
    private function get_latest_release()
    {
        $cache_key = 'nexus_wba_github_release';
        $release = get_transient($cache_key);

        if (false === $release) {
            $response = wp_remote_get(
                'https://api.github.com/repos/' . $this->repository . '/releases/latest',
                array(
                    'timeout' => 10,
                    'headers' => array(
                        'Accept' => 'application/vnd.github+json',
                        'User-Agent' => 'Nexus-Woo-Button-Alignment/' . $this->current_version,
                    ),
                )
            );

            if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
                set_transient($cache_key, array(), HOUR_IN_SECONDS);
                return false;
            }

            $release = json_decode(wp_remote_retrieve_body($response), true);
            if (! is_array($release) || empty($release['tag_name']) || empty($release['html_url'])) {
                set_transient($cache_key, array(), HOUR_IN_SECONDS);
                return false;
            }

            $package = $this->get_package_url($release);
            if (! $package) {
                set_transient($cache_key, array(), HOUR_IN_SECONDS);
                return false;
            }

            $release = array(
                'version' => ltrim(sanitize_text_field($release['tag_name']), 'vV'),
                'url'     => esc_url_raw($release['html_url']),
                'package' => $package,
                'body'    => isset($release['body']) ? (string) $release['body'] : '',
            );

            set_transient($cache_key, $release, 12 * HOUR_IN_SECONDS);
        }

        return is_array($release) && ! empty($release['version']) ? $release : false;
    }

    /**
     * Prefer a release asset with the correct plugin ZIP structure.
     *
     * @param array $release GitHub release data.
     * @return string|false
     */
    private function get_package_url($release)
    {
        if (! empty($release['assets']) && is_array($release['assets'])) {
            foreach ($release['assets'] as $asset) {
                if (! empty($asset['name']) && 'nexus-woo-button-alignment.zip' === $asset['name'] && ! empty($asset['browser_download_url'])) {
                    return esc_url_raw($asset['browser_download_url']);
                }
            }
        }

        return ! empty($release['zipball_url']) ? esc_url_raw($release['zipball_url']) : false;
    }
}
