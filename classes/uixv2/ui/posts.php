<?php
/**
 * UIX Post Types
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\ui;

/**
 * Pages class
 * @package uixv2\ui
 * @author  David Cramer
 */
class posts extends uix{

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access protected
     * @var      string
     */
    protected $type = 'post';

    /**
     * setup actions and hooks to register post types
     *
     * @since 2.0.0
     */
    protected function actions() {

        // run parent actions ( keep 'admin_head' hook )
        parent::actions();
        // add settings page
        add_action( 'init', array( $this, 'prepare_objects' ) );

    }

    /**
     * Define core UIX styling to identify UIX post types
     *
     * @since 2.0.0
     */
    public function uix_styles() {
        $pages_styles = array(
            'admin'    =>  $this->url . 'assets/css/admin' . $this->debug_styles . '.css',           
        );
        $this->styles( $pages_styles );
    }
    
    /**
     * Prepare the objects for register_post_type
     *
     * @since 2.0.0
     * @uses "init" hook
     */
    public function prepare_objects() {
        $slugs = array_keys( $this->objects );
        foreach( $slugs as $type ){
            $this->render( $type );
        }
    }

    /**
     * Render (register) the post type
     *
     * @since 2.0.0
     */
    public function render( $slug ) {
        $uix = $this->get( $slug );
        if( !empty( $uix['post_type'] ) ){
            register_post_type( $slug, $uix['post_type'] );
        }
    }

    /**
     * Determin which post types are active and set them active and render some styling
     * Intended to be ovveridden
     * @since 2.0.0
     */
    protected function locate(){

        $screen = get_current_screen();
        // check the screen is valid and is a uix post type page
        if( !is_object( $screen ) || empty( $screen->post_type ) || empty( $this->objects[ $screen->post_type ] ) ){
            return;
        }
        // output the styles
        $uix = $this->get( $screen->post_type );
        if( !empty( $uix['base_color'] ) ){
        ?><style type="text/css">
            .contextual-help-tabs .active {
                border-left: 6px solid <?php echo $uix['base_color']; ?> !important;
            }
            #wpbody-content .wrap > h1 {
                box-shadow: 0 0 2px rgba(0, 2, 0, 0.1),11px 0 0 <?php echo $uix['base_color']; ?> inset;
            }
            #wpbody-content .wrap > h1 a.page-title-action:hover{
                background: <?php echo $uix['base_color']; ?>;
                border-color: <?php echo $uix['base_color']; ?>;
            }
            #wpbody-content .wrap > h1 a.page-title-action:focus{
                box-shadow: 0 0 2px <?php echo $uix['base_color']; ?>;
                border-color: <?php echo $uix['base_color']; ?>;
            }
        </style>
        <?php
        }
        // add to active slugs
        $this->set_active( $screen->post_type );
    }

}