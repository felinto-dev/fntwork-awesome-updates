<?php
namespace MOD\View;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use MOD\MOD_Core as Core;
use MOD\Helper\MOD_Utils as Utils;
use MOD\Model\MOD_Setting as Settings_Model;

class MOD_Settings {
	public static function mod_admin_page_html() {
	?>
        <div class="wrap" oncontextmenu="return false">
            <h1><?php echo __( 'FNTWORK Configurações', Core::TEXTDOMAIN ); ?></h1>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'mod_auto_updates_group' );
                do_settings_sections( 'mod-auto-updates' );
                submit_button();
            ?>
            </form>
        </div>
    <?php
    }

    public static function mod_log_page_html() {
        $plugins_info = get_option( '_mod_plugins_datajson');
        $themes_info  = get_option( '_mod_themes_datajson');
    ?>
    <div class="wrap">
        <h1><?php _e( 'FNTWORK Logs', Core::TEXTDOMAIN ); ?></h1>
        <p><?php _e( 'Detalhes dos plugins e/ou temas que estão disponíveis para atualização.', Core::TEXTDOMAIN ); ?></p>
        <p><?php echo 'O tempo para consulta está definido para <strong>'.Utils::get_log_cron_name().'</strong>.'; ?></p>
        <div class="mod-log-container">
        <?php

            if ( $plugins_info === '[]' || !$plugins_info ) {
                if ( $themes_info === '[]' || !$themes_info ) {
                    echo '<h2>Nenhuma atualização encontrada!</h2>';
                }
            }

            if ( $plugins_info !== '[]' && $plugins_info ) : ?>
            <table>
                <caption>FNTWORK PLUGINS</caption>
                <thead>
                    <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Versão Disponível</th>
                    <th scope="col">Data da Consulta</th>
                    <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ( json_decode( $plugins_info ) as $info ) {
                    $name = str_replace( '-', ' ', $info->mod_slug );

                    printf( '<tr>
                        <td data-label="Name">%s</td>
                        <td data-label="Versão">%s</td>
                        <td data-label="Data">%s</td>
                        <td data-label="Status"><a href="%s">%s</a></td>
                        </tr>',
                        $name,
                        $info->mod_version,
                        date( 'd/m/Y', strtotime( $info->mod_date ) ),
                        admin_url( 'plugins.php' ),
                        __( 'Atualizar agora', Core::TEXTDOMAIN )
                    );
                }
                ?>
                </tbody>
            </table>
        <?php endif;

        if ( $themes_info !== '[]' && $themes_info ) : ?>
            <table>
                <caption>FNTWORK TEMAS</caption>
                <thead>
                    <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Versão Disponível</th>
                    <th scope="col">Tempo da Consulta</th>
                    <th scope="col">Data da Consulta</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ( json_decode( $themes_info ) as $info ) {
                    $name = str_replace( '-', ' ', $info->mod_slug );

                    printf( '<tr>
                        <td data-label="Name">%s</td>
                        <td data-label="Versão">%s</td>
                        <td data-label="Tempo">%s</td>
                        <td data-label="Data">%s</td>
                        </tr>',
                        $name,
                        $info->mod_version,
                        Utils::get_log_cron_name(),
                        date( 'd/m/Y', strtotime( $info->mod_date ) )
                    );
                }
                ?>
                </tbody>
            </table>
        <?php endif; ?>
        </div>
    </div>
    <?php
    }
}
