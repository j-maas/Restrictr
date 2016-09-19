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
 * Activates functionality, if determined true.
 *
 * @return bool Whether filtering should be active.
 */
function rtr_filter() {
	return ! is_user_in_intranet();
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

// IP check
/**
 * Checks if client is in subnet.
 *
 * @param string $client_ip Human readable client ip
 * @param string $network_ip Human readable network ip
 * @param $subnet_mask
 *
 * @return bool True, iff client ip is inside the subnet.
 */
function is_in_subnet( $client_ip, $network_ip, $subnet_mask ) {
	$masked_client_ip  = inet_pton( $client_ip ) & inet_pton( $subnet_mask );
	$masked_network_ip = inet_pton( $network_ip ) & inet_pton( $subnet_mask );

	return $masked_client_ip == $masked_network_ip;
}

/**
 * Checks if user comes from MMWeg network.
 *
 * @return bool Iff the request ip is inside the MMWeg intranet.
 */
function is_user_in_intranet() {
	$subnetMask          = "255.255.254.0";
	$networkAddress      = "192.168.122.0";
	$trustedProxyAddress = [ "127.0.0.1", "::1" ];

	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ipAddress = $_SERVER['REMOTE_ADDR'];
	} else {
		return false;
	}

	// Check if forward from trusted proxy
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] )
	     and in_array( $ipAddress, $trustedProxyAddress )
	) {
		$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}

	return is_in_subnet( $ipAddress, $subnetMask, $networkAddress );
}