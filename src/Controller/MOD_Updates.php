<?php
namespace MOD\Controller;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use MOD\MOD_Core as Core;
use MOD\Helper\MOD_Utils as Utils;
use MOD\Model\MOD_Update_Plugins as Plugins_Model;
use MOD\Model\MOD_Update_Themes as Themes_Model;

use stdClass;

class MOD_Updates {
    public function __construct() {
        $this->plugins_model = new Plugins_Model();
        $this->themes_model  = new Themes_Model();
        $this->mod_cron      = Utils::mod_get_croninterval();

        add_filter( 'site_transient_update_plugins', [ $this, 'mod_get_plugin_update' ], 99 );
        add_filter( 'transient_update_plugins', [ $this, 'mod_get_plugin_update' ], 99 );
        add_action( $this->mod_cron['plugin_function'], [ $this, 'mod_plugin_data' ] );
        add_filter( 'pre_set_site_transient_update_themes', [ $this, 'mod_get_theme_update' ], 99 );
        add_action( $this->mod_cron['theme_function'], [ $this, 'mod_theme_data' ] );
    }
    /**
	 * Plugin API CRON
	 *
	 * @since 1.0
	 * @param Array $links
	 * @return Array
	 */
    public function mod_plugin_data() {
        $token    = Utils::mod_get_token();
        $packages = Utils::get_api_json();

        if ( !$token ) {
            return;
        }

        if ( !$packages ) {
            return;
        }

        $plugins_info = Utils::mod_get_plugin_info();

        if ( $plugins_info ) {
            $this->plugins_model->mod_check_plugin_update( $packages, $plugins_info );
        }
	}
    /**
	 * Plugin Update
	 *
	 * @since 1.0
	 * @param Array $links
	 * @return Array
	 */
    public function mod_get_plugin_update( $transient ) {
        $plugins_info = get_option( '_mod_plugins_datajson');

        if ( ! $plugins_info ) {
            return $transient;
        }

        if ( ! is_object( $transient ) ) {
            return $transient;
        }

        if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
            $transient->response = array();
        }

        foreach ( json_decode( $plugins_info ) as $info ) {
            $remote   = get_transient( 'mod_upgrade_'.$info->mod_slug );
            $wp_path  = wp_normalize_path( MOD_ABSPATH );
            $rootPath = $wp_path . $info->mod_path;
            $_plugin  = get_plugin_data( $rootPath );

            if ( version_compare( $_plugin['Version'], $info->mod_version, '<' ) ) {
                if ( $remote ) {
                    $transient->response[$info->mod_path] = (object)array(
                        'slug'        => $info->mod_slug,
                        'new_version' => $info->mod_version,
                        'package'     => $info->mod_file
                    );
                }

            }
        }

        return $transient;
    }

    /**
	 * Theme API CRON
	 *
	 * @since 1.0
	 * @param Array $links
	 * @return Array
	 */
    public function mod_theme_data() {
        $token    = Utils::mod_get_token();
        $packages = Utils::get_api_json();

        if ( !$token ) {
            return;
        }

        if ( !$packages ) {
            return;
        }

        $themes_info = Utils::mod_get_theme_info();

        if ( $themes_info ) {
            $this->themes_model->mod_check_theme_update( $packages, $themes_info );
        }
	}
    /**
	 * Theme Update
	 *
	 * @since 1.0
	 * @param Array $links
	 * @return Array
	 */
    public function mod_get_theme_update( $transient ) {
        $themes_info = get_option( '_mod_themes_datajson');

        if ( ! $themes_info ) {
            return $transient;
        }

        if ( ! is_object( $transient ) ) {
            return $transient;
        }

        if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
            $transient->response = [];
        }

        foreach ( json_decode( $themes_info ) as $info ) {
            $path         = $info->mod_path;
            $slug         = $info->mod_slug;
            $mod_version  = $info->mod_version;
            $mod_package  = $info->mod_file;
            $remote       = get_transient( 'mod_upgrade_'.$slug );

             if ( $remote ) {
                $transient->response[$path] = [
                    'new_version' => $mod_version,
                    'package'     => $mod_package
                ];
            }
        }

        return $transient;
    }
}
