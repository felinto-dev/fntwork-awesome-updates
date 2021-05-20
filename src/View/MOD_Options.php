<?php
namespace MOD\View;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use MOD\MOD_Core as Core;
use MOD\Helper\MOD_Utils as Utils;

class MOD_Options {
	public static function mod_settings_message_html() {
        $screen = get_current_screen();

        if ( $screen->parent_base == 'mod-auto-updates' ) {
            printf(
                '<div class="notice notice-info is-dismissible">
                <p>%s</p>
                <p class="submit">
                <a href="%s" class="button-primary" target="_blank">%s</a>
                <a href="%s" class="button-secondary" target="_blank">%s</a>
                </p>
                <p><strong>%s: </strong>%s</p>
                </div>',
                __( 'Se precisar pode entrar em contato com a gente!', Core::TEXTDOMAIN ),
                Core::support_link(),
                __( 'FNTWORK Suporte', Core::TEXTDOMAIN ),
                Core::mod_site_link(),
                __( 'Site Oficial', Core::TEXTDOMAIN ),
                __( 'Versão', Core::TEXTDOMAIN ),
                MOD_VERSION
            );
        }
    }

    public static function mod_update_plugin_html( $data ) {
        if ( isset( $data ) ) {
            printf(
                '<div class="update-message">
                    <p><strong>%s</strong></p>
                    <p><strong>%s</strong></p>
                </div>',
                'Otimizando consulta pelas atualizações.',
                'Adicionando opção de logs.'
            );
		}
    }
}
