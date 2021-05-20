<?php
/*
 * Plugin Name: Felinto Network - Atualizações fantásticas
 * Plugin URI:  https://felintonetwork.com/forum/23-atualiza%C3%A7%C3%B5es-fant%C3%A1sticas-da-felinto-network/
 * Version:     3.0.0
 * Author:      Felinto Network
 * Author URI:  https://felintonetwork.com
 * Text Domain: mod-auto-updates
 * Domain Path: /languages
 * License:     GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Description: Novo e reformulado 💖. Totalmente compatível com PHP 7.1+ o novo plugin da Felinto Network - Atualizações fantásticas, vai te permitir atualizar todos os seus plugins e temas direto do repositório da Felinto Network com 1-click. Haja facilidade!
 * Requires PHP: 7.1
 */

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

require_once dirname( __FILE__ ) . '/constants.php';

function mod_render_admin_notice_html( $message, $type = 'error' ) {
?>
	<div class="<?php echo $type; ?> notice is-dismissible">
		<p>
			<strong><?php _e( 'Felinto Network - Atualizações Fantásticas', MOD_SLUG ); ?>: </strong>

			<?php echo $message; ?>
		</p>
	</div>
<?php
}

if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {
	function mod_admin_notice_php_version() {
		mod_render_admin_notice_html(
			__( 'Sua versão no PHP não é suportada. Requerido >= 5.5', MOD_SLUG )
		);
	}

	_mod_load_notice( 'admin_notice_php_version' );
	return;
}

function _mod_load_notice( $name ) {
	add_action( 'admin_notices', "mod_{$name}" );
}

function _mod_load_instances() {
	require_once __DIR__ . '/vendor/autoload.php';

	MOD\MOD_Core::instance();

	$mod_update = \Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/Open-Linux-Solutions/mod-auto-updates',
		__FILE__,
		'mod-auto-updates'
	);

	$mod_update->setAuthentication('6b0dbb0c08e5a8d3e7ddadeb9a2dc030abd067b6');
	$mod_update->setBranch('master');

	do_action( 'mod_init' );
}

function mod_plugins_loaded_check() {
	return _mod_load_instances();
}

add_action( 'plugins_loaded', 'mod_plugins_loaded_check', 0 );

function mod_on_activation() {
	add_option( MOD_OPTION_ACTIVATE, true );
	set_transient( 'mod-activation-notice', true, 5 );

	mod_delete_options();

	register_uninstall_hook( __FILE__, 'mod_on_uninstall' );
}

if ( get_transient( 'mod-activation-notice' ) ) {
	function mod_admin_notice_active_token() {
		mod_render_admin_notice_html(
			'Precisamos do seu TOKEN para que você possa usar o plugin da Felinto Network - Atualizações fantásticas! <a href="options-general.php?page=mod-auto-updates">Adicionar Token</a>',
			'notice-warning'
		);
	}

	_mod_load_notice( 'admin_notice_active_token' );
	delete_transient( 'mod-activation-notice' );
	return;
}

function mod_on_deactivation() {
	mod_delete_options();
}

function mod_delete_options() {
	delete_option( '_mod_plugins_datajson' );
	delete_option( '_mod_themes_datajson' );
	delete_option( '_mod_error_message' );
}

function mod_on_uninstall() {}

register_activation_hook( __FILE__, 'mod_on_activation' );
register_deactivation_hook( __FILE__, 'mod_on_deactivation' );
