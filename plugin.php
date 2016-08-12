<?php
/**
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      
 * @copyright 2016 David Cramer
 *
 * @wordpress-plugin
 * Plugin Name: UIX
 * Plugin URI:  http://cramer.co.za
 * Description: Plugin Framework for WordPress
 * Version:     1.0.0
 * Author:      David Cramer
 * Author URI:  http://cramer.co.za
 * Text Domain: uix
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('UIX_PATH',  plugin_dir_path( __FILE__ ) );
define('UIX_CORE',  __FILE__ );
define('UIX_URL',  plugin_dir_url( __FILE__ ) );
define('UIX_VER',  '1.0.0' );

// Load instance
include_once UIX_PATH . 'includes/autoloader.php';

add_action( 'plugins_loaded', 'uix_bootstrap' );
function uix_bootstrap(){

	// get pages
	$page_structures = include UIX_PATH . 'includes/pages.php';
	// initialize admin pages
	$pages = \uixv2\pages::get_instance();
	// register pages
	$pages->register( $page_structures );

	// get metaboxes
	$meta_structures = include UIX_PATH . 'includes/metaboxes.php';
	$metaboxes = \uixv2\metaboxes::get_instance();
	// register metaboxes
	$metaboxes->register( $meta_structures );

	$shortcode_structures = include UIX_PATH . 'includes/shortcodes.php';
	$shortcodes = \uixv2\shortcodes::get_instance();
	// register metaboxes
	$shortcodes->register( $shortcode_structures );

}