<?php
/**
 * UIX WordPress Plugin
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
if ( defined( 'WPINC' ) ) {

	// include uix bootstrap.
	require_once( UIX_PATH . 'uix-bootstrap.php' );

}
