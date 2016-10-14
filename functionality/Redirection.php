<?php
/**
 * Redirection
 *
 * Implements redirection functionality.
 *
 * @package WordPress
 * @subpackage restrictr
 * @since 0.0.0
 */

namespace restrictr\functionality;

use restrictr\admin\Settings;

class Redirection {

	/**
	 * Singleton: Disallow constructor.
	 *
	 * @since 0.5.0
	 */
	private function __construct() {
		$this->activate();
	}

	/**
	 * Singleton method.
	 * Returns or generates the singular instance of this class.
	 *
	 * @since 0.5.0
	 *
	 * @return Redirection
	 */
	public static function get_instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Activates the redirection functionality.
	 * Adds all necessary action hooks.
	 *
	 * @since 0.4.0
	 */
	public function activate() {
		add_action( 'template_redirect', array( $this, 'redirect' ) );
	}

	/**
	 * Redirects marked posts and pages.
	 *
	 * If the metabox data specifies this post to be hidden, it is
	 * redirected to the destination specified in the plugin's settings.
	 *
	 * @since 0.0.0
	 *
	 * @global \WP_Post $post The current WordPress post object.
	 */
	public function redirect() {
		global $post;
		$redirect_page        = get_post_meta( $post->ID, 'rtr_metabox_redirect_page', true );
		$redirect_destination = Settings::get_option( 'rtr_setting_redirect_destination' );

		if ( $redirect_page ) {
			wp_redirect( $redirect_destination );
		}
	}
}