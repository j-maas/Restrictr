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

/**
 * Redirects marked pages.
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
	$hide_page            = get_post_meta( $post->ID, 'rtr_metabox_hide_page', true );
	$redirect_destination = rtr_get_option( 'rtr_setting_redirect_destination' );

	if ( $hide_page ) {
		wp_redirect( $redirect_destination );
	}
}

add_action( 'template_redirect', 'rtr_redirect' );