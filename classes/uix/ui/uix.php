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
     * The type of UI object
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $type = 'uix';

    /**
     * Config Structure of object
     *
     * @since 1.0.0
     * @access public
     * @var      array
     */
    public $struct = array();

    /**
     * Set this object type assets
     *
     * @since 1.0.0
     * @access public
     * @var      array
     */
    public $assets = array(
        'script' => array(),
        'style' => array()
    );

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
     * List of attributes to apply to the wrapper element
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    public $attributes = array();

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
        $this->parent = $parent;
        // Set the root URL for this plugin.
        $this->set_url();
        // do setup
        $this->setup();
        // setup attributes
        $this->set_attributes();
        // Set required assets
        $this->set_assets();
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
            if( is_array( $sub_struct ) && uix()->get_register_callback( $struct_key ) )
                $this->process_children( $struct_key );
        }
    }

    /**
     * All objects loaded - application method for finishing off loading objects
     *
     * @since 1.0.0
     * @access public
     */
    public function process_children( $type ){

        foreach( $this->struct[ $type ]  as $sub_slug => $sub_structure ){
            if( !empty( $sub_structure['id'] ) )
                $sub_slug = $sub_structure['id'];

            $this->{$type}($sub_slug, $sub_structure);
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

        // set location
        $location = 'wp_print_styles';

        if( is_admin() )
            $location = 'admin_enqueue_scripts';

        // init UIX headers
        add_action( $location, array( $this, 'enqueue_core' ) );

    }

    /**
     * Define core UIX styles - override to register core ( common styles for uix type )
     *
     * @since 1.0.0
     * @access public
     */
    public function set_assets() {


        if( !empty( $this->struct['style'] ) )
            $this->assets['style'] = array_merge( $this->assets['style'], $this->struct['style'] );

        if( !empty( $this->struct['script'] ) )
            $this->assets['script'] = array_merge( $this->assets['script'], $this->struct['script'] );


    }


    /**
     * uix object id
     *
     * @since 1.0.0
     * @access public
     * @return string The object ID
     */
    public function id(){
        $id = 'uix-' . $this->slug;
        if( !empty( $this->parent ) )
            $id = $this->parent->id() . '-' . $this->slug;

        return $id;
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

        // set assets . methods at before this point can set assets, after this not so much.
        $this->set_assets();

        /**
         * do object initilisation
         *
         * @param object current uix instance
         */
        do_action( 'uix_admin_enqueue_scripts' . $this->type, $this );

        // push assets to ui manager
        uix()->set_assets( $this->assets );

        // done enqueuing - dpo inline or manual enqueue.
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
     * Sets the wrappers attributes
     *
     * @since 1.0.0
     * @access public
     */
    public function set_attributes(){

        $this->attributes[ 'id' ] = $this->id();

        if( !empty( $this->struct['attributes'] ) )
            $this->attributes = array_merge( $this->attributes, $this->struct['attributes'] );

    }


    /**
     * Build Attributes for the input control
     *
     * @since  1.0.0
     * @access public
     * @return string Attributes string for applying to an element
     */
    public function build_attributes() {
        
        $attributes = array();
        foreach( $this->attributes as $att => $value)
            $attributes[] = sprintf( '%s="%s"', esc_html( $att ), esc_attr( $value ) );

        return implode( ' ', $attributes );
    }

    /**
     * Render the UIX object
     *
     * @since 1.0.0
     * @access public
     * @return string HTML of rendered object
     */
    abstract public function render();

    /**
     * Render the child objects
     *
     * @since 1.0.0
     * @access public
     * @return string|null
     */
    public function render_children(){
        $output = null;
        foreach ($this->child as $child)
            $output .= $child->render();

        return $output;
    }


}