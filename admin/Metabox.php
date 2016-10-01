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

namespace restrictr\admin;

class Metabox {

	/**
	 * Singleton method.
	 * Returns or generates the singular instance of this class.
	 *
	 * @since 0.5.0
	 *
	 * @return Metabox
	 */
	public static function get_instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Activates
	 * Adds all filters necessary for hiding functionality.
	 *
	 * @since 0.5.0
	 */
	public function activate() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'metabox_save' ) );
	}

	/**
	 * Registers the metabox.
	 *
	 * Adds a metabox to all post types.
	 *
	 * @since 0.0.0
	 */
	function add_metabox() {
		add_meta_box(
			'rtr_metabox',
			'Restrictr Settings',
			'metabox_renderer',
			'',
			'normal',
			'default'
		);
	}


	/**
	 * Renders the metabox.
	 *
	 * The callback function for this plugin's metabox.
	 *
	 * @since 0.0.0
	 *
	 * @param array $post The current post, provided by WordPress.
	 */
	function metabox_renderer( $post ) {
		$redirect_page        = get_post_meta( $post->ID, 'rtr_metabox_redirect_page', true );
		$redirect_destination = Settings::get_option( 'rtr_setting_redirect_destination' );

		$hide_page = get_post_meta( $post->ID, 'rtr_metabox_hide_page', true );

		wp_nonce_field( 'rtr_metabox', 'rtr_metabox_nonce' );
		?>
		<!-- Redirect checkbox -->
		<p>
			<input type="checkbox" id="rtr_metabox_redirect_page" name="rtr_metabox_redirect_page"
			       value="yes" <?php checked( $redirect_page, 'yes' ); ?> />
			<label for="rtr_metabox_redirect_page">Redirect this page
				(to <a href="<?php echo $redirect_destination ?>"
				       style="word-wrap: break-word"><?php echo $redirect_destination ?></a>)</label>
		</p>

		<!-- Hide checkbox -->
		<p>
			<input type="checkbox" id="rtr_metabox_hide_page" name="rtr_metabox_hide_page"
			       value="yes" <?php checked( $hide_page, 'yes' ); ?> />
			<label for="rtr_metabox_hide_page">Hide this page and subpages from menus</label>

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
	function metabox_save( $post_id ) {
		// Following check is courtesy of http://themefoundation.com/wordpress-meta-boxes-guide/:
		// ------------------------------------------------------------------------------------------------------
		// Checks save status
		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['rtr_metabox_nonce'] )
		                    && wp_verify_nonce( $_POST['rtr_metabox_nonce'], 'rtr_metabox' ) ) ? true : false;

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}
		// ------------------------------------------------------------------------------------------------------

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

}