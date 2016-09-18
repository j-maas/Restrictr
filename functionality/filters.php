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

/**
 * Determines, if a functionality should be applied.
 * Does not allow functionality in admin.
 *
 * @since 0.4.0
 *
 * @return bool
 */
function rtr_is_functionality_applicable() {
	return ! is_admin();
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

rtr_activate_functionality();