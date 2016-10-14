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
Version:     0.6.1
Author:      Johannes Maas
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: restrictr
Domain Path: /languages/
*/

// ------------------------------------------------------------------
// I18n
// ------------------------------------------------------------------
/**
 * Loads the text domain.
 *
 * @since 0.6.0
 */
function load_restrictr_textdomain() {
	load_plugin_textdomain( 'restrictr', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'load_restrictr_textdomain' );

// ------------------------------------------------------------------
// Autoload classes
// ------------------------------------------------------------------
include( 'Psr4AutoloaderClass.php' );

$loader = new \restrictr\Psr4AutoloaderClass();
$loader->addNamespace('restrictr', dirname(__FILE__) . 'src');
$loader->register();

//// ------------------------------------------------------------------
//// Constants
//// ------------------------------------------------------------------
//
///**
// * Plugin's directory path.
// *
// * @since 0.0.0
// * @var string RTR_PLUGIN_DIRECTORY Path to the plugin's base directory.
// */
//define( 'RTR_PLUGIN_DIRECTORY', plugin_dir_path( __FILE__ ) );
//
//// ------------------------------------------------------------------
//// Include admin screen
//// ------------------------------------------------------------------
///**
// * Path to admin directory.
// */
//define( 'RTR_ADMIN_DIRECTORY', RTR_PLUGIN_DIRECTORY . 'admin/' );
//
///**
// * Provides the metabox on pages.
// */
///** @noinspection PhpIncludeInspection */
//include( RTR_ADMIN_DIRECTORY . 'Metabox.php' );
//restrictr\admin\Metabox::get_instance()->activate();
//
///**
// * Provides settings to user.
// */
///** @noinspection PhpIncludeInspection */
//include( RTR_ADMIN_DIRECTORY . 'Settings.php' );
//restrictr\admin\Settings::get_instance()->activate();
//
//// ------------------------------------------------------------------
//// Include functionality
//// ------------------------------------------------------------------
//
///**
// * Path to functionality directory.
// */
//define( 'RTR_FUNCTIONALITY_DIRECTORY', RTR_PLUGIN_DIRECTORY . 'functionality/' );
//
///**
// * Provides redirection functionality.
// */
///** @noinspection PhpIncludeInspection */
//include( RTR_FUNCTIONALITY_DIRECTORY . 'Redirection.php' );
//
///**
// * Provides hiding functionality in menus.
// */
///** @noinspection PhpIncludeInspection */
//include( RTR_FUNCTIONALITY_DIRECTORY . 'Hiding.php' );
//
///**
// * Provides filtering to control other functionality.
// */
///** @noinspection PhpIncludeInspection */
//include( RTR_FUNCTIONALITY_DIRECTORY . 'Filtering.php' );
//\restrictr\functionality\Filtering::get_instance()->activate();