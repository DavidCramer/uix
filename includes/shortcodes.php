<?php
/**
 * Plugin shortcode Structures
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

$plugin_path =  plugin_dir_path( dirname( __FILE__ ) );

$shortcode = array(
	'front'   => array(
		'template'	 		=>	$plugin_path . 'includes/templates/shortcode.php',
	),
);



return $shortcode;