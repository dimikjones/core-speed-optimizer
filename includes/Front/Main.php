<?php
/**
 * Handle front hooks.
 *
 * @class       Front
 * @version     1.0.0
 * @package     Core_Speed_Optimizer/Classes/
 */

namespace Core_Speed_Optimizer\Front;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front main class
 */
final class Main {

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public static function hooks() {
		Assets::hooks();
	}
}
