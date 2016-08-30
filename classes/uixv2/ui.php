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
     * Array of definitions locations
     *
     * @since 1.0.0
     *
     * @var   array
     */
    protected $locations = array();

	/**
	 * UI constructor
	 *
	 * @since 2.0.0
	 * @param array $locations array of loader locations and callbacks
	 */
	public function __construct() {

		/**
		 * do UI loader locations
		 *
		 * @param uixv2/ui $current UI object
		 */
		do_action( 'uixv2_register', $this );

		// go over each locations
		foreach( $this->locations as $location ){
			$uid = @ opendir( $location );
			if ( $uid ) {
				while( ( $folder = readdir( $uid ) ) !== false ) {
					if ( substr( $folder, 0, 1) == '.' )
						continue;

					if ( is_dir( $location . '/' . $folder ) ) {
						$fid = @ opendir( $location . '/' . $folder );
						if ( $fid ) {
							$init = array( '\uixv2\ui\\' . $folder, 'register' );
							if( !is_callable( $init ) ){
								continue;
							}
							$structures = array();
							while( ( $file = readdir( $fid ) ) !== false ) {
								if( is_file( $location . '/' . $folder . '/' . $file ) ){
									$is_struct = include $location . '/' . $folder . '/' . $file;
									if( is_array( $is_struct ) ){
										$structures = array_merge( $structures, $is_struct );
									}
								}
							}
							if( !empty( $structures ) ){
								call_user_func_array( $init, array( $structures ) );
							}
						}
					}
				}
				@closedir( $uid );
			}
		}
	}


    /**
     * Register the UIX objects
     *
     * @since 1.0.0
     *
     * @param array|string $args path or array of paths to structures
     */
    public function register( $arr ) {
    	// convert if string
    	if( is_string( $arr ) ){
    		$arr = array( $arr );
    	}
    	// determin how the structure works.
    	foreach( $arr as $key => $value ){
			if( is_dir( $value ) && !in_array( $value, $this->locations ) ){
				$this->locations[] = $value;
			}
    	}
    }

}