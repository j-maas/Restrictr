<?php
/**
 * Settings
 *
 * Makes settings available to user.
 *
 * @package WordPress
 * @subpackage restrictr
 * @since 0.0.0
 */

/**
 * Option group's name.
 *
 * @since 0.0.0
 * @var string RTR_OPTION_GROUP Name of this plugin's option group.
 */
define( 'RTR_OPTION_GROUP', 'rtr_option_group' );

// ------------------------------------------------------------------
// Register menu and settings
// ------------------------------------------------------------------

/**
 * Register custom settings menu.
 *
 * Registers a custom menu for the plugin's settings.
 *
 * @since 0.0.0
 */
function rtr_add_menu() {
	add_options_page(
		'Restrictr Settings',
		'Restrictr',
		'manage_options',
		'rtr_menu',
		'rtr_settings_page_renderer'
	);
}

add_action( 'admin_menu', 'rtr_add_menu' );

/**
 * Register settings.
 *
 * Registers settings using the `Settings API`.
 *
 * @since 0.0.0
 */
function rtr_settings_init() {
	// Add section
	add_settings_section(
		'rtr_settings_section',
		'Settings',
		'rtr_setting_section_renderer',
		'rtr_menu'
	);

	// Add settings
	register_setting( 'rtr_settings', 'rtr_setting_redirect_destination', 'rtr_setting_redirect_destination_validation' );
	add_settings_field(
		'rtr_setting_redirect_destination',
		'Page to redirect to',
		'rtr_setting_redirect_destination_renderer',
		'rtr_menu',
		'rtr_settings_section'
	);
}

add_action( 'admin_init', 'rtr_settings_init' );

// ------------------------------------------------------------------
// Default and settings' validation functions
// ------------------------------------------------------------------

/**
 * Fetch option or default value.
 *
 * Uses {@see get_option()} to fetch the provided option from the database.
 * If no value is set in the database, a default that is defined inside this function
 * is returned instead.
 *
 * @see get_option()
 * @since 0.0.0
 *
 * @param string $option_name The name of the option to fetch.
 *
 * @return mixed Depends on the option.
 */
function rtr_get_option( $option_name ) {
	$default = array(
		'rtr_setting_redirect_destination' => get_home_url()
	);

	return get_option( $option_name, $default );
}

/**
 * Validates the redirect destination setting.
 *
 * Throws a setting error if the input value is not a valid URL.
 *
 * @since 0.0.0
 *
 * @param string $input The user's input.
 *
 * @return string The user's input if valid, or else the setting's previous value.
 */
function rtr_setting_redirect_destination_validation( $input ) {
	$destination = $input;
	$output      = rtr_get_option( 'rtr_setting_redirect_destination' );

	if ( filter_var( $destination, FILTER_VALIDATE_URL ) === false ) {
		add_settings_error(
			'rtr_setting_redirect_destination',
			'rtr_error_invalid_url',
			'The entered URL was not valid. The previous URL has been re-established.'
		);
	} else {
		$output = $destination;
	}

	return $output;
}

// ------------------------------------------------------------------
// Renderer functions
// ------------------------------------------------------------------

/**
 * Menu page renderer.
 *
 * Renders the menu page of the plugin's settings.
 *
 * @since 0.0.0
 */
function rtr_settings_page_renderer() {
	?>
	<div class="wrap">
		<form action="options.php" method="POST">
			<!-- Use Settings API to render settings -->
			<?php settings_fields( 'rtr_settings' ); ?>
			<?php do_settings_sections( 'rtr_menu' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Setting section renderer.
 *
 * Renders an empty settings section.
 *
 * @since 0.0.0
 */
function rtr_setting_section_renderer() {
}

/**
 * Redirect destination setting's renderer.
 *
 * Renders an URL `input` for the redirect destination setting.
 *
 * @since 0.0.0
 */
function rtr_setting_redirect_destination_renderer() {
	$setting = esc_attr( rtr_get_option( 'rtr_setting_redirect_destination' ) );
	echo "<input name='rtr_setting_redirect_destination' id='rtr_setting_redirect_destination' type='url' value='$setting' />
 			If redirection is enabled, this is the page that will be redirected to.";
}