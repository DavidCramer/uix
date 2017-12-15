<?php
/**
 * UIX Bootstrapper
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 David Cramer
 */

// If this file is called directly, abort.
if ( defined( 'WPINC' ) ) {


	if ( ! defined( 'UIX_ASSET_DEBUG' ) ) {
		if ( ! defined( 'DEBUG_SCRIPTS' ) ) {
			define( 'UIX_ASSET_DEBUG', '.min' );
		} else {
			define( 'UIX_ASSET_DEBUG', '' );
		}
	}


	// include uix helper functions and autoloader.
	require_once UIX_PATH . 'includes/functions.php';

	// register uix autoloader.
	spl_autoload_register( 'uix_autoload_class', true, false );

	// bootstrap plugin load.
	add_action( 'plugins_loaded', 'uix' );

}
