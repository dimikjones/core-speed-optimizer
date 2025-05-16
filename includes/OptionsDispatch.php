<?php
/**
 * Register admin assets.
 *
 * @class       OptionsDispatch
 * @version     1.0.0
 * @package     Core_Speed_Optimizer/Classes/
 */

namespace Core_Speed_Optimizer;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin assets class
 */
final class OptionsDispatch {

	/**
	 * Hook in methods.
	 */
	public static function hooks() {
		add_filter( 'wp_enqueue_scripts', array( __CLASS__, 'disable_block_editor_styles_frontend' ), 100 );
	}


	/**
	 * Dequeue block editor styles on frontend.
	 */
	public static function disable_block_editor_styles_frontend() {
		if ( Utils::is_request( 'frontend' ) ) {

			if ( get_option( 'disable_block_editor_styles_frontend' ) ) {
				// Removes core block styles.
				wp_dequeue_style( 'wp-block-library' );
				// Removes theme block styles.
				wp_dequeue_style( 'wp-block-library-theme' );
			}
		}
	}
}
