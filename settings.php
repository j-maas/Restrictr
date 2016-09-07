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
define( 'RTR_OPTION_GROUP', 'rtr-option-group' );

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
		'rtr-menu',
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
		'rtr-settings-section',
		'Settings',
		'rtr_setting_section_renderer',
		'rtr-menu'
	);

	// Add settings
	register_setting( 'rtr-settings', 'rtr-setting-redirect-destination' );
	add_settings_field(
		'rtr-setting-redirect-destination',
		'Page to redirect to',
		'rtr_setting_redirect_destination_renderer',
		'rtr-menu',
		'rtr-settings-section'
	);
}

add_action( 'admin_init', 'rtr_settings_init' );

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
			<?php settings_fields( 'rtr-settings' ); ?>
			<?php do_settings_sections( 'rtr-menu' ); ?>
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
	$setting = esc_attr( get_option( 'rtr-setting-redirect-destination' ) );
	echo "<input name='rtr-setting-redirect-destination' id='rtr-setting-redirect-destination' type='url' value='$setting' />
 			If redirection is enabled, this is the page that will be redirected to.";
}