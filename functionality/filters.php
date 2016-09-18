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
 * Does not allow functionality in admin and checks if the functionality is enabled by checking $setting_name.
 *
 * @since 0.4.0
 *
 * @param string $setting_name The functionality's enabling setting name.
 *
 * @return bool
 */
function rtr_is_applicable( $setting_name ) {
	return ! is_admin() && rtr_get_option( $setting_name );
}