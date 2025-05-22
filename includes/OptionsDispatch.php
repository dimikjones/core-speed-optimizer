<?php
/**
 * Register admin assets.
 *
 * @class       OptionsDispatch
 * @version     1.0.0
 * @package     Core_Speed_Optimizer/Classes/
 */

namespace Core_Speed_Optimizer;

use Core_Speed_Optimizer\Admin\PluginOptions as Options;

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
		add_filter( 'wp_enqueue_scripts', array( __CLASS__, 'disable_heartbeat_frontend' ), 100 );
		add_filter( 'heartbeat_settings', array( __CLASS__, 'control_heartbeat_settings' ) );
		add_action( 'init', array( __CLASS__, 'disable_emojis' ) );
	}


	/**
	 * Dequeue block editor styles on frontend.
	 */
	public static function disable_block_editor_styles_frontend() {
		if ( Utils::is_request( 'frontend' ) ) {

			if ( get_option( 'core_speed_optimizer_options' )['disable_block_editor_styles_frontend'] ) {
				// Removes core block styles.
				wp_dequeue_style( 'wp-block-library' );
				// Removes theme block styles.
				wp_dequeue_style( 'wp-block-library-theme' );
			}
		}
	}

	/**
	 * Disable the WordPress Heartbeat API on the frontend but keep it active in the admin and post editor.
	 */
	public static function disable_heartbeat_frontend() {
		if ( Utils::is_request( 'frontend' ) ) {

			if ( get_option( 'core_speed_optimizer_options' )['disable_heartbeat_frontend'] ) {
				// Removes Heartbeat API script from loading.
				wp_deregister_script( 'heartbeat' );
			}
		}
	}

	/**
	 * Control the Heartbeat API execution based on user area.
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public static function control_heartbeat_settings( $settings ) {

		if ( get_option( 'core_speed_optimizer_options' )['control_heartbeat_settings'] ) {
			if ( Utils::is_request( 'frontend' ) ) {
				// Slower execution for frontend.
				$settings['interval'] = 60;
			} else {
				// Faster execution in admin.
				$settings['interval'] = 30;
			}
		}

		return $settings;
	}

	/**
	 * Disable all WordPress emoji scripts and styles.
	 */
	public static function disable_emojis() {

		if ( get_option( 'core_speed_optimizer_options' )['disable_emojis'] ) {
			// Remove emoji script from frontend and admin.
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

			// Remove emoji styles from frontend and admin.
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );

			// Prevent emojis from being injected in the RSS feed.
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

			// Remove TinyMCE emoji support (editor compatibility).
			add_filter( 'tiny_mce_plugins', array( __CLASS__, 'emojis_tinymce' ) );
			add_filter( 'wp_resource_hints', array( __CLASS__, 'emojis_remove_dns_prefetch' ), 10, 2 );
		}
	}

	/**
	 * Disable emojis tinymce.
	 */
	public static function emojis_tinymce( $plugins ) {
		// Bail if the plugins is not an array.
		if ( ! is_array( $plugins ) ) {
			return array();
		}

		// Remove the `wpemoji` plugin and return everything else.
		return array_diff( $plugins, array( 'wpemoji' ) );
	}

	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @param  array  $urls          URLs to print for resource hints.
	 * @param  string $relation_type The relation type the URLs are printed for.
	 * @return array                 Difference betwen the two arrays.
	 */
	public static function emojis_remove_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}

		return $urls;
	}
}
