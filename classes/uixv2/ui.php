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
     * @since 2.0.0
     * @access protected
     * @var   array
     */
    protected $locations = array();

    /**
     * Holds instance
     *
     * @since 2.0.0
     * @access protected
     * @var      object/UI
     */
    protected static $instance = null;

	/**
	 * UI structure auto load
	 *
	 * @since 2.0.0
     *
	 * @param array $locations array of loader locations and callbacks
	 */
	public function auto_load() {

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
                            $init = $this->get_register_function( $folder );
                            if( null === $init ){ continue; }

                            $structures = array();
                            while( ( $file = readdir( $fid ) ) !== false ) {
                                if( is_file( $location . '/' . $folder . '/' . $file ) ){
                                    if( false !== strpos( $file, '.json' ) ){
                                        $json = file_get_contents( $location . '/' . $folder . '/' . $file );
                                        $is_struct = json_decode( $json, ARRAY_A );
                                    }else{
                                        $is_struct = include $location . '/' . $folder . '/' . $file;
                                    }
                                    if( is_array( $is_struct ) ){
                                        $structures = array_merge( $structures, $is_struct );
                                    }
                                }
                            }
                            if( !empty( $structures ) ){
                                $this->register( $folder, $structures );
                            }
                        }
					}
				}
				@closedir( $uid );
			}
		}
	}

    /**
     * loads a structure object
     *
     * @since 2.0.0
     *
     * @return    object|\uixv2\    A single instance
     */
    public function load( $type, $structures ) {
        $init = $this->get_register_function( $type );
        if( null !== $init ){
            $path = explode('\\', $type );
            $type = array_shift( $path );
            $object = call_user_func( $init, $structures );

            if( !empty( $path[0] ) ){
                $this->{$type}[$path[0]] = $object;
            }else{
                $this->{$type} = $object;
            }
        }
    }

    /**
     * Return an instance of this class.
     *
     * @since 2.0.0
     *
     * @return    object|\uixv2\    A single instance
     */
    public function get_register_function( $type ) {
        $init = array( '\uixv2\ui\\' . $type, 'register' );
        if( !is_callable( $init ) ){
            return null;
        }
        return $init;
    }


    /**
     * Return an instance of this class.
     *
     * @since 2.0.0
     *
     * @return    object|\uixv2\    A single instance
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
            self::$instance->auto_load();
        }

        return self::$instance;

    }

    /**
     * Register the UIX objects
     *
     * @since 2.0.0
     *
     * @param array|string $arr path, or array of paths to structures, or UI type
     * @param array $struct array of UI structure if $arr is a UI type string
     */
    public function register( $arr, array $struct = array() ) {
        // convert if string
        if( is_string( $arr ) ){
            if( !empty( $struct ) ){
                $this->load( $arr, $struct );
                return;
            }else{
              $arr = array( $arr );
            }
        }
        // determin how the structure works.
        foreach( $arr as $key => $value ){
            if( is_dir( $value ) && !in_array( $value, $this->locations ) ){
                $this->locations[] = $value;
            }
        }
    }

}