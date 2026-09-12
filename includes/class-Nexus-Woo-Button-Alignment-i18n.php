<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://rensa.co.za
 * @since      1.0.1
 *
 * @package    Nexus_Woo_Button_Alignment
 * @subpackage Nexus_Woo_Button_Alignment/includes
 * @author     Rensa Nexus <support@rensa.co.za>
 */
class Nexus_WBA_i18n
{

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.1
     */
    public function load_translations()
    {
        $domain = 'nexus-woo-button-alignment';
        $locale = get_locale();
        $mofile = plugin_dir_path(dirname(__FILE__)) . 'languages/Nexus-Woo-Button-Alignment-' . $locale . '.mo';

        if (file_exists($mofile) && filesize($mofile) > 0) {
            load_textdomain($domain, $mofile, $locale);
        }
    }
}
