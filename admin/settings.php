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
 * @since 0.2.0 Registers multiple sections.
 */
function rtr_settings_init() {
	// Add sections
	add_settings_section(
		'rtr_settings_section_redirection',
		'Redirection',
		'rtr_setting_section_redirection_renderer',
		'rtr_menu'
	);

	add_settings_section(
		'rtr_settings_section_hiding',
		'Hiding',
		'rtr_setting_section_hiding_renderer',
		'rtr_menu'
	);

	// Add settings
	// Redirection
	register_setting( 'rtr_settings', 'rtr_setting_redirect_enabled' );
	add_settings_field(
		'rtr_setting_redirect_enabled',
		'Enable redirection',
		'rtr_setting_redirect_enabled_renderer',
		'rtr_menu',
		'rtr_settings_section_redirection'
	);

	register_setting( 'rtr_settings', 'rtr_setting_redirect_destination', 'rtr_setting_redirect_destination_validation' );
	add_settings_field(
		'rtr_setting_redirect_destination',
		'Redirect to (URL)',
		'rtr_setting_redirect_destination_renderer',
		'rtr_menu',
		'rtr_settings_section_redirection'
	);

	add_settings_field(
		'rtr_setting_field_redirected_pages',
		'Redirected posts and pages',
		'rtr_setting_field_redirected_pages_renderer',
		'rtr_menu',
		'rtr_settings_section_redirection'
	);

	// Hiding
	register_setting( 'rtr_settings', 'rtr_setting_hiding_enabled' );
	add_settings_field(
		'rtr_setting_hiding_enabled',
		'Enable hiding',
		'rtr_setting_hiding_enabled_renderer',
		'rtr_menu',
		'rtr_settings_section_hiding'
	);

	add_settings_field(
		'rtr_setting_field_hidden_pages',
		'Hidden posts and pages',
		'rtr_setting_field_hidden_pages_renderer',
		'rtr_menu',
		'rtr_settings_section_hiding'
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
	// Provide defaults
	$default = null;
	switch ( $option_name ) {
		case 'rtr_setting_redirect_enabled':
			$default = 1;
			break;

		case 'rtr_setting_redirect_destination':
			$default = get_home_url();
			break;

		case 'rtr_setting_hiding_enabled':
			$default = 1;
			break;
	}

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
	<!--suppress HtmlUnknownTarget -->
	<div class="wrap">
		<form action="options.php" method="POST">
			<h1>Restrictr Settings</h1>
			<!-- Use Settings API to render settings -->
			<?php settings_fields( 'rtr_settings' ); ?>
			<?php do_settings_sections( 'rtr_menu' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

// Setting sections

/**
 * Redirection setting section renderer.
 *
 * Renders an empty settings section.
 *
 * @since 0.2.0
 */
function rtr_setting_section_redirection_renderer() {
}

/**
 * Redirection setting section renderer.
 *
 * Renders an empty settings section.
 *
 * @since 0.2.0
 */
function rtr_setting_section_hiding_renderer() {
}

// Setting fields
// Abstract

/**
 * Checkbox setting renderer.
 *
 * @since 0.2.0
 *
 * @param string $setting_name The name of the corresponding setting.
 * @param string $description Optional. The description to output as a label for the checkbox.
 */
function rtr_setting_checkbox_renderer( $setting_name, $description = '' ) {
	$setting = rtr_get_option( $setting_name );

	echo "<input type='checkbox' name='$setting_name' id='$setting_name'
	       value='1' " . checked( $setting, 1, false ) . ' />';

	if ( $description ) {
		echo "<label for='$setting_name' class='description'>$description</label>";
	}
}

/**
 * Metabox data list pages renderer.
 *
 * Renders a list of all posts and pages that are marked with certain metabox data.
 *
 * @since 0.2.0
 *
 * @param string $setting_name The name of the metabox data to search for.
 * @param string $mark_name The metabox value which marks posts to select.
 * @param string $nothing_text Message displayed when no posts are marked.
 */
function rtr_setting_meta_list_renderer( $setting_name, $mark_name, $nothing_text ) {
	$marked_pages = get_posts( array(
		'meta_key'    => $setting_name,
		'meta_value'  => $mark_name,
		'post_type'   => 'any',
		'orderby'     => array(
			'post_type',
			'post_title'
		),
		'numberposts' => - 1,
	) );

	if ( $marked_pages ):
		?>
		<table class="widefat striped">
			<tr>
				<th id="redirected-name"><span>Name</span></th>
			</tr>
			<?php foreach ( $marked_pages as $index => $page ): ?>
				<tr>
					<td>
						<a href="<?php echo get_the_permalink( $page->ID ); ?>"><?php echo get_the_title( $page->ID ); ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
	else:
		?>
		<span><?php echo $nothing_text; ?></span>
		<?php
	endif;
}

// Settings

/**
 * Redirect enable setting's renderer.
 *
 * Renders a checkbox for the redirect enable setting.
 *
 * @since 0.2.0
 */
function rtr_setting_redirect_enabled_renderer() {
	rtr_setting_checkbox_renderer( 'rtr_setting_redirect_enabled', 'If disabled, no page will be redirected.' );
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
	echo "<input type='url' name='rtr_setting_redirect_destination' id='rtr_setting_redirect_destination'
 				value='$setting' class='regular-text code' />
 			<p class='description'>If redirection is enabled on a page, it will be redirected to this URL.</p>";
}

/**
 * Redirected pages renderer.
 *
 * Renders a list of all posts and pages that have redirection enabled.
 *
 * @since 0.2.0
 */
function rtr_setting_field_redirected_pages_renderer() {
	rtr_setting_meta_list_renderer(
		'rtr_metabox_redirect_page',
		'yes',
		'No post or page is redirected.'
	);
}

/**
 * Hiding enable setting's renderer.
 *
 * Renders a checkbox for the hiding enable setting.
 *
 * @since 0.2.0
 */
function rtr_setting_hiding_enabled_renderer() {
	rtr_setting_checkbox_renderer( 'rtr_setting_hiding_enabled', 'If disabled, no page will be hidden.' );
}

/**
 * Hidden pages renderer.
 *
 * Renders a list of all posts and pages that have hiding enabled.
 *
 * @since 0.2.0
 */
function rtr_setting_field_hidden_pages_renderer() {
	rtr_setting_meta_list_renderer(
		'rtr_metabox_hide_page',
		'yes',
		'No post or page is hidden.'
	);
}