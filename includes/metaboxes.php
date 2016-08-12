<?php
/**
 * Plugin metabox Structures
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

$plugin_path =  plugin_dir_path( dirname( __FILE__ ) );

$metabox = array(
	'uix_metabox'   => array(
		'post_type'			=> 	array( 'post' ), // array of post types this should be in
		'name'				=>	'UIX Metabox Example', // the label/name of the metabox 
		'context'			=>	'normal', // metabox type ( normal , advanced, side )
		'priority'			=>	'core', // priority of the box in editor		
		'base_color'  		=>	'#c0392b',
		'base_text_color'	=>  '#fff',
		'chromeless'		=>	true,
		'modals'	  =>  array(
			//modal templates to be included as an array. slug => file
			'project'	=> $plugin_path . 'includes/templates/project.php',
		),
		'template'	 		=>	$plugin_path . 'includes/templates/modals.php',
	),
);



return $metabox;