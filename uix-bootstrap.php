<?php
/**
 * UIX Bootstrapper / WordPress Plugin
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 *
 * @wordpress-plugin
 * Plugin Name: UIX
 * Plugin URI:  http://cramer.co.za
 * Description: UI Framework for WordPress Plugins.
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
if( ! defined( 'UIX_CORE' ) ){
    define('UIX_CORE',  __FILE__ );
    define('UIX_PATH',  plugin_dir_path( __FILE__ ) );
    define('UIX_URL',  plugin_dir_url( __FILE__ ) );
    define('UIX_VER',  '1.0.0' );
}

// include uix helper functions and autoloader.
require_once( UIX_PATH . 'includes/functions.php' );

// register uix autoloader
spl_autoload_register( 'uix_autoload_class', true, false );

// bootstrap plugin load
add_action( 'plugins_loaded', 'uix' );
