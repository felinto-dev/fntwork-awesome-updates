<?php
namespace MOD\Controller;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use MOD\MOD_Core as Core;
use MOD\Helper\MOD_Utils as Utils;
use MOD\View\MOD_Options as Options_View;

class MOD_Options {
	public function __construct() {
		add_filter( 'plugin_action_links_mod-auto-updates/mod-auto-updates.php', array( $this, 'plugin_links' ) );
		add_action( 'in_plugin_update_message-mod-auto-updates/mod-auto-updates.php', array( $this, 'mod_plugin_update_message' ), 10, 2 );
		add_filter( 'cron_schedules', array( $this, 'mod_add_cron_interval' ) );
		add_action( 'admin_init', array( $this, 'mod_register_schedule' ) );
		//add_action( 'admin_notices', array( $this, 'mod_admin_message' ) );
		add_action( 'admin_notices', array( $this, 'mod_settings_message' ) );
		add_action( 'admin_init', array( $this, 'mod_remove_api_messages' ) );
	}
    /**
	 * Add link settings page
	 *
	 * @since 1.0
	 * @param Array $links
	 * @return Array
	 */
	public function plugin_links( $links ) {
		$links_settings = array( sprintf(
            '<a href="%s">%s</a>',
			'options-general.php?page=mod-auto-updates',
            __( 'Configurações', Core::TEXTDOMAIN )
		) );

		$support_settings = array( sprintf(
            '<a href="%s">%s</a>',
			'https://go.fnt.work/telegram',
            __( 'Suporte FNTWORK', Core::TEXTDOMAIN )
		) );

		return array_merge( $links_settings, $support_settings, $links );
	}
  	/**
	 * Change message update
	 *
	 * @since 1.0.8
	 * @param String $links
	 * @return String
	 */
	public function mod_plugin_update_message( $data, $response ) {
		Options_View::mod_update_plugin_html( $data );
	}

	public function mod_add_cron_interval( $schedules ) {
		$mod_cron = Utils::mod_get_croninterval();

		$schedules[$mod_cron['plugin_event']] = array(
			'interval' => $mod_cron['interval'],
			'display'  => $mod_cron['display']
		);

		$schedules[$mod_cron['theme_event']] = array(
			'interval' => $mod_cron['interval'],
			'display'  => $mod_cron['display']
		);

		return $schedules;
	}

	public function mod_register_schedule() {
		$mod_cron = Utils::mod_get_croninterval();

		if ( ! wp_next_scheduled( $mod_cron['plugin_function'] ) ) {
			wp_schedule_event( time(), $mod_cron['plugin_event'], $mod_cron['plugin_function'] );
		}

		if ( ! wp_next_scheduled( $mod_cron['theme_function'] ) ) {
			wp_schedule_event( time(), $mod_cron['theme_event'], $mod_cron['theme_function'] );
		}
	}
	/**
	 * Plugin API CRON
	 *
	 * @since 1.0
	 * @param Array $links
	 * @return Array
	 */
    public function mod_admin_message() {
		$error = get_option( '_mod_error_message' );

		if ( !$error ) {
			return;
		}

        Utils::set_mod_admin_message( $error );
	}
	/**
	 * Admin Messages
	 *
	 * @since 1.0
	 * @param Array $links
	 * @return Array
	 */
	public function mod_settings_message() {
		Options_View::mod_settings_message_html();
	}
	/**
	 * Api Messages
	 *
	 * @since 1.0.9
	 * @param String
	 * @return String
	 */
	public function mod_remove_api_messages() {
        $error = get_option( '_mod_error_message');

		if ( $error ) {
			if ( $error === 'error_token' ) {
				delete_option( '_mod_error_message' );
			}
		}
    }
}
