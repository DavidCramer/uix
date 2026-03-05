<?php
/**
 * UIX Bootstrap
 *
 * Kicks off the UIX framework on plugins_loaded.
 * This file is included via both uix-plugin.php and Composer's files[] autoload.
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 */

declare(strict_types=1);

// Guard against direct calls and double-includes.
if ( ! defined( 'WPINC' ) || defined( 'UIX_BOOTSTRAPPED' ) ) {
    return;
}

define( 'UIX_BOOTSTRAPPED', true );

add_action( 'plugins_loaded', static function (): void {
    \UIX\UIX::boot();
}, 5 );
