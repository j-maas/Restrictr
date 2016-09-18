<?php
/**
 * Filters
 *
 * Implements control over other functionality through filters.
 *
 * @package WordPress
 * @subpackage restrictr
 * @since 0.4.0
 */

function rtr_filter() {
	return ! is_user_logged_in();
}

/**
 * Determines, if a functionality should be applied.
 * Does not allow functionality in admin.
 *
 * @since 0.4.0
 *
 * @return bool
 */
function rtr_is_functionality_applicable() {
	$filter_active = true;

	if ( rtr_get_option( 'rtr_setting_filter_enabled' ) ) {
		$filter_active = rtr_filter();
	}

	return ! is_admin() && $filter_active;
}

/**
 * Activates redirection and hiding functionality, if applicable.
 * Checks if each functionality is enabled.
 *
 * @since 0.4.0
 */
function rtr_activate_functionality() {
	if ( rtr_is_functionality_applicable() ) {

		if ( rtr_get_option( 'rtr_setting_redirect_enabled' ) ) {
			rtr_activate_redirection();
		}

		if ( rtr_get_option( 'rtr_setting_hiding_enabled' ) ) {
			rtr_activate_hiding();
		}
	}
}

add_action( 'wp_loaded', 'rtr_activate_functionality' );