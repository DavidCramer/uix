<?php
/**
 * UIX Core
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * Core UIX abstract class.
 * 
 * @package uix\ui
 * @author  David Cramer
 */
abstract class uix{

    /**
     * Config Structure of object
     *
     * @since 1.0.0
     * @access public
     * @var      array
     */
    public $struct = array();


    /**
     * The type of UI object
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $type = 'uix';

    /**
     * object slug
     * @access public
     * @since 1.0.0
     *
     * @var      string
     */
    public $slug;
    
    /**
     * array of child objects
     *
     * @since 1.0.0
     * @access public
     * @var      array
     */
    public $child = array();

    /**
     * Objects parent
     *
     * @since 1.0.0
     * @access public
     * @var      object/uix
     */
    public $parent;    

    /**
     * Base URL of this class
     *
     * @since 1.0.0
     * @access protected
     * @var      string
     */
    protected $url;

    /**
     * List of core object scripts ( common scripts )
     *
     * @since 1.0.0
     * @access protected
     * @var      array
     */
    protected $scripts = array();

    /**
     * List of core object styles ( common styles )
     *
     * @since 1.0.0
     * @access protected
     * @var      array
     */
    protected $styles = array();

    /**
     * prefix for min scripts
     *
     * @since 1.0.0
     * @access protected
     * @var      string
     */
    protected $debug_scripts = null;

    /**
     * prefix for min styles
     *
     * @since 1.0.0
     * @access protected
     * @var      string
     */
    protected $debug_styles = null; 

    /**
     * UIX constructor
     *
     * @since 1.0.0
     * @access protected
     * @param string $slug Object slug
     * @param array $object Objects structure array
     * @param uix $parent Parent UIX Object
     */
    protected function __construct( $slug, $object, $parent = null ) {
        
        // set the slug
        $this->slug = $slug;
        // set the object
        $this->struct = $object;
        // set parent if given
        if( null !== $parent && is_object( $parent ) )
            $this->parent = $parent;

        // Set the root URL for this plugin.
        $this->set_url();

        // do setup
        if( $this->setup() ){ return; }

        // enable / disable debug scripts
        $this->debug_scripts();

        // enable / disable debug styles
        $this->debug_styles();

        // define then register core styles
        $this->uix_styles();

        // define then register core scripts
        $this->uix_scripts();

        // start internal actions to allow for automating post init
        $this->actions();

    }

    /**
     * Autoload Children - Checks structure for nested structures
     *
     * @since 1.0.0
     * @access public
     */
    public function setup(){
        foreach ( $this->struct as $struct_key=>$sub_struct ){
            if( is_array( $sub_struct ) && uix()->get_register_callback( $struct_key ) ){
                foreach( $sub_struct as $sub_slug => $sub_structure ){
                    $this->{$struct_key}( $sub_slug, $sub_structure );    
                }
            }
        }
    }

    /**
     * All objects loaded - application method for finishing off loading objects
     *
     * @since 1.0.0
     * @access public
     */
    public function init(){}

    /**
     * setup actions and hooks - ovveride to add specific hooks. use parent::actions() to keep admin head
     *
     * @since 1.0.0
     * @access protected
     */
    protected function actions() {
        // init uix after loaded
        add_action( 'init', array( $this, 'init' ) );
        // init UIX headers
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_core' ) );
        // queue helps
        add_action( 'admin_head', array( $this, 'add_help' ) );        
    }


    /**
     * Enabled debuging of scripts
     *
     * @since 1.0.0
     * @access protected
     */
    protected function debug_scripts() {
        // detect debug scripts
        if( !defined( 'DEBUG_SCRIPTS' ) ){
            $this->debug_scripts = '.min';
        }
    }

    /**
     * Enabled debuging of styles
     *
     * @since 1.0.0
     * @access protected
     */
    protected function debug_styles() {
        // detect debug styles      
        if( !defined( 'DEBUG_STYLES' ) ){
            $this->debug_styles = '.min';
        }
    }   


    /**
     * Define core UIX styles - override to register core ( common styles for uix type )
     *
     * @since 1.0.0
     * @access public
     */
    public function uix_styles() {
        // Initilize core styles
        $styles = array();
        // push to activly register styles
        $this->styles( $styles );

    }

    /**
     * Define core UIX scripts - override to register core ( common scripts for uix type )
     *
     * @since 1.0.0
     * @access public
     */
    public function uix_scripts() {
        // Initilize core scripts
        $scripts = array();
        // push to activly register scripts
        $this->scripts( $scripts );
    }


    /**
     * uix object id
     *
     * @since 1.0.0
     * @access public
     * @return string The object ID
     */
    public function id(){
        $id = 'uix-' . $this->type . '-' . $this->slug;
        if( !empty( $this->parent ) )
            $id .= $this->parent->id();
        return $id;
    }

    /**
     * Register the core UIX styles
     *
     * @since 1.0.0
     * @access public
     * @param array Array of styles to be enqueued for all objects of current instance
     */
    public function styles( array $styles ) {
        
        if( !empty( $this->struct['styles'] ) )
            $styles = array_merge( $this->struct['styles'], $styles );

        /**
         * Filter UIX styles
         *
         * @param array $styles array of UIX styles to be registered
         */
        $styles = apply_filters( 'uix_set_styles-' . $this->type, $styles );

        $this->styles = array_merge( $this->styles, $styles );

    }


    /**
     * Register the core UIX scripts
     *
     * @since 1.0.0
     * @access public
     * @param array Array of scripts to be enqueued for all objects of current instance
     */
    public function scripts( array $scripts ) {

        if( !empty( $this->struct['scripts'] ) )
            $scripts = array_merge( $this->struct['scripts'], $scripts );

        /**
         * Filter UIX scripts
         *
         * @param array $scripts array of core UIX scripts to be registered
         */
        $scripts = apply_filters( 'uix_set_scripts-' . $this->type, $scripts );

        $this->scripts = array_merge( $this->scripts, $scripts );

    }

    /**
     * Register the UIX objects
     *
     * @since 1.0.0
     * @access public
     * @param string $slug Object slug
     * @param array $object object structure array
     * @return object|\uix object instance
     */
    public static function register( $slug, $object, $parent = null ) {
            // get the current instance
            $caller = get_called_class();
            return new $caller( $slug, $object, $parent );
    }

    /**
     * Adds child objects to the current object
     *
     * @since 1.0.0
     * @access public
     * @param string $type Child object type
     * @param string $slug Child object slug
     * @param array $structure object structure array
     * @return uix the child object added
     */
    public function add_child( $type, $slug, $structure ) {
        return $this->{$type}( $slug, $structure );
    }

    /**
     * Magic caller for adding child objects
     *
     * @since 1.0.0
     * @access public
     * @param string $type Type of object to attempt to create
     * @param array $args arguments for the caller
     * @return UIX|null
     */    
    public function __call( $type, $args ){
        $init = uix()->get_register_callback( $type );
        $child = null;
        if( null !== $init ){
            $args[] = $this;
            $child = call_user_func_array( $init, $args );
            if( null !== $child ){
                $this->child[ $args[0] ] = $child;
            }
        }
        return $child;
    }

    /**
     * enqueue core assets
     *
     * @since 1.0.0
     * @access public
     */
    public function enqueue_core() {

        // attempt to get a config
        if( !$this->is_active() ){ return; }

        /**
         * do object initilisation
         *
         * @param object current uix instance
         */
        do_action( 'uix_admin_enqueue_scripts' . $this->type, $this );

        // enqueue core scripts
        $this->enqueue( $this->scripts, 'script' );

        // enqueue core styles
        $this->enqueue( $this->styles, 'style' );

        // done enqueuing 
        $this->enqueue_active_assets();
    }

    /**
     * runs after assets have been enqueued
     *
     * @since 1.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){}

    /**
     * Detects the root of the plugin folder and sets the URL
     *
     * @since 1.0.0
     * @access public
     */
    public function set_url(){

        $plugins_url = plugins_url();
        $this_url = trim( substr( trailingslashit( plugin_dir_url(  __FILE__ ) ), strlen( $plugins_url ) ), '/' );
        
        if( false !== strpos( $this_url, '/') ){
            $url_path = explode('/', $this_url );
            // generic 3 path depth: classes/namespace/ui|data
            array_splice( $url_path, count( $url_path ) - 3 );
            $this_url = implode( '/', $url_path );
        }
        // setup the base URL
        $this->url = trailingslashit( $plugins_url . '/' . $this_url );
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

    /**
     * Determin if a UIX object should be active for this screen
     * Intended to be ovveridden
     * @since 1.0.0
     * @access public
     */
    public function is_active(){
        if( !empty( $this->parent ) )
            return $this->parent->is_active();
        
        return true; // base is_active will result in true;
    }

    /**
     * Add defined contextual help to current screen
     *
     * @since 1.0.0
     * @access public
     */
    public function add_help(){
        
        if( ! $this->is_active() ){ return; }

        $screen = get_current_screen();
        
        if( !empty( $this->struct['help'] ) ){
            foreach( (array) $this->struct['help'] as $help_slug => $help ){

                if( is_file( $help['content'] ) && file_exists( $help['content'] ) ){
                    ob_start();
                    include $help['content'];
                    $content = ob_get_clean();
                }else{
                    $content = $help['content'];
                }

                $screen->add_help_tab( array(
                    'id'       =>   $help_slug,
                    'title'    =>   $help['title'],
                    'content'  =>   $content
                ));
            }
        }            
        // Help sidebars are optional
        if(!empty( $this->struct['help_sidebar'] ) ){
            $screen->set_help_sidebar( $this->struct['help_sidebar'] );
        }
    }

    /**
     * Base color helper
     *
     * @since 1.0.0
     * @access public
     */
    protected function base_color(){
        if( empty( $this->struct['base_color'] ) ){
            if( !empty( $this->parent ) )
                return $this->parent->base_color();
        }else{
            return $this->struct['base_color'];
        }

        return '#0073aa';

    }

    /**
     * Render the UIX object
     *
     * @since 1.0.0
     * @access public
     */
    abstract public function render();

}