<?php
/**
 * UIX Core
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix2\ui;

/**
 * UIX class
 * @package uix2\ui
 * @author  David Cramer
 */
abstract class uix{

    /**
     * Config Structure of object
     *
     * @since 2.0.0
     * @access public
     * @var      array
     */
    public $struct = array();


    /**
     * The type of UI object
     *
     * @since 2.0.0
     * @access public
     * @var      string
     */
    public $type = 'uix';

    /**
     * object slug
     * @access public
     * @since 2.0.0
     *
     * @var      string
     */
    public $slug;
    
    /**
     * array of child objects
     *
     * @since 2.0.0
     * @access public
     * @var      array
     */
    public $child = array();

    /**
     * Objects parent
     *
     * @since 2.0.0
     * @access public
     * @var      object/uix
     */
    public $parent;    

    /**
     * Base URL of this class
     *
     * @since 2.0.0
     * @access protected
     * @var      string
     */
    protected $url;

    /**
     * List of core object scripts ( common scripts )
     *
     * @since 2.0.0
     * @access protected
     * @var      array
     */
    protected $scripts = array();

    /**
     * List of core object styles ( common styles )
     *
     * @since 2.0.0
     * @access protected
     * @var      array
     */
    protected $styles = array();

    /**
     * prefix for min scripts
     *
     * @since 2.0.0
     * @access protected
     * @var      array
     */
    protected $debug_scripts = null;

    /**
     * prefix for min styles
     *
     * @since 2.0.0
     * @access protected
     * @var      array
     */
    protected $debug_styles = null; 

    /**
     * UIX constructor
     *
     * @since 2.0.0
     * @access protected
     * @param string $slug Object slug
     * @param array $object Objects structure array
     * @param object/uix $parent Parent UIX Object
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
     * Set custom UIX object setup proceses
     *
     * @since 2.0.0
     * @access public
     * @return void|bool return true to stop constructor init sequence if needed to change order
     */
    public function setup() {}


    /**
     * setup actions and hooks - ovveride to add specific hooks. use parent::actions() to keep admin head
     *
     * @since 2.0.0
     * @access protected
     */
    protected function actions() {
        // init UIX headers
        add_action( 'admin_enqueue_scripts', array( $this, 'init' ) );
        // queue helps
        add_action( 'admin_head', array( $this, 'add_help' ) );        
    }


    /**
     * Enabled debuging of scripts
     *
     * @since 2.0.0
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
     * @since 2.0.0
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
     * @since 2.0.0
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
     * @since 2.0.0
     * @access public
     */
    public function uix_scripts() {
        // Initilize core scripts
        $scripts = array();
        // push to activly register scripts
        $this->scripts( $scripts );
    }


    /**
     * Register the core UIX styles
     *
     * @since 2.0.0
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
     * @since 2.0.0
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
     * @since 2.0.0
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
     * @since 2.0.0
     * @access public
     * @param string $type Child object type
     * @param string $slug Child object slug
     * @param array $structure object structure array
     * @return object/UIX the child object added
     */
    public function add_child( $type, $slug, $structure ) {
        return $this->{$type}( $slug, $structure );
    }

    /**
     * Magic caller for adding child objects
     *
     * @since 2.0.0
     * @access public
     * @param string $type Type of object to attempt to create
     * @param array $args arguments for the caller
     * @return UIX|null
     */    
    public function __call( $type, $args ){
        $init = uix2()->get_register_callback( $type );
        $child = null;
        if( null !== $init ){
            $args[] = $this;
            $child = call_user_func_array( $init, $args );
            if( null !== $child ){
                if( empty( $child->struct['base_color'] ) && !empty( $this->struct['base_color'] ) )
                    $child->struct['base_color'] = $this->struct['base_color'];
                
                $this->child[ $args[0] ] = $child;
            }
        }
        return $child;
    }

    /**
     * initialize object and enqueue assets
     *
     * @since 2.0.0
     * @access public
     */
    public function init() {

        // attempt to get a config
        if( !$this->is_active() ){ return; }

        /**
         * do object initilisation
         *
         * @param object current uix instance
         */
        do_action( 'uix_admin_enqueue_scripts' . $this->type, $this );

        // enqueue core scripts and styles
        $assets = array(
            'scripts' => $this->scripts,
            'styles' => $this->styles,
        );
        // enqueue core scripts and styles
        $this->enqueue( $assets, $this->type );

        // done enqueuing 
        $this->enqueue_active_assets();
    }

    /**
     * runs after assets have been enqueued
     *
     * @since 2.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){}

    /**
     * Detects the root of the plugin folder and sets the URL
     *
     * @since 2.0.0
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
     * @since 2.0.0
     * @access protected
     * @param array $set {
     *      array   $scripts array of script sources to be enqueued
     *      string  $prefix prefix for enqueue handle ( usually the object slug )
     * }object array structure
     */
    protected function enqueue( $set, $prefix ){
        // go over the set to see if it has styles or scripts

        // setup default args for array type includes
        $arguments_array = array(
            "src"       => false,
            "deps"      => array(),
            "ver"       => false,
            "in_footer" => false,
            "media"     => false
        );

        // enqueue set specific runtime styles
        if( !empty( $set['styles'] ) ){
            foreach( $set['styles'] as $style_key => $style ){
                if( is_int( $style_key ) ){
                    wp_enqueue_style( $style );
                }else{
                    if( is_array( $style ) ){
                        $args = array_merge( $arguments_array, $style );
                        wp_enqueue_style( $prefix . '-' . $script_key, $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );
                    }else{
                        wp_enqueue_style( $prefix . '-' . $style_key, $style );
                    }
                }
            }
        }
        // enqueue set specific runtime scripts
        if( !empty( $set['scripts'] ) ){
            foreach( $set['scripts'] as $script_key => $script ){
                if( is_int( $script_key ) ){
                    wp_enqueue_script( $script );
                }else{
                    if( is_array( $script ) ){
                        $args = array_merge( $arguments_array, $script );
                        wp_enqueue_script( $prefix . '-' . $script_key, $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );
                    }else{
                        wp_enqueue_script( $prefix . '-' . $script_key, $script );
                    }
                }
            }
        }

    }   

    /**
     * Determin if a UIX object should be active for this screen
     * Intended to be ovveridden
     * @since 2.0.0
     * @access public
     */
    public function is_active(){
        if( !empty( $this->parent ) && $this->parent->is_active() ){ return true; }
        return false;
    }

    /**
     * Add defined contextual help to current screen
     *
     * @since 2.0.0
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
     * Render the UIX object
     *
     * @since 2.0.0
     * @access public
     */
    abstract public function render();

}