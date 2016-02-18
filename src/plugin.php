<?php
/**
 * @package   {{namespace}}
 * @author    {{author}}
 * @license   GPL-2.0+
 * @link      
 * @copyright 2016 {{author}}
 *
 * @wordpress-plugin
 * Plugin Name: {{name}}
 * Plugin URI:  {{url}}
 * Description: {{description}}
 * Version:     {{version}}
 * Author:      {{author}}
 * Author URI:  {{url}}
 * Text Domain: {{namespace}}
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('{{slug}}_PATH',  plugin_dir_path( __FILE__ ) );
define('{{slug}}_CORE',  __FILE__ );
define('{{slug}}_URL',  plugin_dir_url( __FILE__ ) );
define('{{slug}}_VER',  '{{version}}' );

// Load instance
add_action( 'plugins_loaded', function(){
	// include the library
	include_once {{slug}}_PATH . 'classes/uix.php';
	// front
	if( !is_admin() ){
		// front class
		include_once {{slug}}_PATH . 'classes/front.php';
	}
	
	// get the pages
	$pages = include {{slug}}_PATH . 'includes/pages.php';

	// initialize admin UI
	$uix = \{{namespace}}\ui\uix::get_instance( $pages, '{{namespace}}' );
	$uix->register_pages( $pages );

	//$metaboxes = include {{namespace}}_PATH . 'includes/metaboxes.php';	
	//$uix->register_metaboxes( $metaboxes );	
	
} );
