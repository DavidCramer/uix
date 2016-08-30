<?php
/**
 * UIX Core
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\ui;

/**
 * UIX class
 * @package uixv2\ui
 * @author  David Cramer
 */
abstract class uix{

    /**
     * The type of object
     *
     * @since 1.0.0
     *
     * @var      string
     */
    protected $type = 'uix';

    /**
     * List of registered objects
     *
     * @since 1.0.0
     *
     * @var      array
     */
    protected $objects = array();

    /**
     * Active slugs
     *
     * @since 1.0.0
     *
     * @var      array
     */
    protected $active_slugs = array();

    /**
     * active objects
     *
     * @since 1.0.0
     *
     * @var      array
     */
    protected $active_objects = array();

    /**
     * Base URL of this class
     *
     * @since 1.0.0
     *
     * @var      string
     */
    protected $url;

    /**
     * List of core scripts
     *
     * @since 1.0.0
     *
     * @var      array
     */
    protected $scripts = array();

    /**
     * List of core styles
     *
     * @since 1.0.0
     *
     * @var      array
     */
    protected $styles = array();

    /**
     * prefix for min scripts
     *
     * @since 1.0.0
     *
     * @var      array
     */
    protected $debug_scripts = null;

    /**
     * prefix for min styles
     *
     * @since 1.0.0
     *
     * @var      array
     */
    protected $debug_styles = null; 

    /**
     * Holds instances
     *
     * @since 1.0.0
     *
     * @var      array
     */
    protected static $instances = array();

    /**
     * UIX constructor - override this to control order of initialization
     *
     * @since 1.0.0
     *
     */
    public function __construct() {
        // Set the root URL for this plugin.
        $this->set_url();

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
     * Return an instance of this class.
     *
     * @since 1.0.0
     *
     * @return    object|\pmts\    A single instance
     */
    public static function get_instance() {

        $caller = get_called_class();
        // If the single instance hasn't been set, set it now.
        if ( ! isset( self::$instances[$caller] ) ) {
            self::$instances[$caller] = new $caller();
        }

        return self::$instances[$caller];

    }

    /**
     * setup actions and hooks - ovveride to add specific hooks. use parent::actions() to keep admin head
     *
     * @since 1.0.0
     *
     */
    protected function actions() {
        // init UIX headers
        add_action( 'admin_head', array( $this, 'init' ) );
        // queue helps
        add_action( 'admin_head', array( $this, 'add_help' ) );        
    }


    /**
     * Enabled debuging of scripts
     *
     * @since 1.0.0
     *
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
     *
     */
    protected function debug_styles() {
        // detect debug styles      
        if( !defined( 'DEBUG_STYLES' ) ){
            $this->debug_styles = '.min';
        }
    }   


    /**
     * Define core UIX styles
     *
     * @since 1.0.0
     *
     */
    public function uix_styles() {
        // Initilize core styles
        $core_styles = array(
            'styles'    =>  $this->url . 'assets/css/admin' . $this->debug_styles . '.css',
            'icons'     =>  $this->url . 'assets/css/icons' . $this->debug_styles . '.css',         
            'grid'      =>  $this->url . 'assets/css/grid' . $this->debug_styles . '.css',
            'controls'  =>  $this->url . 'assets/css/controls' . $this->debug_styles . '.css',
        );

        /**
         * Filter core UIX styles
         *
         * @param array $core_styles array of core UIX styles to be registered
         */
        $core_styles = apply_filters( 'uix_set_core_styles-' . $this->type, $core_styles );
        
        // push to activly register styles
        $this->styles( $core_styles );

    }

    /**
     * Define core UIX scripts
     *
     * @since 1.0.0
     *
     */
    public function uix_scripts() {
        // Initilize core scripts
        $core_scripts = array();

        /**
         * Filter core UIX scripts
         *
         * @param array $core_scripts array of core UIX scripts to be registered
         */
        $core_scripts = apply_filters( 'uix_set_core_styles-' . $this->type, $core_scripts );

        // push to activly register scripts
        $this->scripts( $core_scripts );
    }


    /**
     * Register the core UIX styles
     *
     * @since 1.0.0
     *
     */
    public function styles( array $styles ) {
        
        $this->styles = array_merge( $this->styles, $styles );

    }


    /**
     * Register the core UIX scripts
     *
     * @since 1.0.0
     *
     */
    public function scripts( array $scripts ) {

        $this->scripts = array_merge( $this->scripts, $scripts );

    }

    /**
     * Register the UIX objects
     *
     * @since 1.0.0
     *
     * @param array $objects object structure array
     * @return    object|\uix    A single instance of class
     */
    public static function register( array $objects ) {

        // get the instance
        $uix = static::get_instance();

        // set objects
        $uix->set_objects( $objects );
        
        return $uix;
    }

    /**
     * Register the UIX objects
     *
     * @since 1.0.0
     *
     * @param array $objects object structure array
     */
    public function set_objects( array $objects ) {

        /**
         * Filter objects to be created
         *
         * @param array $objects array of UIX object structures to be registered
         */
        $this->objects = apply_filters( 'uix_register_objects-' . $this->type, $objects );

        
    }

    /**
     * load UIX for the current page
     *
     * @since 1.0.0
     *
     */
    public function init() {

        // attempt to get a config
        $slugs = $this->locate();

        if( empty( $slugs ) ){
            return;
        }

        // set active
        $this->active_slugs = (array) $slugs;

        // enqueue core scripts and styles
        $assets = array(
            'scripts' => $this->scripts,
            'styles' => $this->styles,
        );
        // enqueue core scripts and styles
        $this->enqueue( $assets, $this->type );

        // setup active objects structures
        $this->set_active_objects();

        // enque active objects assets
        $this->enqueue_active_assets();
    }

    /**
     * sets the active objects structures
     *
     * @since 1.0.0
     *
     */
    protected function set_active_objects(){
        // build internal structures of active objects
        foreach( (array) $this->active_slugs as $slug ){
            $this->active_objects[ $slug ] = array(
                'structure' => $this->get( $slug )
            );
        }

    }


    /**
     * sets the active objects structures
     *
     * @since 1.0.0
     *
     */
    protected function enqueue_active_assets(){
        // enque active slugs assets
        foreach( (array) $this->active_objects as $slug => $object ){
            // enqueue stlyes and scripts
            $this->enqueue( $object['structure'], $this->type . '-' . $slug );
        }
    }

    /**
     * Detects the root of the plugin folder and sets the URL
     *
     * @since 1.0.0
     *
     */
    public function set_url(){

        $plugins_url = plugins_url();
        $this_url = trim( substr( plugin_dir_url( __FILE__ ), strlen( $plugins_url ) ), '/' );
        
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
     * @since 0.0.1
     *
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
        if( !empty( $set[ 'styles'] ) ){
            foreach( $set[ 'styles'] as $style_key => $style ){
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
        if( !empty( $set[ 'scripts'] ) ){
            foreach( $set[ 'scripts'] as $script_key => $script ){
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
     * Determin if a UIX object should be loaded for this screen
     * Intended to be ovveridden
     * @since 0.0.1
     *
     * @return array $array of slugs of a registered structures relating to this screen
     */
    protected function locate(){

        $slugs = array();
        
        // default is to use a GET['uix']
        if( isset( $_GET['uix'] ) ){
            foreach( (array) $_GET['uix'] as $uix_slug ) {
                if( !empty( $this->objects[ strip_tags( $uix_slug ) ] ) ){
                    $slugs[] = strip_tags( $uix_slug );
                }
            }
        }
        
        return $slugs;
    }

    /**
     * Add defined contextual help to admin page
     *
     * @since 1.0.0
     */
    public function add_help(){
        
        $slugs = (array) $this->locate();

        if( empty( $slugs ) || count( $slugs ) > 1 ){ return; }

        $uix = $this->get( $slugs[0] ); // help can only be on a single
        
        if( empty( $uix ) || empty( $uix['help'] ) ){ return; }

        $screen = get_current_screen();
        
        foreach( (array) $uix['help'] as $help_slug => $help ){

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
        
        // Help sidebars are optional
        if(!empty( $page['help_sidebar'] ) ){
            $screen->set_help_sidebar( $page['help_sidebar'] );
        }

    }

    /**
     * get the uix config
     *
     * @since 0.0.1
     * @param array $slug registered UIX slug to fetch
     *
     * @return array|null $uix array structure of current uix point or null if invalid
     */
    protected function get( $slug ){

        $uix = null;

        // get the slug from base ID
        if( !empty( $this->objects[ $slug ] ) ){
            $uix = $this->objects[ $slug ];
            /**
             * Filter UIX object
             *
             * @param array $uix The uix object array.
             * @param string $slug slug of object being loaded
             */     
            $uix = apply_filters( 'uix_load-' . $this->type, $uix, $slug );
        }

        return $uix;

    }


}