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

define('{{prefix}}_PATH',  plugin_dir_path( __FILE__ ) );
define('{{prefix}}_CORE',  __FILE__ );
define('{{prefix}}_URL',  plugin_dir_url( __FILE__ ) );
define('{{prefix}}_VER',  '{{version}}' );

// Load instance
add_action( 'plugins_loaded', function(){
	
	// include the library
	include_once {{prefix}}_PATH . 'uix/uix.php';

	// get the pages
	$pages = include {{prefix}}_PATH . 'includes/pages.php';

	// initialize admin UI
	$uix = \{{namespace}}\ui\uix::get_instance( '{{namespace}}' );
	$uix->register_pages( $pages );

	
} );
