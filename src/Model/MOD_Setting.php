<?php
namespace MOD\Model;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use MOD\MOD_Core as Core;
use MOD\Helper\MOD_Utils as Utils;
use MOD\View\MOD_Settings as Settings_View;

class MOD_Setting {
	public function mod_page_register_fields( $page_name ) {
		register_setting(
            'mod_auto_updates_group',
            'mod_auto_updates_field',
            [ $this, 'sanitize' ]
        );

        add_settings_section(
            'setting_section_id',
            __( 'Felinto Network - Atualizações Fantásticas', Core::TEXTDOMAIN ),
            [ $this, 'print_section_info' ],
            $page_name
        );

        add_settings_field(
            'mod_token_number',
			__( 'Token', Core::TEXTDOMAIN ),
            [ $this, 'mod_token_callback' ],
            $page_name,
            'setting_section_id'
        );

        add_settings_field(
            'mod_cron_settings',
			__( 'Atualizações', Core::TEXTDOMAIN ),
            [ $this, 'mod_cron_callback' ],
            $page_name,
            'setting_section_id'
        );

        add_settings_field(
            'mod_log_settings',
			__( 'Logs', Core::TEXTDOMAIN ),
            [ $this, 'mod_log_callback' ],
            $page_name,
            'setting_section_id'
        );
	}
	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = [];

        if ( isset( $input['mod_token_number'] ) ) {
            $new_input['mod_token_number'] = sanitize_text_field( $input['mod_token_number'] );
        }

        if ( isset( $input['mod_cron_settings'] ) ) {
            $new_input['mod_cron_settings'] = sanitize_text_field( $input['mod_cron_settings'] );
        }

        if ( isset( $input['mod_log_settings'] ) ) {
            $new_input['mod_log_settings'] = sanitize_text_field( $input['mod_log_settings'] );
        }

        return $new_input;
	}
	/**
     * Print the Section text
    */
    public function print_section_info() {
        print( __( 'Para funcionamento das atualizações necessário inserir o token.', Core::TEXTDOMAIN ) );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function mod_token_callback() {
		$options = get_option( 'mod_auto_updates_field' );

        printf(
			'<input type="password" id="mod_token_number" name="mod_auto_updates_field[mod_token_number]" value="%s" required/>
			<p class="description" id="tagline-description">%s</p>',
			isset( $options['mod_token_number'] ) ? esc_attr( $options['mod_token_number']) : '',
			__( 'Campo para inserir seu Token para atualização dos plugins e/ou temas.', Core::TEXTDOMAIN )
        );
    }

     /**
     * Get the settings option array and print one of its values
     */
    public function mod_cron_callback() {
        $options = get_option( 'mod_auto_updates_field' );
        $items   = [
            '10hour'    => 'A cada 10 horas',
            '1hour'     => 'A cada 1 hora',
            '5hour'     => 'A cada 5 horas',
            '10minutes' => 'A cada 10 minutos'
        ];

        echo '<select id="mod_cron_settings" name="mod_auto_updates_field[mod_cron_settings]">';

        foreach ( $items as $key => $item ) {
            $selected = esc_attr( $options['mod_cron_settings'] ) == $key ? 'selected="selected"' : '';
            echo "<option value='$key' $selected>$item</option>";
        }

        echo '</select>';

        printf( '<p class="description" id="tagline-description">%s</p>',
            __( 'Escolha o tempo para a consulta de atualizações. Por padrão, será a cada 10 Horas!', Core::TEXTDOMAIN )
        );
    }
    /**
     * Get the settings option array and print one of its values
    */
    public function mod_log_callback() {
        $options = get_option( 'mod_auto_updates_field' );

        printf(
            '<input id="%1$s" name="mod_auto_updates_field[%1$s]" type="checkbox" %2$s />
            <label for="mod_log_settings"> Habilitar Log</label>
            <p class="description" id="tagline-description">%3$s</p>',
            'mod_log_settings',
            checked( isset( $options['mod_log_settings'] ), true, false ),
            __( 'Marque para habilitar o submenu com os registros dos logs.', Core::TEXTDOMAIN )
        );
    }
}
