<?php
/**
 * UIX WordPress Plugin
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @wordpress-plugin
 * Plugin Name: UIX
 * Plugin URI:  https://github.com/DavidCramer/uix
 * Description: React-based UI framework for WordPress plugins.
 * Version:     4.0.0
 * Requires PHP: 8.1
 * Author:      David Cramer
 * Author URI:  http://cramer.co.za
 * Text Domain: uix
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

declare(strict_types=1);

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'UIX_PLUGIN_FILE', __FILE__ );
define( 'UIX_PATH', plugin_dir_path( __FILE__ ) );
define( 'UIX_URL', plugin_dir_url( __FILE__ ) );
define( 'UIX_VERSION', '4.0.0' );

// Autoloader (Composer or bundled fallback).
if ( file_exists( UIX_PATH . 'vendor/autoload.php' ) ) {
    require_once UIX_PATH . 'vendor/autoload.php';
}

// Bootstrap via uix-bootstrap.php (included by Composer files[] autoload too).
require_once UIX_PATH . 'uix-bootstrap.php';
