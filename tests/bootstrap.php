<?php
/**
 * Bootstrap the plugin unit testing environment.
 *
 * Support for:
 *
 * 1. `WP_DEVELOP_DIR` and `WP_TESTS_DIR` environment variables
 * 2. Plugin installed inside of WordPress.org developer checkout
 * 3. Tests checked out to /tmp
 *
 * @package UIX
 */


$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['SERVER_NAME']     = '';
$PHP_SELF                   = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../uix-plugin.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

function uix_load_test_structures( $uix ) {
	$uix->register( dirname( __FILE__ ) . '/ui' );
	$uix->register( dirname( __FILE__ ) . '/bad' );
}

tests_add_filter( 'uix_register', 'uix_load_test_structures' );

require $_tests_dir . '/includes/bootstrap.php';

echo "Installing UIX...\n";
