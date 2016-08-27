<?php
/**
 * @package   uixv2
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
 * Text Domain: uixv2
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('UIXV2_PATH',  plugin_dir_path( __FILE__ ) );
define('UIXV2_CORE',  __FILE__ );
define('UIXV2_URL',  plugin_dir_url( __FILE__ ) );
define('UIXV2_VER',  '1.0.0' );

// Load autoloader
include_once UIXV2_PATH . 'includes/autoloader.php';

add_action( 'plugins_loaded', 'uixv2_plugin_bootstrap', 200 );
function uixv2_plugin_bootstrap(){

	// start DB Post-Types UI
	new \uixv2\ui();
	new \uixv2\core();

	
}