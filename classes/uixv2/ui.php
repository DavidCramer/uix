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
     * Array of object instances
     *
     * @since 2.0.0
     * @access public
     * @var   object/uix
     */
    public $ui;

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
            $objects = call_user_func( $init, $structures );
            foreach( $objects as $slug => $object ){
                $this->ui->{$type}[ $slug ] = $object;
            }
            return $objects;
        }
    }

    /**
     * Add a single structure object
     *
     * @since 2.0.0
     * @param string $type The type of object to add
     * @param string $slug The objects slug to add
     * @param string $structure The objects structure
     * @return    object|\uixv2\    the instance of the object type
     */
    public function add( $type, $slug, $structure ) {
        $this->load( $type, array( $slug => $structure ) );
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
     * Handly method to get request vars
     *
     * @since 2.0.0
     *
     * @param string $type Request type to get
     * @return array Regest vars array
     */
    public function request_vars( $type ) {
        switch ( $type ) {
            case 'post':
                return $_POST;
                break;
            case 'get':
                return $_POST;
                break;
            case 'files':
                return $_POST;
                break;
            default:
                return $_REQUEST;
                break;
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