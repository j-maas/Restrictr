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

// Only apply functionality on frontend and if enabled and filters allow it.
if ( rtr_is_applicable( 'rtr_setting_redirect_enabled' ) ) {
	add_action( 'template_redirect', 'rtr_redirect' );
}

/**
 * Redirects marked posts and pages.
 *
 * If the metabox data specifies this post to be hidden, it is
 * redirected to the destination specified in the plugin's settings.
 *
 * @since 0.0.0
 *
 * @global WP_Post $post The current WordPress post object.
 */
function rtr_redirect() {
	global $post;
	$redirect_page        = get_post_meta( $post->ID, 'rtr_metabox_redirect_page', true );
	$redirect_destination = rtr_get_option( 'rtr_setting_redirect_destination' );

	if ( $redirect_page ) {
		wp_redirect( $redirect_destination );
	}
}