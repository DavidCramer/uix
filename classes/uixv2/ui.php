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
	 * @since 2.0.0
	 * @param array $locations array of loader locations and callbacks
	 */
	public function __construct( array $locations = array() ) {

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