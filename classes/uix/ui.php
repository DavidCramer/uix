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
 * UI loader and handler class. This forms a single instance with UI objects attached
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
     * Array of post and get data
     *
     * @since 1.0.0
     * @access private
     * @var   array
     */
    protected $data = array();

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
     * @access public
     */
    public function auto_load() {
        /**
         * do UI loader locations
         *
         * @param ui $this Current instance of this class
         */
        do_action( 'uix_register', $this );

        // go over each locations
        foreach( $this->locations as $type => $paths ){

            if( $this->is_callable( $type ) )
                $this->process_paths( $type, $paths );

        }
    
    }

    /**
     * Add a single structure object
     *
     * @since 1.0.0
     * @param string $type The type of object to add
     * @param array $paths array of paths to process and add
     */
    private function process_paths( $type, $paths ){

        foreach( $paths as $path ) {
            $has_struct = $this->get_file_structure( $path );
            if( is_array( $has_struct ) )
                $this->add_objects( $type, $has_struct );

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
     * @return object The instance of the object type or null if invalid
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
     * @param string $type The type of object to add
     * @param array $objects The objects structure
     * @param object $parent object
     */
    public function add_objects( $type, array $objects, $parent = null ) {
        foreach( $objects as $slug => $struct ){
            if( is_array( $struct ) )
                $this->add( $type, $slug, $struct, $parent);
        }
    }



    /**
     * Return an instance of this class.
     * @codeCoverageIgnore
     * @since 1.0.0
     * @param array $request_data Current REQUEST superglobals
     * @return ui A single instance of this class
     */
    public static function get_instance( $request_data ) {

        // If the single instance hasn't been set, set it now.
        if ( isset( self::$instance ) ) {
            self::$instance->data = $request_data;            
        }else{
            self::$instance = new self;
            self::$instance->data = $request_data;
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
            $this->locations = array_merge_recursive( $this->locations, $this->get_files_from_folders( trailingslashit( $value ) ) );

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
        return $this->data[ $type ];
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
     * Opens a location and gets the folders to check
     *
     * @since 1.0.0
     * @access private
     * @param string $path  The file patch to examine and to fetch contents from
     * @return array List of folders
     */
    private function get_folder_contents( $path ) {

        $items = array();
        if( $uid = opendir( $path ) ) {
            while (($item = readdir($uid)) !== false) {
                if ( substr($item, 0, 1) != '.' )
                    $items[ $item ] = $path . $item;

            }
            closedir( $uid );
        }

        return $items;
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
    private function get_files_from_folders( $path ) {

        $items = $this::get_folder_contents( $path );

        foreach ( $items as $type => &$location ){
            $location = $this::get_folder_contents( trailingslashit( $location ) );
            sort($location);
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
     */
    public function silent_warning( $errno, $errstr, $errfile, $errline ) {
        $this->add( 'notice', 'notice_' . $errno . '-' . $errline, array(
            'description' => '<strong>' . __( 'Warning' ) . '</strong>: ' . $errstr . '<br>on ' . $errfile .' line ' . $errline,
            'state'       => 'warning'
        )  );
    }


    /**
     * Sets assets to be enqueued for this instance.
     *
     * @param array $assets the asset to enqueue where the key is the type and the value the asset
     */
    public function set_assets( $assets ){

        foreach ( $assets as $type => $asset )
            $this->enqueue( $asset, $type );

    }

    /**
     * enqueue a set of styles and scripts
     *
     * @since 1.0.0
     * @access protected
     * @param array $set Array of assets to be enqueued
     * @param string $type The type of asset
     */
    protected function enqueue( $set, $type ){
        // go over the set to see if it has styles or scripts

        $enqueue_type = 'wp_enqueue_' . $type;

        foreach( $set as $key => $item ){

            if( is_int( $key ) ){
                $enqueue_type( $item );
                continue;
            }

            $args = $this->build_asset_args( $item );
            $enqueue_type( $key, $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );

        }

    }


    /**
     * Checks the asset type
     *
     * @since 1.0.0
     * @access private
     * @param array|string $asset Asset structure, slug or path to build
     * @return array Params for enqueuing the asset
     */
    private function build_asset_args( $asset ){

        // setup default args for array type includes
        $args = array(
            "src"       => false,
            "deps"      => array(),
            "ver"       => false,
            "in_footer" => false,
            "media"     => false
        );

        if( is_array( $asset ) ){
            $args = array_merge( $args, $asset );
        }else{
            $args['src'] = $asset;
        }

        return $args;
    }


}