<?php
/**
 * UIX UI Loader
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix;

/**
 * UI loader and handler class. This forms a single instance with UI objects attached.
 * 
 * @package uix
 * @author  David Cramer
 */
class ui{


    /**
     * Array of definitions locations
     *
     * @since 1.0.0
     * @access protected
     * @var   array
     */
    protected $locations = array();

    /**
     * Array of object instances
     *
     * @since 1.0.0
     * @access public
     * @var   array
     */
    public $ui;

    /**
     * Holds instance
     *
     * @since 1.0.0
     * @access protected
     * @var      object/UI
     */
    protected static $instance = null;

    /**
     * UI structure auto load
     *
     * @since 1.0.0
     * @access private
     */
    private function auto_load() {

        /**
         * do UI loader locations
         *
         * @param ui current instance of this class
         */
        do_action( 'uix_register', $this );

        // go over each locations
        foreach( $this->locations as $type => $paths ){
            
            if( !$this->is_callable( $type ) )
                continue;
            
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
     * @since 1.0.0
     * @param string $type The type of object to add
     * @param string $slug The objects slug to add
     * @param array $structure The objects structure
     * @param object $parent object
     * @return uix|null The instance of the object type or null if invalid
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
     * @since 1.0.0
     * @param string $type The type of object to get register callback for
     * @return array|null Callback array for registering an object or null if invalid
     */
    public function get_register_callback( $type ) {
        $init = array( '\uix\ui\\' . $type, 'register' );
        if( !is_callable( $init ) ){
            return null;
        }
        return $init;
    }

    /**
     * Checks if the object type is callable
     *
     * @since 1.0.0
     * @param string $type The type of object to check
     * @return bool 
     */
    public function is_callable( $type ) {
        $init = array( '\uix\ui\\' . $type, 'register' );
        return is_callable( $init );
    }

    /**
     * Registers multiple objects
     *
     * @since 1.0.0
     * @param string $type The type of object to check
     * @return bool 
     */
    public function add_objects( $type, array $objects, uix $parent = null ) {

        $init = array( '\uix\ui\\' . $type, 'register' );
        return is_callable( $init );
    }



    /**
     * Return an instance of this class.
     *
     * @since 1.0.0
     *
     * @return ui A single instance of this class
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
     * @since 1.0.0
     *
     * @param array|string $arr path, or array of paths to structures to autoload
     */
    public function register( $arr ) {
        // set error handler for catching file location errors
        set_error_handler( array( $this, 'silent_warning' ), E_WARNING );
        // determin how the structure works.
        foreach( (array) $arr as $key => $value )
            $this->locations = array_merge( $this->locations, $this->get_files_from_folders( trailingslashit( $value ) ) );
        // restore original handler
        restore_error_handler();        
    }

    /**
     * Handy method to get request vars
     *
     * @since 1.0.0
     *
     * @param string $type Request type to get
     * @return array Request vars array
     */
    public function request_vars( $type ) {
        switch ( $type ) {
            case 'post':
                return $_POST;                
            case 'get':
                return $_GET;                
            case 'files':
                return $_POST;                
            default:
                return $_REQUEST;                
        }
    }    


    /**
     * Gets the file structures and converts it if needed
     *
     * @since 1.0.0
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
     * @since 1.0.0
     * @access private
     * @param string $path  The file patch to examine and to fetch contents from
     * @param bool $file flag to set file fetching vs folder load
     * @return array List of folders and files
     */
    private function get_files_from_folders( $path, $file = false ) {
        $items = array();
        $uid = opendir( $path );
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
            closedir( $uid );
        }

        return $items;
    }

    /**
     * Handles E_WARNING error notices whan the file loader runs.
     *
     *
     * @since 1.0.0
     *
     * @link http://php.net/manual/en/function.set-error-handler.php
     * @param int $errno Contains the level of the error raised, as an integer. 
     * @param string $errstr Contains the error message.
     * @param string $errfile Which contains the filename that the error was raised in.
     * @param int $errline which contains the line number the error was raised at.
     * @return bool|null If built in handler is used then null is returned.
     */
    public function silent_warning( $errno, $errstr, $errfile, $errline ) {
        $this->add( 'notice', 'notice_' . $errno . '-' . $errline, array(
            'description' => '<strong>' . __( 'Warning' ) . '</strong>: ' . $errstr . '<br>on ' . $errfile .' line ' . $errline,
            'state'       => 'warning'
        )  );
    }

}