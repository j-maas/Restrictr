<?php
/**
 * Uninstall
 *
 * @package WordPress
 * @subpackage restrictr
 * @since 0.6.1
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// ------------------------------------------------------------------
// Remove options
// ------------------------------------------------------------------

$option_names = array(
	'rtr_setting_filter_enabled',
	'rtr_setting_redirect_enabled',
	'rtr_setting_redirect_destination',
	'rtr_setting_hiding_enabled',
);

foreach ( $option_names as $option ) {
	delete_option( $option );
}

// ------------------------------------------------------------------
// Delete post meta
// ------------------------------------------------------------------

$metabox_data_names = array(
	'rtr_metabox_redirect_page',
	'rtr_metabox_hide_page',
);

foreach ( $metabox_data_names as $metabox_data ) {
	delete_post_meta_by_key( $metabox_data );
}