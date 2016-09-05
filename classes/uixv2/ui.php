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
     * @var   array
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
     */
    private function auto_load() {

        /**
         * do UI loader locations
         *
         * @param uixv2/ui $current UI instance
         */
        do_action( 'uixv2_register', $this );

        // go over each locations
        foreach( $this->locations as $type => $paths ){

            foreach( $paths as $path ) {
                $has_struct = $this->get_file_structure( $path );
                if( is_array( $has_struct ) ){
                    foreach( $has_struct as $slug => $struct ){
                        if( is_array( $struct ) )
                            $this->add( $type, $slug, $struct );
                    }
                }
            }

        }
    }

    /**
     * Add a single structure object
     *
     * @since 2.0.0
     * @param string $type The type of object to add
     * @param string $slug The objects slug to add
     * @param string $structure The objects structure
     * @param object $parent object
     * @return  object|\uixv2\/null    the instance of the object type or null if invalid
     */
    public function add( $type, $slug, $structure, $parent = null ) {
        $init = $this->get_register_callback( $type );
        if( null !== $init ){
            $object = call_user_func_array( $init, array( $slug, $structure, $parent ) );
            $this->ui->{$type}[ $slug ] = $object;
            return $object;
        }
        return null;
    }


    /**
     * Returns a callback for registering the object or null if invalid type
     *
     * @since 2.0.0
     * @param string $type The type of object to get register callback for
     * @return array|null Callback array for registering an object or null if invalid
     */
    public function get_register_callback( $type ) {
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
     * @return    object|UI    A single instance
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
     * @param array|string $arr path, or array of paths to structures to autoload
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
     * Handy method to get request vars
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