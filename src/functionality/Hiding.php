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

namespace restrictr\functionality;

class Hiding {

	/**
	 * Singleton: Disallow constructor.
	 *
	 * @since 0.5.0
	 */
	private function __construct() {
		$this->activate();
	}

	/**
	 * Activates hiding functionality.
	 * Adds all filters necessary for hiding functionality.
	 *
	 * @since 0.4.0
	 */
	public function activate() {
		add_filter( 'wp_get_nav_menu_items', array( $this, 'hide_from_menu' ) );
		add_filter( 'get_pages', array( $this, 'remove_hidden_pages' ) );
		add_action( 'pre_get_posts', array( $this, 'exclude_hidden_from_query' ) );
		add_filter( 'option_sticky_posts', array( $this, 'hide_sticky' ) );
		add_filter( 'get_comment', array( $this, 'hide_comments' ) );
	}

	/**
	 * Singleton method.
	 * Returns or generates the singular instance of this class.
	 *
	 * @since 0.5.0
	 *
	 * @return Hiding
	 */
	public static function get_instance() {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Hides menu items that point to hidden posts.
	 * Only hides a page, if the menu item's link is successfully converted to an ID.
	 *
	 * @see \restrictr\functionality\url_to_postid()
	 * @since 0.1.0
	 *
	 * @param array $items The menu items, provided by WordPress.
	 *
	 * @return mixed
	 */
	public function hide_from_menu( $items ) {
		// Contains IDs of all hidden menu items
		$hidden_posts = array();

		foreach ( $items as $key => $menu_item ) {
			// Improvement: Does not work on costum post type slug
			$referenced_page_id = $this->url_to_postid( $menu_item->url );

			// Skip this $menu_item if possible
			if ( $referenced_page_id == 0 ) { // if page referenced is external, it cannot have metabox data
				error_log( 'Hiding::hide_from_menu: Page with URL ' . $menu_item->url . ' not found.' );
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

	/**
	 * Wrapper. Converts URL to corresponding post id.
	 * Extends {@see url_to_postid()} to also convert the blog's permalink.
	 *
	 * @see url_to_postid()
	 * @since 0.5.0
	 *
	 * @param string $url The URL to convert.
	 *
	 * @return int Post ID, or 0 on failure.
	 */
	private function url_to_postid( $url ) {
		$page_id = url_to_postid( $url );

		// Check if blog
		$blog_id = get_option( 'page_for_posts' );
		if ( $url == get_permalink( $blog_id ) ) {
			$page_id = $blog_id;
		}

		return $page_id;
	}

	/**
	 * Removes pages that are marked as hidden from the array.
	 * Uses recursive search for hiding child pages of hidden parent pages.
	 *
	 * @since 0.3.0
	 *
	 * @param array $pages The array of pages to filter
	 * @param array $hidden_pages Contains the ids of already hidden pages.
	 *
	 * @return array The input array without pages that are marked as hidden
	 */
	public function remove_hidden_pages( $pages, $hidden_pages = array() ) {
		$altered = false; // Recursion break condition

		foreach ( $pages as $index => $page ) {
			$hide_page = get_post_meta( $page->ID, 'rtr_metabox_hide_page', true );
			$parent_id = $page->post_parent;

			if ( $hide_page || isset( $hidden_pages[ $parent_id ] ) ) {
				unset( $pages[ $index ] );
				$hidden_pages[ $page->ID ] = 'hidden';

				$altered = true;
			}
		}

		if ( $altered ) {
			$pages = $this->remove_hidden_pages( $pages, $hidden_pages );
		}

		return $pages;
	}

	/**
	 * Excludes posts that are marked as hidden from the query.
	 *
	 * @since 0.6.1
	 *
	 * @param \WP_Query $query
	 */
	public function exclude_hidden_from_query( $query ) {
		$meta_query_vars = array(
			'relation' => 'OR',
			array(
				'key'     => 'rtr_metabox_hide_page',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'   => 'rtr_metabox_hide_page',
				'value' => '',
			),
		);

		$existing_meta_query = $query->get( 'meta_query', null );
		if ( ! isset( $existing_meta_query ) ) {
			$query->set( 'meta_query', $meta_query_vars );
		} else {
			$query->set( 'meta_query', array(
					$existing_meta_query,
					$meta_query_vars,
				)
			);
		}
	}

	/**
	 * Filters out the IDs of sticky posts that should be hidden.
	 * Masks {@see get_option()} when used to get stickies.
	 *
	 * @since 0.3.0
	 *
	 * @param array $stickies The IDs of the sticky posts. Provided by WordPress.
	 *
	 * @return array The IDs of non-hidden sticky posts.
	 */
	public function hide_sticky( $stickies ) {
		foreach ( $stickies as $index => $sticky_id ) {
			$hide_page = get_post_meta( $sticky_id, 'rtr_metabox_hide_page', true );

			if ( $hide_page ) {
				unset( $stickies[ $index ] );
			}
		}

		return $stickies;
	}

	/**
	 * Hides comments for hidden pages.
	 *
	 * @since 0.3.0
	 *
	 * @param \WP_Comment|array|null $comments The comment or list of comments. Provided by WordPress.
	 *
	 * @return \WP_Comment|array|null The input but without comments for hidden pages.
	 */
	function hide_comments( $comments ) {
		if ( is_array( $comments ) ) {
			foreach ( $comments as $index => $comment ) {
				$hide_page = get_post_meta( $comment->comment_post_ID, 'rtr_metabox_hide_page', true );

				if ( $hide_page ) {
					unset( $comments[ $index ] );
				}
			}
		} elseif ( $comments ) {
			$hide_page = get_post_meta( $comments->comment_post_ID, 'rtr_metabox_hide_page', true );

			if ( $hide_page ) {
				$comments = null;
			}
		}

		return $comments;
	}
}