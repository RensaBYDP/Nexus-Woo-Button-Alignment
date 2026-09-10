<?php

/**
 * GitHub Auto-Updater for Nexus Woo Button Alignment
 *
 * Checks GitHub Releases for updates and hooks into WordPress's native
 * update transients and upgrader workflows.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nexus_Woo_Updater
{
    /**
     * Main plugin file path.
     *
     * @var string
     */
    protected $file;

    /**
     * Plugin basename (e.g., 'Nexus-Woo-Button-Alignment/nexus-woo-button-alignment.php').
     *
     * @var string
     */
    protected $plugin_slug;

    /**
     * Plugin directory name / slug.
     *
     * @var string
     */
    protected $slug;

    /**
     * Current plugin version.
     *
     * @var string
     */
    protected $version;

    /**
     * GitHub username / organization.
     *
     * @var string
     */
    protected $github_username;

    /**
     * GitHub repository name.
     *
     * @var string
     */
    protected $github_repo;

    /**
     * Optional GitHub Personal Access Token (for private repositories or rate limits).
     *
     * @var string|null
     */
    protected $access_token;

    /**
     * In-memory cache for GitHub release data during the current request.
     *
     * @var object|null
     */
    protected $github_data = null;

    /**
     * Constructor.
     *
     * @param string $file Main plugin file path.
     * @param string $github_username GitHub owner.
     * @param string $github_repo GitHub repository name.
     * @param string|null $access_token Optional access token.
     */
    public function __construct($file, $github_username, $github_repo, $access_token = null)
    {
        $this->file            = $file;
        $this->plugin_slug     = plugin_basename($file);
        $this->slug            = dirname($this->plugin_slug);
        $this->github_username = $github_username;
        $this->github_repo     = $github_repo;
        $this->access_token    = $access_token;

        $this->init();
    }

    /**
     * Initialize updater hooks and properties.
     */
    protected function init()
    {
        // Retrieve plugin version from plugin data header.
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugin_data   = get_plugin_data($this->file, false, false);
        $this->version = !empty($plugin_data['Version']) ? $plugin_data['Version'] : '1.0.0';

        // Check for updates via WordPress update transients.
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));

        // Provide plugin info in the update modal ("View version x.x details").
        add_filter('plugins_api', array($this, 'plugin_popup'), 20, 3);

        // Normalize destination directory after zip extraction.
        add_filter('upgrader_post_install', array($this, 'post_install'), 10, 3);

        // Clear cached update transients when the plugin is updated.
        add_action('upgrader_process_complete', array($this, 'purge_transient'), 10, 2);

        // Add GitHub release link in plugin action links.
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
    }

    /**
     * Get GitHub API Release endpoint URL.
     *
     * @return string
     */
    protected function get_api_url()
    {
        return sprintf(
            'https://api.github.com/repos/%s/%s/releases/latest',
            rawurlencode($this->github_username),
            rawurlencode($this->github_repo)
        );
    }

    /**
     * Retrieve GitHub release information, using WordPress transients for caching.
     *
     * @param bool $force Whether to bypass the cache.
     * @return object|false
     */
    public function get_github_release($force = false)
    {
        if (null !== $this->github_data && !$force) {
            return $this->github_data;
        }

        $transient_key = 'nexus_woo_gh_update_' . md5($this->github_username . '/' . $this->github_repo);

        // Bypass cache if forced or if site admin triggered manual update check.
        if (isset($_GET['force-check']) || $force) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            delete_transient($transient_key);
        } else {
            $cached = get_transient($transient_key);
            if (false !== $cached && is_object($cached)) {
                $this->github_data = $cached;
                return $this->github_data;
            }
        }

        $headers = array(
            'Accept'     => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
        );

        $token = $this->access_token;
        if (empty($token) && defined('NEXUS_WOO_GITHUB_TOKEN')) {
            $token = NEXUS_WOO_GITHUB_TOKEN;
        }
        $token = apply_filters('nexus_woo_github_token', $token, $this->github_repo);

        if (!empty($token)) {
            $headers['Authorization'] = 'Bearer ' . trim($token);
        }

        $response = wp_remote_get($this->get_api_url(), array(
            'headers' => $headers,
            'timeout' => 15,
        ));

        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        if (!is_object($data) || empty($data->tag_name)) {
            return false;
        }

        $this->github_data = $data;

        // Cache for 12 hours.
        set_transient($transient_key, $data, 12 * HOUR_IN_SECONDS);

        return $this->github_data;
    }

    /**
     * Determine package download link from GitHub release.
     * Prefers uploaded .zip release assets; falls back to zipball_url.
     *
     * @param object $release
     * @return string
     */
    protected function get_package_download_url($release)
    {
        if (!empty($release->assets) && is_array($release->assets)) {
            foreach ($release->assets as $asset) {
                if (isset($asset->name) && preg_match('/\.zip$/i', $asset->name)) {
                    if (!empty($asset->browser_download_url)) {
                        return $asset->browser_download_url;
                    }
                }
            }
        }

        return !empty($release->zipball_url) ? $release->zipball_url : '';
    }

    /**
     * Check GitHub for updates and modify the WordPress update transient.
     *
     * @param object $transient
     * @return object
     */
    public function check_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $release = $this->get_github_release();
        if (!$release || empty($release->tag_name)) {
            return $transient;
        }

        // Clean tag name e.g. "v1.0.3" -> "1.0.3"
        $new_version  = ltrim($release->tag_name, 'vV');
        $download_url = $this->get_package_download_url($release);

        $plugin_info = array(
            'id'            => 'nexus-woo-button-alignment',
            'slug'          => $this->slug,
            'plugin'        => $this->plugin_slug,
            'new_version'   => $new_version,
            'url'           => !empty($release->html_url) ? $release->html_url : 'https://github.com/' . $this->github_username . '/' . $this->github_repo,
            'package'       => $download_url,
            'icons'         => array(),
            'banners'       => array(),
            'banners_rtl'   => array(),
            'tested'        => '',
            'requires_php'  => '7.4',
            'compatibility' => new stdClass(),
        );

        if (version_compare($new_version, $this->version, '>')) {
            $transient->response[$this->plugin_slug] = (object) $plugin_info;
            if (isset($transient->no_update[$this->plugin_slug])) {
                unset($transient->no_update[$this->plugin_slug]);
            }
        } else {
            $plugin_info['package'] = '';
            $transient->no_update[$this->plugin_slug] = (object) $plugin_info;
            if (isset($transient->response[$this->plugin_slug])) {
                unset($transient->response[$this->plugin_slug]);
            }
        }

        return $transient;
    }

    /**
     * Fill the plugin details modal in the WordPress admin plugins dashboard.
     *
     * @param false|object|array $result
     * @param string $action
     * @param object $args
     * @return false|object
     */
    public function plugin_popup($result, $action, $args)
    {
        if ('plugin_information' !== $action) {
            return $result;
        }

        if (empty($args->slug) || ($args->slug !== $this->slug && $args->slug !== $this->plugin_slug && $args->slug !== 'nexus-woo-button-alignment')) {
            return $result;
        }

        $release = $this->get_github_release();
        if (!$release) {
            return $result;
        }

        $new_version  = ltrim($release->tag_name, 'vV');
        $download_url = $this->get_package_download_url($release);

        $changelog = !empty($release->body)
            ? nl2br(esc_html($release->body))
            : '<p>' . esc_html__('No changelog provided for this release.', 'nexus-woo-button-alignment') . '</p>';

        $res = new stdClass();
        $res->name          = 'Nexus Woo Button Alignment';
        $res->slug          = $this->slug;
        $res->version       = $new_version;
        $res->author        = '<a href="https://github.com/RensaBYDP" target="_blank" rel="noopener noreferrer">Rensa, Build Your Digital Playground</a>';
        $res->homepage      = 'https://github.com/' . $this->github_username . '/' . $this->github_repo;
        $res->requires      = '5.0';
        $res->tested        = '6.7';
        $res->requires_php  = '7.4';
        $res->download_link = $download_url;
        $res->trunk         = $download_url;
        $res->last_updated  = !empty($release->published_at) ? $release->published_at : '';
        $res->sections      = array(
            'description' => esc_html__('Automatically aligns WooCommerce buttons on shop, category, and product loop pages for a cleaner and more consistent layout.', 'nexus-woo-button-alignment'),
            'changelog'   => $changelog,
        );

        return $res;
    }

    /**
     * Normalize destination folder name after installation.
     *
     * GitHub zipballs unpack into a folder like `username-repo-hash/`.
     * We rename the extracted directory to match the original plugin directory
     * so that WordPress does not deactivate the plugin upon update.
     *
     * @param bool|WP_Error $response
     * @param array $hook_extra
     * @param array $result
     * @return array
     */
    public function post_install($response, $hook_extra, $result)
    {
        global $wp_filesystem;

        // Verify this installation is for our plugin.
        if (empty($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_slug) {
            return $result;
        }

        // Expected final destination (e.g., /wp-content/plugins/Nexus-Woo-Button-Alignment/).
        $proper_destination = WP_PLUGIN_DIR . '/' . $this->slug;

        // Move/rename extracted directory to proper destination if different.
        if (isset($result['destination']) && untrailingslashit($result['destination']) !== untrailingslashit($proper_destination)) {
            $wp_filesystem->move($result['destination'], $proper_destination, true);
            $result['destination'] = $proper_destination;
        }

        // Re-activate plugin if it was active prior to the upgrade.
        if (is_plugin_active($this->plugin_slug)) {
            activate_plugin($this->plugin_slug);
        }

        return $result;
    }

    /**
     * Purge update transient cache after successful upgrade.
     *
     * @param WP_Upgrader $upgrader
     * @param array $hook_extra
     */
    public function purge_transient($upgrader, $hook_extra)
    {
        if (
            isset($hook_extra['action'], $hook_extra['type'], $hook_extra['plugins']) &&
            'update' === $hook_extra['action'] &&
            'plugin' === $hook_extra['type'] &&
            in_array($this->plugin_slug, (array) $hook_extra['plugins'], true)
        ) {
            $transient_key = 'nexus_woo_gh_update_' . md5($this->github_username . '/' . $this->github_repo);
            delete_transient($transient_key);
            $this->github_data = null;
        }
    }

    /**
     * Add links to the plugin meta row on the plugins page.
     *
     * @param array $plugin_meta
     * @param string $plugin_file
     * @return array
     */
    public function plugin_row_meta($plugin_meta, $plugin_file)
    {
        if ($plugin_file === $this->plugin_slug) {
            $repo_url = 'https://github.com/' . $this->github_username . '/' . $this->github_repo;
            $plugin_meta[] = sprintf(
                '<a href="%s/releases" target="_blank" rel="noopener noreferrer">%s</a>',
                esc_url($repo_url),
                esc_html__('GitHub Releases', 'nexus-woo-button-alignment')
            );
        }

        return $plugin_meta;
    }
}
