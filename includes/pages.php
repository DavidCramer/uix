<?php
/**
 * UIX Admin Page Structures
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer
 */


return array(
	'uix_base'	=>	array(
		'page_title'	=>	__( 'UIX Admin', 'uix' ),
		'menu_title'	=>	__( 'UIX Admin', 'uix' ),
		//'parent'		=>	'caldera-forms',
		'capability'	=>	'manage_options',
		'icon'			=>	'dashicons-welcome-widgets-menus',
		'position'		=>	3.1,
		'styles'		=>	array(
			'uix-base-styles' => UIX_URL . 'assets/css/admin.css'
		),
		'scripts'		=>	array(),
		'tabs'			=>	array(
			'main'		=>	array(
				'page_title'	=> 	__('General Settings and Options', 'uix'),
				'page_description'	=> 	__('Condifure general setup requirens', 'uix'),
				'menu_title'	=> 	__('General', 'uix'),
				//'default'		=>	true,
				'template'		=>	UIX_PATH . 'templates/general-ui.php',
				'partials'		=>	array(
					'user-row' => UIX_PATH . 'templates/user-row-ui.php'
				)
			),
			'news'		=>	array(
				'page_title'	=> 	__('News and Stuff', 'uix'),
				'menu_title'	=> 	__('News', 'uix'),
				'default'		=>	true,
				'template'		=>	UIX_PATH . 'templates/other-ui.php'
			),
			'options'		=>	array(
				'page_title'	=> 	__('options and things', 'uix'),
				'menu_title'	=> 	__('options', 'uix'),
				'default'		=>	true,
				'template'		=>	UIX_PATH . 'templates/other-ui.php'
			),
			'people'		=>	array(
				'page_title'	=> 	__('People', 'uix'),
				'menu_title'	=> 	__('People and pets', 'uix'),
				'default'		=>	true,
				'template'		=>	UIX_PATH . 'templates/other-ui.php'
			)			
		)
	),
	'news_pages'	=>	array(
		'page_title'	=>	__( 'News System', 'uix' ),
		'menu_title'	=>	__( 'News System', 'uix' ),
		//'parent'		=>	'caldera-forms',
		'capability'	=>	'manage_options',
		'icon'			=>	'dashicons-welcome-widgets-menus',
		'position'		=>	36,
		'styles'		=>	array(
			'uix-base-styles' => UIX_URL . 'assets/css/admin.css'
		),
		'scripts'		=>	array(),
		'tabs'			=>	array(
			'news'		=>	array(
				'page_title'	=> 	__('News and Stuff', 'uix'),
				'menu_title'	=> 	__('News', 'uix'),
				'default'		=>	true,
				'template'		=>	UIX_PATH . 'templates/other-ui.php'
			)		
		)
	)
);