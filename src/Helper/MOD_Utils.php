<?php
namespace MOD\Helper;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use MOD\MOD_Core as Core;

class MOD_Utils {
	/**
	 * Sanitize value from custom method
	 *
	 * @since 1.0
	 * @param String $name
	 * @param Mixed $default
	 * @param String|Array $sanitize
	 * @return Mixed
	*/
	public static function request( $type, $name, $default, $sanitize = 'rm_tags' ) {
		$request = filter_input_array( $type, FILTER_SANITIZE_SPECIAL_CHARS );

		if ( ! isset( $request[ $name ] ) || empty( $request[ $name ] ) ) {
			return $default;
		}

		return self::sanitize( $request[ $name ], $sanitize );
	}
	/**
	 * Sanitize value from methods post
	 *
	 * @since 1.0
	 * @param String $name
	 * @param Mixed $default
	 * @param String|Array $sanitize
	 * @return Mixed
	*/
	public static function post( $name, $default = '', $sanitize = 'rm_tags' ) {
		return self::request( INPUT_POST, $name, $default, $sanitize );
	}
	/**
	 * Sanitize value from methods get
	 *
	 * @since 1.0
	 * @param String $name
	 * @param Mixed $default
	 * @param String|Array $sanitize
	 * @return Mixed
	*/
	public static function get( $name, $default = '', $sanitize = 'rm_tags' ) {
		return self::request( INPUT_GET, $name, $default, $sanitize );
	}
	/**
	 * Sanitize value from cookie
	 *
	 * @since 1.0
	 * @param String $name
	 * @param Mixed $default
	 * @param String|Array $sanitize
	 * @return Mixed
	*/
	public static function cookie( $name, $default = '', $sanitize = 'rm_tags' ) {
		return self::request( INPUT_COOKIE, $name, $default, $sanitize );
	}
	/**
	 * Get filtered super global server by key
	 *
	 * @since 1.0
	 * @param String $key
	 * @return String
	*/
	public static function server( $key ) {
		$value = self::get_value_by( $_SERVER, strtoupper( $key ) );

		return self::rm_tags( $value, true );
	}
	/**
	 * Verify request by nonce
	 *
	 * @since 1.0
	 * @param String $name
	 * @param String $action
	 * @return Boolean
	*/
	public static function verify_nonce_post( $name, $action ) {
		return wp_verify_nonce( self::post( $name, false ), $action );
	}
	/**
	 * Sanitize requests
	 *
	 * @since 1.0
	 * @param String $value
	 * @param String|Array $sanitize
	 * @return String
	*/
	public static function sanitize( $value, $sanitize ) {
		if ( ! is_callable( $sanitize ) ) {
	    	return ( false === $sanitize ) ? $value : self::rm_tags( $value );
		}

		if ( is_array( $value ) ) {
			return array_map( $sanitize, $value );
		}

		return call_user_func( $sanitize, $value );
	}
	/**
	 * Properly strip all HTML tags including script and style
	 *
	 * @since 1.0
	 * @param Mixed String|Array $value
	 * @param Boolean $remove_breaks
	 * @return Mixed String|Array
	 */
	public static function rm_tags( $value, $remove_breaks = false ) {
		if ( empty( $value ) || is_object( $value ) ) {
			return $value;
		}

		if ( is_array( $value ) ) {
			return array_map( __METHOD__, $value );
		}

	    return wp_strip_all_tags( $value, $remove_breaks );
	}
	/**
	 * Find the position of the first occurrence of a substring in a string
	 *
	 * @since 1.0
	 * @param String $value
	 * @param String $search
	 * @return Boolean
	*/
	public static function indexof( $value, $search ) {
		return ( false !== strpos( $value, $search ) );
	}
	/**
	 * Verify request ajax
	 *
	 * @since 1.0
	 * @param null
	 * @return Boolean
	*/
	public static function is_request_ajax() {
		return ( strtolower( self::server( 'HTTP_X_REQUESTED_WITH' ) ) === 'xmlhttprequest' );
	}
	/**
	 * Get charset option
	 *
	 * @since 1.0
	 * @param Null
	 * @return String
	 */
	public static function get_charset() {
		return self::rm_tags( get_bloginfo( 'charset' ) );
	}
	/**
	 * Descode html entityes
	 *
	 * @since 1.0
	 * @param String $string
	 * @return String
	 */
	public static function html_decode( $string ) {
		return html_entity_decode( $string, ENT_NOQUOTES, self::get_charset() );
	}
	/**
	 * Get value by array index
	 *
	 * @since 1.0
	 * @param Array $args
	 * @param String|int $index
	 * @return String
	 */
	public static function get_value_by( $args, $index, $default = '' ) {
		if ( ! array_key_exists( $index, $args ) || empty( $args[ $index ] ) ) {
			return $default;
		}

		return $args[ $index ];
	}
	/**
	 * Admin sanitize url
	 *
	 * @since 1.0
	 * @param String $path
	 * @return String
	 */
	public static function get_admin_url( $path = '' ) {
		return esc_url( get_admin_url( null, $path ) );
	}
	/**
	 * Site URL
	 *
	 * @since 1.0
	 * @param String $path
	 * @return String
	 */
	public static function get_site_url( $path = '' ) {
		return esc_url( get_site_url( null, $path ) );
	}
	/**
	 * Permalink url sanitized
	 *
	 * @since 1.0
	 * @param Integer $post_id
	 * @return String
	 */
	public static function get_permalink( $post_id = 0 ) {
		return esc_url( get_permalink( $post_id ) );
	}
	/**
	 * Add prefix in string
	 *
	 * @since 1.0
	 * @param String $after
	 * @param String $before
	 * @return String
	 */
	public static function add_prefix( $after, $before = '' ) {
		return $before . Core::PREFIX . $after;
	}
	/**
	 * Get date formatted for SQL
	 *
	 * @param String $date
	 * @param String $format
	 * @return String
	 */
	public static function convert_date_for_sql( $date, $format = 'Y-m-d' ) {
		return empty( $date ) ? '' : self::convert_date( $date, $format, '/', '-' );
	}
	/**
	 * Conversion of date
	 *
	 * @param String $date
	 * @param String $format
	 * @param String $search
	 * @param String $replace
	 * @return String
	 */
	public static function convert_date( $date, $format = 'Y-m-d', $search = '/', $replace = '-' ) {
		if ( $search && $replace ) {
			$date = str_replace( $search, $replace, $date );
		}

		return date_i18n( $format, strtotime( $date ) );
	}

	public static function get_template( $file, $args = array() ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$locale = Core::plugin_dir_path() . $file . '.php';

		if ( ! file_exists( $locale ) ) {
			return;
		}

		include $locale;
	}
	/**
	 * Get Token
	 *
	 * @return string
	 */
	public static function mod_get_token() {
		$options = get_option( 'mod_auto_updates_field' );
		$token   = $options['mod_token_number'];

		return $token;
	}

	/**
	 * Get Log On
	 *
	 * @return string
	 */
	public static function mod_is_log_page() {
		$options = get_option( 'mod_auto_updates_field' );
		$log     = '';

		if ( isset( $options['mod_log_settings'] ) ) {
			$log = $options['mod_log_settings'];
		}

		return $log;
	}
	/**
	 * Get Cron Interval
	 *
	 * @return string
	 */
	public static function mod_get_croninterval() {
		$options  = get_option( 'mod_auto_updates_field' );
		$cron     = $options['mod_cron_settings'];
		$args     = array(
			'plugin_event'    => 'mod_tmp_plugins',
			'plugin_function' => 'mod_remove_plugins',
			'theme_event'     => 'mod_tmp_themes',
			'theme_function'  => 'mod_remove_themes',
			'interval'        => 10 * HOUR_IN_SECONDS,
			'display'         => esc_html__( 'Consulta a cada 10 Horas' )
		);

		if ( empty( $cron ) ) {
			return $args;
		}

		if ( $cron === '10hour' ) {
			wp_clear_scheduled_hook( 'mod_remove_plugins1' );
			wp_clear_scheduled_hook( 'mod_remove_plugins5' );
			wp_clear_scheduled_hook( 'mod_remove_plugins10' );
			wp_clear_scheduled_hook( 'mod_remove_themes1' );
			wp_clear_scheduled_hook( 'mod_remove_themes5' );
			wp_clear_scheduled_hook( 'mod_remove_themes10' );

			return $args;
		}

		if ( $cron === '1hour' ) {
			wp_clear_scheduled_hook( 'mod_remove_plugins' );
			wp_clear_scheduled_hook( 'mod_remove_plugins5' );
			wp_clear_scheduled_hook( 'mod_remove_plugins10' );
			wp_clear_scheduled_hook( 'mod_remove_themes' );
			wp_clear_scheduled_hook( 'mod_remove_themes5' );
			wp_clear_scheduled_hook( 'mod_remove_themes10' );

			return [
				'plugin_event'    => 'mod_tmp_plugins1',
				'plugin_function' => 'mod_remove_plugins1',
				'theme_event'     => 'mod_tmp_themes1',
				'theme_function'  => 'mod_remove_themes1',
				'interval'        => 1 * HOUR_IN_SECONDS,
				'display'         => esc_html__( 'Consulta a cada 1 Hora' )
			];
		}

		if ( $cron === '5hour' ) {
			wp_clear_scheduled_hook( 'mod_remove_plugins1' );
			wp_clear_scheduled_hook( 'mod_remove_plugins' );
			wp_clear_scheduled_hook( 'mod_remove_plugins10' );
			wp_clear_scheduled_hook( 'mod_remove_themes1' );
			wp_clear_scheduled_hook( 'mod_remove_themes' );
			wp_clear_scheduled_hook( 'mod_remove_themes10' );

			return [
				'plugin_event'    => 'mod_tmp_plugins5',
				'plugin_function' => 'mod_remove_plugins5',
				'theme_event'     => 'mod_tmp_themes5',
				'theme_function'  => 'mod_remove_themes5',
				'interval'        => 5 * HOUR_IN_SECONDS,
				'display'         => esc_html__( 'Consulta a cada 5 Horas' )
			];
		}

		if ( $cron === '10minutes' ) {
			wp_clear_scheduled_hook( 'mod_remove_plugins1' );
			wp_clear_scheduled_hook( 'mod_remove_plugins5' );
			wp_clear_scheduled_hook( 'mod_remove_plugins' );
			wp_clear_scheduled_hook( 'mod_remove_themes1' );
			wp_clear_scheduled_hook( 'mod_remove_themes5' );
			wp_clear_scheduled_hook( 'mod_remove_themes' );

			return [
				'plugin_event'    => 'mod_tmp_plugins10',
				'plugin_function' => 'mod_remove_plugins10',
				'theme_event'     => 'mod_tmp_themes10',
				'theme_function'  => 'mod_remove_themes10',
				'interval'        => 10 * MINUTE_IN_SECONDS,
				'display'         => esc_html__( 'Consulta a cada 10 Minutos' )
			];
		}
	}
	/**
	 * Create DIR
	 *
	 * @return Boolean
	 */
	public static function create_plugin_dir() {
		$plugin_path = Core::TMP_PLUGINS;

		if ( !file_exists( $plugin_path ) ) {
			mkdir( $plugin_path, 0755, true );
		}
	}
	/**
	 * Create DIR
	 *
	 * @return Boolean
	 */
	public static function create_theme_dir() {
		$theme_path = Core::TMP_THEMES;

		if ( !file_exists( $theme_path ) ) {
			mkdir( $theme_path, 0755, true );
		}
	}
	/**
	 * Check DIR
	 *
	 * @return Boolean
	 */
	public static function dir_plugins_is_empty() {
		$plugin_path = Core::TMP_PLUGINS;
		$message     = 'empty';

		if ( is_dir( $plugin_path ) ) {
			foreach ( scandir( $plugin_path ) as $file ) {
				if ( !in_array( $file, array('.', '..', 'plugins.json' ) ) ) {
					$message = 'not_empty';
				}
			}

			return $message;
		}

	}
	/**
	 * Check DIR
	 *
	 * @return Boolean
	 */
	public static function dir_themes_is_empty() {
		$theme_path = Core::TMP_THEMES;
		$message     = 'empty';

		if ( is_dir( $theme_path ) ) {
			foreach ( scandir( $theme_path ) as $file ) {
				if ( !in_array( $file, array( '.', '..', 'themes.json' ) ) ) {
					$message = 'not_empty';
				}
			}

			return $message;
		}
	}
	/**
	 * Remove Files
	 *
	 * @return Boolean
	 */
	public static function is_empty_dir( $dirname ) {
		$files = glob( $dirname.'*' );

		foreach ( $files as $file ) {
			if ( is_file( $file ) )
			unlink($file);
		}
	}
	/**
	 * Api MOD
	 *
	 * @return String
	 */
	public static function get_api_json() {
		$url   = Core::API_URL;
		$token = self::mod_get_token();
		$error = get_option( '_mod_error_message');
        $args  = [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode( $token.':'.Core::PASSWORD )
			],
			'timeout'     => 120,
			'httpversion' => '1.1'
		];

		$response = wp_remote_get( $url, $args );

		if ( is_array( $response ) && !empty( $response['body'] ) ) {
			$data = json_decode( $response['body'] );

			if ( !$data ) {
				update_option( '_mod_error_message', 'error_json' );
				return;
			}

			$packages = $data->packages;

			if ( $error ) {
				delete_option( '_mod_error_message' );
			}

			return $packages;

		} else {
			update_option( '_mod_error_message', 'error_json' );
			return false;
		}
	}

	public static function mod_get_plugin_info() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Get all plugins
		$all_plugins = get_plugins();

		// Get active plugins
		$active_plugins = get_option( 'active_plugins' );

		// Assemble array of name, version, and whether plugin is active (boolean)
		foreach ( $all_plugins as $key => $value ) {
			$is_active = ( in_array( $key, $active_plugins ) ) ? true : false;
			$slug      = substr( $key, 0, stripos( $key, "/" ) );
			$mod_slug  = 'fntwork/'.$slug;

			if ( $is_active == true && $slug != MOD_SLUG ) {
				$plugins[$mod_slug] = [
					'name'     => $value['Name'],
					'path'     => $key,
					'slug'     => $slug,
					'mod_slug' => $mod_slug,
					'version'  => $value['Version'],
					'active'   => $is_active,
				];
			}
		}

		return $plugins;
	}

	public static function mod_get_theme_info() {
		$themes     = [];
		$get_themes = wp_get_themes();

		foreach ( $get_themes as $theme ) {
			$stylesheet          = $theme->get_stylesheet();
			$mod_slug            = 'fntwork/'.$stylesheet;
			$themes[$stylesheet] = [
				'Name'        => $theme->get( 'Name' ),
				'ThemeURI'    => $theme->get( 'ThemeURI' ),
				'Slug'        => $stylesheet,
				'ModSlug'     => $mod_slug,
				'Description' => $theme->get( 'Description' ),
				'Author'      => $theme->get( 'Author' ),
				'AuthorURI'   => $theme->get( 'AuthorURI' ),
				'Version'     => $theme->get( 'Version' ),
				'Template'    => $theme->get( 'Template' ),
				'Status'      => $theme->get( 'Status' ),
				'Tags'        => $theme->get( 'Tags' ),
				'TextDomain'  => $theme->get( 'TextDomain' ),
				'DomainPath'  => $theme->get( 'DomainPath' )
			];
		}

		return $themes;
	}

	public static function set_mod_admin_message( $error ) {

		if ( $error === 'error_json' ) {
			echo '<div class="notice notice-warning is-dismissible">
				<p><strong>FNTWORK - Atualizações Fantásticas: </strong>Para melhor atendimento, no momento nosso servidor está em manutenção!</p>
			</div>';
		}

		if ( $error === 'error_server' ) {
			echo '<div class="notice notice-warning is-dismissible">
				<p><strong>FNTWORK - Atualizações Fantásticas: </strong>Para melhor atendimento, no momento nosso servidor está em manutenção!</p>
			</div>';
		}
	}

	public static function get_log_cron_name() {
		$options  = get_option( 'mod_auto_updates_field' );
		$cron     = $options['mod_cron_settings'];

		switch ( $cron ) {
			case $cron === '10hour':
				return 'A cada 10 horas';
				break;
			case $cron === '1hour':
				return 'A cada 1 hora';
				break;
			case $cron === '5hour':
				return 'A cada 5 horas';
				break;
			case $cron === '10minutes':
				return 'A cada 10 minutos';
				break;
			default:
				return 'A cada 10 horas';
				break;
		}
	}
}
