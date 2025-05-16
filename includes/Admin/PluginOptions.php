<?php
/**
 * Register plugin admin page with options.
 *
 * @class       AdminPluginOptions
 * @version     1.0.0
 * @package     Core_Speed_Optimizer/Classes/
 */

namespace Core_Speed_Optimizer\Admin;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Core_Speed_Optimizer\AdminPage as AdminPageMain;

class PluginOptions extends AdminPageMain {

	/**
	 * Hook in methods.
	 */
	public static function hooks() {
		$instance = new PluginOptions();

		// Register our options_page to the admin_menu action hook.
		add_action( 'admin_menu', array( $instance, 'add_admin_page' ) );

		// Register our settings_init to the admin_init action hook.
		add_action( 'admin_init', array( $instance, 'configure_options' ) );
	}

	/**
	 * Adds the admin page to the menu.
	 */
	public function add_admin_page() {
		add_menu_page(
			$this->get_page_title(),
			$this->get_page_title(),
			'manage_options',
			$this->get_menu_slug(),
			array( $this, 'options_page_html' ),
			'dashicons-chart-pie'
		);
	}

	/**
	 * Configure the options page.
	 */
	public function configure_options() {
		$fields = [
			[
				'type'        => 'text',
				'id'          => 'core_speed_optimizer_options_some_text',
				'label'       => 'Text Field',
				'description' => __( 'Enter a custom text value here.', 'core-speed-optimizer' ),
			],
			[
				'type'        => 'checkbox',
				'id'          => 'disable_block_editor_styles_frontend',
				'label'       => 'Disable Block Editor Styles on Frontend',
				'description' => __( 'If not using Blocks disable their styles.', 'core-speed-optimizer' ),
			],
			[
				'type'        => 'select',
				'id'          => 'custom_select',
				'label'       => 'Select Option',
				'description' => __( 'Choose one option from the dropdown.', 'core-speed-optimizer' ),
				'options'     => [ 'Option 1', 'Option 2', 'Option 3' ],
			],
			[
				'type'        => 'toggle',
				'id'          => 'custom_toggle',
				'label'       => 'Enable Dark Mode',
				'description' => __( 'Toggle this setting to switch between dark and light mode.', 'core-speed-optimizer' ),
			],
		];

		foreach ( $fields as $field ) {
			add_settings_field(
				$field['id'],
				$field['label'],
				[ $this, 'render_field' ],
				$this->get_menu_slug(),
				'core_speed_optimizer_options',
				// Passing full field data.
				$field
			);

			register_setting( $this->get_menu_slug(), $field['id'] );
		}

		add_settings_section(
			'core_speed_optimizer_options',
			__( 'Assets', 'core-speed-optimizer' ),
			[ $this, 'render_section' ],
			$this->get_menu_slug()
		);
	}

	/**
	 * Render the options page.
	 */
	public function options_page_html() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// WPCS: input var ok.
		if ( isset( $_REQUEST['settings-updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			// Add settings saved message with the class of "updated".
			add_settings_error( 'messages', 'message', esc_html__( 'Settings Saved', 'core-speed-optimizer' ), 'updated' );
		}

		// Show error/update messages.
		settings_errors( 'messages' );
		?>
		<div class="wrap" id="core-speed-optimizer-options">
			<h1><?php echo esc_html( $this->get_page_title() ); ?></h1>
			<p>I want some introduction paragraph about my plugin here.</p>
			<form action="options.php" method="POST">
				<?php settings_fields( $this->get_menu_slug() ); ?>
				<?php do_settings_sections( $this->get_menu_slug() ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	// Render different field types dynamically.
	public function render_field( $args ) {
		$value = get_option( $args['id'], '' );

		switch ( $args['type'] ) {
			case 'text':
				echo '<input type="text" name="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $value ) . '" />';
				break;
			case 'checkbox':
				$checked = checked( 1, $value, false );
				echo '<input type="checkbox" name="' . esc_attr( $args['id'] ) . '" value="1" ' . esc_attr( $checked ) . ' />';
				break;
			case 'select':
				echo '<select name="' . esc_attr( $args['id'] ) . '">';
				foreach ( $args['options'] as $option ) {
					$selected = selected( $value, $option, false );
					echo '<option value="' . esc_attr( $option ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $option ) . '</option>';
				}
				echo '</select>';
				break;
			case 'toggle':
				$checked = checked( 1, $value, false );
				echo '<label class="switch">
                        <input type="checkbox" name="' . esc_attr( $args['id'] ) . '" value="1" ' . esc_attr( $checked ) . '>
                        <span class="slider round"></span>
                      </label>';
				break;
		}

		// Display the description below the field.
		if ( ! empty( $args['description'] ) ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
	}

	public function render_settings_page() {
		echo '<div class="wrap"><h2>My Settings Page</h2>';
		echo '<form method="post" action="options.php">';
		settings_fields( $this->get_menu_slug() );
		do_settings_sections( $this->get_menu_slug() );
		submit_button();
		echo '</form></div>';
	}

	/**
	 * Renders the section.
	 */
	public function render_section() {
		?>
		<p><?php esc_html_e( 'Boost WordPress performance with these options', 'core-speed-optimizer' ); ?></p>
		<?php
	}

	/**
	 * Get the options page menu slug.
	 *
	 * @return string
	 */
	protected function get_menu_slug() {
		return 'core_speed_optimizer_options';
	}

	/**
	 * Get the options page title.
	 *
	 * @return string
	 */
	protected function get_page_title() {
		return __( 'Core Speed', 'core-speed-optimizer' );
	}
}
