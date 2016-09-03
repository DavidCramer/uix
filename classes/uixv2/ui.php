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
     * @access private
     * @param array $locations array of loader locations and callbacks
     */
    private function auto_load() {

        /**
         * do UI loader locations
         *
         * @param uixv2/ui $current UI object
         */
        do_action( 'uixv2_register', $this );

        // go over each locations
        foreach( $this->locations as $type => $paths ){

            $structures = array();
            foreach( $paths as $path ) {
                $has_struct = $this->get_file_structure( $path );
                if( is_array( $has_struct ) )
                    $structures = array_merge( $structures, $has_struct );
            }
            if( !empty( $structures ) )
                $this->load( $type, $structures );
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
     * Register the UIX object paths for autoloading
     *
     * @since 2.0.0
     *
     * @param array|string $arr path, or array of paths to structures, or UI type
     */
    public function register( $arr ) {
        // determin how the structure works.
        foreach( (array) $arr as $key => $value ){
            if( is_dir( $value ) && !in_array( $value, $this->locations ) ){
                $this->locations = array_merge( $this->locations, $this->get_files_from_folders( $value ) );
            }
        }
    }


    /**
     * Gets the file structures and converts it if needed
     *
     * @since 2.0.0
     * @access private
     * @param string $path The file path to load
     * @return array|bool object structure array or false if invalid
     */
    private function get_file_structure( $path ){
        ob_start();
        $content = include $path;
        $has_output = ob_get_clean();
        // did stuff output
        if( !empty( $has_output ) )
            $content = json_decode( $has_output, ARRAY_A );

        return $content;
    }


    /**
     * Opens a location and gets the file to load for each folder
     *
     * @since 2.0.0
     * @access private
     * @param array $paths to fetch contents of
     * @param bool $file flag to set file fetching
     * @return array List of folders and files
     */
    private function get_files_from_folders( $path, $file = false ) {
        $items = array();
        $uid = @ opendir( $path );
        if ( $uid ) {
            while( ( $item = readdir( $uid ) ) !== false ) {
                if ( substr( $item, 0, 1) == '.' )
                    continue;
                if( false === $file ){
                    $items[ $item ] = $this->get_files_from_folders( $path . $item, true );
                }else{
                    $items[] = $path . '/' . $item;
                }
            }
            @closedir( $uid );
        }

        return $items;
    }
}