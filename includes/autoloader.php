<?php
/**
 * UIX Autoloader
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

function uix_autoload_class( $class ){
	if( false !== strpos( $class, 'uixv2\\' ) ){
		$parts = explode( '\\', substr( $class, 6 ) );
		$joined_path = implode( '/', $parts );		
		if( file_exists( UIX_PATH . 'classes/' . $joined_path . '.php' ) ){
			include_once UIX_PATH . 'classes/' . $joined_path . '.php';
		}
	}
}
spl_autoload_register( 'uix_autoload_class', true, false );