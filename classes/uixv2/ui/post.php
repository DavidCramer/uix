<?php
/**
 * UIX Post Type
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\ui;

/**
 * UIX Post Type class
 * @package uixv2\ui
 * @author  David Cramer
 */
class post extends uix{

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access public
     * @var      string
     */
    public $type = 'post';

    /**
     * registeres metaboxes if defined
     *
     * @since 2.0.0
     * @see \uixv2\uix
     * @param array $objects object structure array
     */
    public function setup() {

        if( !empty( $this->struct['metaboxes'] ) ){
            foreach ( $this->struct['metaboxes'] as $metabox_slug => $metabox_structure)
                $this->add_child( 'metabox', $metabox_slug, $metabox_structure );
        }
    }


    /**
     * setup actions and hooks to register post types
     *
     * @since 2.0.0
     */
    protected function actions() {

        // run parent actions ( keep 'admin_head' hook )
        parent::actions();
        // add settings page
        add_action( 'init', array( $this, 'render' ) );

    }


    /**
     * Render the custom header styles
     *
     * @since 2.0.0
     *
     */
    protected function enqueue_active_assets(){
        // output the styles
        if( !empty( $this->struct['base_color'] ) ){
        ?><style type="text/css">
            .contextual-help-tabs .active {
                border-left: 6px solid <?php echo $this->struct['base_color']; ?> !important;
            }
            #wpbody-content .wrap > h1 {
                box-shadow: 0 0 2px rgba(0, 2, 0, 0.1),11px 0 0 <?php echo $this->struct['base_color']; ?> inset;
            }
            #wpbody-content .wrap > h1 a.page-title-action:hover{
                background: <?php echo $this->struct['base_color']; ?>;
                border-color: <?php echo $this->struct['base_color']; ?>;
            }
            #wpbody-content .wrap > h1 a.page-title-action:focus{
                box-shadow: 0 0 2px <?php echo $this->struct['base_color']; ?>;
                border-color: <?php echo $this->struct['base_color']; ?>;
            }
        </style>
        <?php
        }
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
     * Render (register) the post type
     *
     * @since 2.0.0
     */
    public function render() {

        if( !empty( $this->struct['post_type'] ) ){
            register_post_type( $this->slug, $this->struct['post_type'] );
        }

    }

    /**
     * Determin which post types are active and set them active and render some styling
     * Intended to be ovveridden
     * @since 2.0.0
     */
    public function is_active(){

        $screen = get_current_screen();

        // check the screen is valid and is a uix post type page
        if( !is_object( $screen ) || empty( $screen->post_type ) || $screen->post_type !== $this->slug ){
            return false;
        }
        return true;
    }

}