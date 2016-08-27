<?php
/**
 * @package   uixv2
 * @author    %author%
 * @license   GPL-2.0+
 * @link      
 * @copyright 2016 %author%
 *
 * @wordpress-plugin
 * Plugin Name: %name%
 * Plugin URI:  %url%
 * Description: %description%
 * Version:     %version%
 * Author:      %author%
 * Author URI:  %url%
 * Text Domain: text-domain
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

/**
 * locate and find class via classes folder tructure.
 *
 * @since 2.0.0
 *
 * @param string $class     class name to be checked and autoloaded
 */
function uixv2_autoload_class( $class ){
    $parts = explode( '\\', $class );
    $name = array_shift( $parts );
    if( file_exists( UIXV2_PATH . 'classes/' . $name ) ){        
        if( !empty( $parts ) ){
            $name .= '/' . implode( '/', $parts );
        }
        $class_file = UIXV2_PATH . 'classes/' . $name . '.php';
        if( file_exists( $class_file ) ){
            include_once $class_file;
        }
    }
}
spl_autoload_register( 'uixv2_autoload_class', true, false );

// bootstrap plugin load
add_action( 'plugins_loaded', 'uixv2_plugin_bootstrap', 200 );
function uixv2_plugin_bootstrap(){

    // init UI
    new \uixv2\ui();
    // init plugin logic
    new \uixv2\core();

}