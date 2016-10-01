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

namespace restrictr\functionality;

class Filtering {

	/**
	 * Singleton method.
	 * Returns or generates the singular instance of this class.
	 *
	 * @since 0.5.0
	 *
	 * @return Filtering
	 */
	public static function get_instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	public function activate() {
		add_action( 'wp_loaded', array( $this, 'filter' ) );
	}

	/**
	 * Activates redirection and hiding functionality, if applicable.
	 * Checks if each functionality is enabled.
	 *
	 * @since 0.4.0
	 */
	public function filter() {
		if ( $this->is_filtering_applicable() ) {

			if ( rtr_get_option( 'rtr_setting_redirect_enabled' ) ) {
				Redirection::get_instance()->activate();
			}

			if ( rtr_get_option( 'rtr_setting_hiding_enabled' ) ) {
				Hiding::get_instance()->activate();
			}
		}
	}

	/**
	 * Determines, if a functionality should be applied.
	 * Does not allow functionality in admin.
	 *
	 * @since 0.4.0
	 *
	 * @return bool
	 */
	private function is_filtering_applicable() {
		$filter_active = true;

		if ( rtr_get_option( 'rtr_setting_filter_enabled' ) ) {
			$filter_active = $this->is_filter_active();
		}

		return ! is_admin() && $filter_active;
	}

	/**
	 * Whether the filter condition is met.
	 *
	 * @since 0.4.0
	 *
	 * @return bool Whether filtering should be active.
	 */
	private function is_filter_active() {
		return ! $this->is_user_in_intranet();
	}

	// IP check
	/**
	 * Checks if user comes from MMWeg network.
	 *
	 * @since 0.4.0
	 *
	 * @return bool Iff the request ip is inside the MMWeg intranet.
	 */
	private function is_user_in_intranet() {
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

		return $this->is_in_subnet( $ipAddress, $subnetMask, $networkAddress );
	}

	/**
	 * Checks if client is in subnet.
	 *
	 * @since 0.4.0
	 *
	 * @param string $client_ip Human readable client ip
	 * @param string $network_ip Human readable network ip
	 * @param $subnet_mask
	 *
	 * @return bool True, iff client ip is inside the subnet.
	 */
	private function is_in_subnet( $client_ip, $network_ip, $subnet_mask ) {
		$masked_client_ip  = inet_pton( $client_ip ) & inet_pton( $subnet_mask );
		$masked_network_ip = inet_pton( $network_ip ) & inet_pton( $subnet_mask );

		return $masked_client_ip == $masked_network_ip;
	}
}