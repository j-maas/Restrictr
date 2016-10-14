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
include( 'src/Psr4AutoloaderClass.php' );

$loader = new \restrictr\Psr4AutoloaderClass();
$loader->addNamespace('restrictr', dirname(__FILE__) . '/src');
$loader->register();

\restrictr\admin\Metabox::get_instance()->activate();
\restrictr\admin\Settings::get_instance()->activate();
\restrictr\functionality\Filtering::get_instance()->activate();