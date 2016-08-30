<?php
/**
 * UIXV2 Bootstrapper
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
if( ! defined( 'UIXV2_CORE' ) ){
    define('UIXV2_PATH',  plugin_dir_path( __FILE__ ) );
    define('UIXV2_CORE',  __FILE__ );
    define('UIXV2_URL',  plugin_dir_url( __FILE__ ) );
    define('UIXV2_VER',  '2.0.0' );
}
// bootstrap plugin load
add_action( 'plugins_loaded', 'uixv2_plugin_bootstrap' );
function uixv2_plugin_bootstrap(){
    // init UI
    new \uixv2\ui();
}