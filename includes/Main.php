<?php
/**
 * Main class.
 *
 * @package  Core_Speed_Optimizer
 * @version  1.0.0
 */

namespace Core_Speed_Optimizer;

use Core_Speed_Optimizer\Admin\Main as Admin;
use Core_Speed_Optimizer\Front\Main as Front;


/**
 * Base Plugin class holding generic functionality
 */
final class Main {

	/**
	 * Set the minimum required versions for the plugin.
	 */
	const PLUGIN_REQUIREMENTS = array(
		'php_version' => '7.4',
		'wp_version'  => '5.6',
		'wc_version'  => '5.3',
	);


	/**
	 * Constructor
	 */
	public static function bootstrap() {

		register_activation_hook( PLUGIN_FILE, array( Install::class, 'install' ) );

		add_action( 'plugins_loaded', array( __CLASS__, 'load' ) );

		add_action( 'init', array( __CLASS__, 'init' ) );

		// Perform other actions when plugin is loaded.
		do_action( 'core_speed_optimizer_loaded' );
	}


	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'core-speed-optimizer' ), '1.0.0' );
	}


	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'core-speed-optimizer' ), '1.0.0' );
	}


	/**
	 * Include plugins files and hook into actions and filters.
	 *
	 * @since  1.0.0
	 */
	public static function load() {

		if ( ! self::check_plugin_requirements() ) {
			return;
		}

		if ( Utils::is_request( 'admin' ) ) {
			Admin::hooks();
		}

		if ( Utils::is_request( 'frontend' ) ) {
			Front::hooks();
		}

		// Common includes.
		Block::hooks();

		Customizations\ACF::hooks();

		// Options are required on both front and admin.
		OptionsDispatch::hooks();

		// Set up localisation.
		self::load_plugin_textdomain();

		// Init action.
		do_action( 'core_speed_optimizer_loaded' );
	}


	/**
	 * Method called by init hook
	 *
	 * @return void
	 */
	public static function init() {

		// Before init action.
		do_action( 'before_core_speed_optimizer_init' );

		// Add needed hooks here.

		// After init action.
		do_action( 'core_speed_optimizer_init' );
	}


	/**
	 * Checks all plugin requirements. If run in admin context also adds a notice.
	 *
	 * @return boolean
	 */
	private static function check_plugin_requirements() {

		$errors = array();
		global $wp_version;

		if ( ! version_compare( PHP_VERSION, self::PLUGIN_REQUIREMENTS['php_version'], '>=' ) ) {
			/* Translators: The minimum PHP version */
			$errors[] = sprintf( esc_html__( 'WordPress Plugin Boilerplate requires a minimum PHP version of %s.', 'core-speed-optimizer' ), self::PLUGIN_REQUIREMENTS['php_version'] );
		}

		if ( ! version_compare( $wp_version, self::PLUGIN_REQUIREMENTS['wp_version'], '>=' ) ) {
			/* Translators: The minimum WP version */
			$errors[] = sprintf( esc_html__( 'WordPress Plugin Boilerplate requires a minimum WordPress version of %s.', 'core-speed-optimizer' ), self::PLUGIN_REQUIREMENTS['wp_version'] );
		}

		if ( isset( self::PLUGIN_REQUIREMENTS['wc_version'] ) && ( ! defined( 'WC_VERSION' ) || ! version_compare( WC_VERSION, self::PLUGIN_REQUIREMENTS['wc_version'], '>=' ) ) ) {
			/* Translators: The minimum WC version */
			$errors[] = sprintf( esc_html__( 'WordPress Plugin Boilerplate requires a minimum WooCommerce version of %s.', 'core-speed-optimizer' ), self::PLUGIN_REQUIREMENTS['wc_version'] );
		}

		if ( empty( $errors ) ) {
			return true;
		}

		if ( Utils::is_request( 'admin' ) ) {

			add_action(
				'admin_notices',
				function () use ( $errors ) {
					?>
					<div class="notice notice-error">
						<?php
						foreach ( $errors as $error ) {
							echo '<p>' . esc_html( $error ) . '</p>';
						}
						?>
					</div>
					<?php
				}
			);

			return;
		}

		return false;
	}


	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/core-speed-optimizer/core-speed-optimizer-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/core-speed-optimizer-LOCALE.mo
	 */
	private static function load_plugin_textdomain() {

		// Add plugin's locale.
		$locale = apply_filters( 'plugin_locale', get_locale(), 'core-speed-optimizer' );

		load_textdomain( 'core-speed-optimizer', WP_LANG_DIR . '/core-speed-optimizer/core-speed-optimizer-' . $locale . '.mo' );

		load_plugin_textdomain( 'core-speed-optimizer', false, plugin_basename( __DIR__ ) . '/i18n/languages' );
	}
}
