<?php
/**
 * UIXV2 UI Loader
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2;

/**
 * UI class
 * @package uixv2
 * @author  David Cramer
 */
class ui{

	/**
	 * UI constructor
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct() {

		// Setup UI locations
		$locations = array(
			'pages' => array(
				'callback'	=> array( '\uixv2\ui\pages', 'register' ),
				'dir'		=> UIXV2_PATH . 'includes/ui/pages'
			),
			'posttypes' => array(
				'callback'	=> array( '\uixv2\ui\posts', 'register' ),
				'dir'		=> UIXV2_PATH . 'includes/ui/post-types'
			),
			'metaboxes' => array(
				'callback'	=> array( '\uixv2\ui\metaboxes', 'register' ),
				'dir'		=> UIXV2_PATH . 'includes/ui/metaboxes'
			),
			'shortcodes' => array(
				'callback'	=> array( '\uixv2\ui\shortcodes', 'register' ),
				'dir'		=> UIXV2_PATH . 'includes/ui/shortcodes'
			),

		);

		/**
		 * Filter UI loader locations
		 *
		 * @param array $locations array of core UIX object structures to be registered
		 */
		$locations = apply_filters( 'uixv2_ui_locations', $locations );

		// go over each locations
		foreach( $locations as $location ){
			if( empty( $location['dir'] ) || empty( $location['callback'] ) ){ continue; }
			$ui_dir = $location['dir'];
			$uid = @ opendir( $ui_dir );
			$structures = array();
			if ( $uid ) {
				while (($file = readdir( $uid ) ) !== false ) {
					if ( substr($file, 0, 1) == '.' )
						continue;
					if ( is_file( $ui_dir . '/' . $file ) ) {
						$is_struct = include $ui_dir . '/' . $file;
						if( is_array( $is_struct ) ){
							$structures = array_merge( $structures, $is_struct );
						}
					}
				}
				@closedir( $uid );
			}

			if( !empty( $structures ) ){
				call_user_func_array( $location['callback'], array( $structures ) );
			}

		}		

	}

}