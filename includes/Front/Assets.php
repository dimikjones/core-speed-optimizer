<?php
/**
 * Register frontend assets.
 *
 * @class       FrontAssets
 * @version     1.0.0
 * @package     Core_Speed_Optimizer/Classes/
 */

namespace Core_Speed_Optimizer\Front;

use Core_Speed_Optimizer\Assets as AssetsMain;
use Core_Speed_Optimizer\Utils;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend assets class
 */
final class Assets {

	/**
	 * Hook in methods.
	 */
	public static function hooks() {
		add_filter( 'core_speed_optimizer_enqueue_styles', array( __CLASS__, 'add_styles' ), 9 );
		add_filter( 'core_speed_optimizer_enqueue_scripts', array( __CLASS__, 'add_scripts' ), 9 );
		add_action( 'wp_enqueue_scripts', array( AssetsMain::class, 'load_scripts' ) );
		add_action( 'wp_print_scripts', array( AssetsMain::class, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( AssetsMain::class, 'localize_printed_scripts' ), 5 );
	}


	/**
	 * Add styles for the admin.
	 *
	 * @param array $styles Admin styles.
	 * @return array<string,array>
	 */
	public static function add_styles( $styles ) {

		$styles['core-speed-optimizer-general'] = array(
			'src' => AssetsMain::localize_asset( 'front.css' ),
		);

		return $styles;
	}


	/**
	 * Add scripts for the admin.
	 *
	 * @param  array $scripts Admin scripts.
	 * @return array<string,array>
	 */
	public static function add_scripts( $scripts ) {

		$scripts['core-speed-optimizer-general'] = array(
			'src'  => AssetsMain::localize_asset( 'front.js' ),
			'data' => array(
				'ajax_url' => Utils::ajax_url(),
			),
		);

		return $scripts;
	}
}
