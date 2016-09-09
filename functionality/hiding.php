<?php
/**
 * Hiding
 *
 * Implements hiding of pages and posts from menus.
 *
 * @package WordPress
 * @subpackage restrictr
 * @since 0.1.0
 */

function rtr_hide_from_menu( $items ) {
	// Contains IDs of all hidden menu items
	$hidden_posts = array();

	foreach ( $items as $key => $menu_item ) {
		// Improvement: https://github.com/Giuseppe-Mazzapica/Url_To_Query
		$referenced_page_id = url_to_postid( $menu_item->url );

		// Skip this $menu_item if possible
		if ( $referenced_page_id == 0 ) { // if page referenced is external, it cannot have metabox data
			continue;
		}

		$hide_page           = get_post_meta( $referenced_page_id, 'rtr_metabox_hide_page', true );
		$menu_item_parent_id = $menu_item->menu_item_parent;

		if ( $hide_page || isset( $hidden_posts[ $menu_item_parent_id ] ) ) {
			unset( $items[ $key ] );
			$hidden_posts[ $menu_item->ID ] = 'hidden';
		}
	}

	return $items;
}

if ( ! is_admin() ) {
	add_filter( 'wp_get_nav_menu_items', 'rtr_hide_from_menu' );
}