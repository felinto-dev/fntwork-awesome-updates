<?php
namespace MOD\Model;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use MOD\MOD_Core as Core;
use MOD\Helper\MOD_Utils as Utils;
use MOD\View\MOD_Settings as Settings_View;

class MOD_Update_Plugins {
    public function mod_check_plugin_update( $packages, $plugins_info ) {
				$token    = Utils::mod_get_token();
        $plugin_path  = Core::TMP_PLUGINS;
        $current_date = date( 'Y-m-d' );
        $response     = [];
        $body         = [];

        foreach ( $packages as $versions ) {
            foreach ( $versions as $plugin ) {
                if ( $plugin->type == 'wordpress-plugin' ) :
                    $mod_name    = $plugin->name;
                    $mod_version = $plugin->version;
                    $mod_file    = $plugin->dist->file;

                    foreach ( $plugins_info as $key => $info ) {
                        $path     = $info['path'];
                        $version  = $info['version'];
                        $mod_slug = $info['mod_slug'];
                        $slug     = $info['slug'];

                        if ( $mod_slug == $mod_name ) {
                            if ( version_compare( $version, $mod_version, '<' ) ) {

                                $response[$path] = [
                                    'mod_date'    => $current_date,
                                    'mod_path'    => $path,
                                    'mod_version' => $mod_version,
                                    'mod_slug'    => $slug,
																		'mod_file'    => $mod_file . '?x-api-key=' . $token
																];

                                $body[] = $response[$path];

                                set_transient( 'mod_upgrade_'.$slug, $response );
                            }
                        }
                    }
                endif;
            }
        }

        update_option( '_mod_plugins_datajson', json_encode( $body ) );

        return $response;
    }
}
