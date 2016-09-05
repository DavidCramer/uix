<?php
/**
 * UIX2 Bootstrapper
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
if( ! defined( 'UIX2_CORE' ) ){
    define('UIX2_CORE',  __FILE__ );
    define('UIX2_PATH',  plugin_dir_path( __FILE__ ) );
    define('UIX2_URL',  plugin_dir_url( __FILE__ ) );
    define('UIX2_VER',  '2.0.0' );
}

/**
 * locate and find class via classes folder tructure.
 *
 * @since 2.0.0
 *
 * @param string $class     class name to be checked and autoloaded
 */
function uix2_autoload_class( $class ){
    $parts = explode( '\\', $class );
    $name = array_shift( $parts );
    if( file_exists( UIX2_PATH . 'classes/' . $name ) ){        
        if( !empty( $parts ) ){
            $name .= '/' . implode( '/', $parts );
        }
        $class_file = UIX2_PATH . 'classes/' . $name . '.php';
        if( file_exists( $class_file ) ){
            include_once $class_file;
        }
    }
}
spl_autoload_register( 'uix2_autoload_class', true, false );

// bootstrap plugin load
add_action( 'plugins_loaded', 'uix2' );
function uix2(){
    // init UI
    return \uix2\ui::get_instance();
}