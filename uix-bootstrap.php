<?php
/**
 * UIX Bootstrapper/ WordPress Plugin
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

/**
 * UIX Object class autoloader.
 * It locates and finds class via classes folder structure.
 *
 * @since 1.0.0
 *
 * @param string $class     class name to be checked and autoloaded
 */
function uix_autoload_class( $class ){
    $parts = explode( '\\', $class );
    $name = array_shift( $parts );
    if( file_exists( UIX_PATH . 'classes/' . $name ) ){        
        if( !empty( $parts ) ){
            $name .= '/' . implode( '/', $parts );
        }
        $class_file = UIX_PATH . 'classes/' . $name . '.php';
        if( file_exists( $class_file ) ){
            include_once $class_file;
        }
    }
}
spl_autoload_register( 'uix_autoload_class', true, false );

// bootstrap plugin load
add_action( 'plugins_loaded', 'uix' );
/**
 * UIX Helper to minipulate the overall UI instance.
 *
 * @since 1.0.0
 * @access public
 */
function uix(){
    // init UI
    return \uix\ui::get_instance();
}