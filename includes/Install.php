<?php
/**
 * Handle plugin's install actions.
 *
 * @class       Install
 * @version     1.0.0
 * @package     Core_Speed_Optimizer/Classes/
 */

namespace Core_Speed_Optimizer;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Install class
 */
final class Install {

	/**
	 * Install action.
	 */
	public static function install( $sitewide = false ) {

		// Perform install actions here.

		// Trigger action.
		do_action( 'core_speed_optimizer_installed', $sitewide );
	}
}
