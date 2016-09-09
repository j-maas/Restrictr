<?php
/**
 * Metabox
 *
 * Makes the metabox available on pages.
 *
 * @package WordPress
 * @subpackage restrictr
 * @since 0.0.0
 */

/**
 * Registers the metabox.
 *
 * Adds a metabox to all post types.
 *
 * @since 0.0.0
 */
function rtr_add_metabox() {
	add_meta_box(
		'rtr_metabox',
		'Restrictr Settings',
		'rtr_metabox_renderer',
		'',
		'normal',
		'default'
	);
}

add_action( 'add_meta_boxes', 'rtr_add_metabox' );

/**
 * Renders the metabox.
 *
 * The callback function for this plugin's metabox.
 *
 * @since 0.0.0
 *
 * @param array $post The current post, provided by WordPress.
 */
function rtr_metabox_renderer( $post ) {
	$redirect_page        = get_post_meta( $post->ID, 'rtr_metabox_redirect_page', true );
	$redirect_destination = rtr_get_option( 'rtr_setting_redirect_destination' );

	wp_nonce_field( 'rtr_metabox', 'rtr_metabox_nonce' );
	?>
	<p>
		<input type="checkbox" id="rtr_metabox_redirect_page" name="rtr_metabox_redirect_page"
		       value="yes" <?php checked( $redirect_page, 'yes' ); ?> />
		<label for="rtr_meta_redirect_page">Redirect this page
			(to <a href="<?php echo $redirect_destination ?>"
			       style="word-wrap: break-word"><?php echo $redirect_destination ?></a>)</label>
	</p>
	<?php
}

/**
 * Saves metabox data and validates security.
 *
 * Checks for autosave, missing or invalid nonce, invalid user permission and bails if
 * any of those conditions is true.
 * Else it updates the metabox data with the input.
 *
 * @since 0.0.0
 *
 * @param int $post_id The saved post's ID, provided by WordPress.
 */
function rtr_metabox_save( $post_id ) {
	// Checks save status
	$is_autosave    = wp_is_post_autosave( $post_id );
	$is_revision    = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST['rtr_metabox_nonce'] )
	                    && wp_verify_nonce( $_POST['rtr_metabox_nonce'], 'rtr_metabox' ) ) ? true : false;

	// Exits script depending on save status
	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}

	// Update metabox data
	if ( isset( $_POST['rtr_metabox_redirect_page'] ) ) {
		update_post_meta( $post_id, 'rtr_metabox_redirect_page', 'yes' );
	} else {
		update_post_meta( $post_id, 'rtr_metabox_redirect_page', '' );
	}

	if ( isset( $_POST['rtr_metabox_hide_page'] ) ) {
		update_post_meta( $post_id, 'rtr_metabox_hide_page', 'yes' );
	} else {
		update_post_meta( $post_id, 'rtr_metabox_hide_page', '' );
	}
}

add_action( 'save_post', 'rtr_metabox_save' );