<?php
/**
 * Main file
 *
 * Entry point for the restrictr plugin.
 *
 * @package WordPress
 * @subpackage restrictr
 * @since 0.0.0
 */

/*
Plugin Name: Restrictr
Description: Restrict and hide pages under custom conditions
Version:     0.2.0
Author:      Johannes Maas
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Plugin's directory path.
 *
 * @since 0.0.0
 * @var string RTR_PLUGIN_DIRECTORY Path to the plugin's base directory.
 */
define( 'RTR_PLUGIN_DIRECTORY', plugin_dir_path( __FILE__ ) );

// ------------------------------------------------------------------
// Include admin screen
// ------------------------------------------------------------------
/**
 * Path to admin directory.
 */
define( 'RTR_ADMIN_DIRECTORY', RTR_PLUGIN_DIRECTORY . 'admin/' );

/**
 * Provides the metabox on pages.
 */
/** @noinspection PhpIncludeInspection */
include( RTR_ADMIN_DIRECTORY . 'metabox.php' );

/**
 * Provides settings to user.
 */
/** @noinspection PhpIncludeInspection */
include( RTR_ADMIN_DIRECTORY . 'settings.php' );

// ------------------------------------------------------------------
// Include functionality
// ------------------------------------------------------------------
/**
 * Path to functionality directory.
 */
define( 'RTR_FUNCTIONALITY_DIRECTORY', RTR_PLUGIN_DIRECTORY . 'functionality/' );

/**
 * Provides redirection functionality.
 */
/** @noinspection PhpIncludeInspection */
include( RTR_FUNCTIONALITY_DIRECTORY . 'redirection.php' );

/**
 * Provides hiding functionality in menus.
 */
/** @noinspection PhpIncludeInspection */
include( RTR_FUNCTIONALITY_DIRECTORY . 'hiding.php' );