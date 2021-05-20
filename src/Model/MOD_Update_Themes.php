<?php
namespace MOD\Model;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use MOD\MOD_Core as Core;
use MOD\Helper\MOD_Utils as Utils;
use MOD\View\MOD_Settings as Settings_View;

class MOD_Update_Themes {
    public function mod_check_theme_update( $packages, $themes_info ) {
        $theme_path   = Core::TMP_THEMES;
        $current_date = date( 'Y-m-d' );
        $response     = [];
        $body         = [];

        foreach ( $packages as $versions ) {
            foreach ( $versions as $theme ) {
                if ( $theme->type == 'wordpress-theme' ) :
                    $mod_name    = $theme->name;
                    $mod_version = $theme->version;
                    $mod_url     = $theme->dist->url;
                    $mod_file    = $theme->dist->file;

                    foreach ( $themes_info as $info ) {
                        $path     = $info['Slug'];
                        $version  = $info['Version'];
                        $mod_slug = $info['ModSlug'];
                        $slug     = $info['Slug'];

                        if ( $mod_slug == $mod_name ) {
                            if ( version_compare( $version, $mod_version, '<' ) ) {

                                $response[$path] = [
                                    'mod_date'    => $current_date,
                                    'mod_path'    => $path,
                                    'mod_version' => $mod_version,
                                    'mod_slug'    => $slug,
                                    'mod_file'    => $mod_file
                                ];

                                $body[] = $response[$path];

                                set_transient( 'mod_upgrade_'.$slug, $response );
                            }
                        }
                    }
                endif;
            }
        }

        update_option( '_mod_themes_datajson', json_encode( $body ) );

        return $response;
    }
}
