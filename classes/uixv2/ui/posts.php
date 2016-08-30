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
 * @package clarity\ui
 * @author  David Cramer
 */
class posts extends uix{

    /**
     * The type of object
     *
     * @since 2.0.0
     *
     * @var      string
     */
    protected $type = 'post';

    /**
     * register add settings pages
     *
     * @since 2.0.0
     *
     */
    protected function actions() {
        // run parent actions ( keep 'admin_head' hook )
        parent::actions();
        // add settings page
        add_action( 'init', array( $this, 'register_post_type' ) );

    }

    /**
     * Register the post type
     *
     * @since 2.0.0
     * @access public
     */
    public function register_post_type() {

        foreach( (array) $this->objects as $post_type => $args ){
            register_post_type( $post_type, $args );

            //add help if defined
            if( !empty( $args['help'] ) ){
                add_action( 'admin_head', array( $this, 'add_help' ) );
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
        $screen = get_current_screen();
        // check the screen is valid and is a uix post type page
        if( !is_object( $screen ) || empty( $screen->post_type ) || empty( $this->objects[ $screen->post_type ] ) ){
            return $slugs;
        }
        // add to active slugs
        $slugs[] = $screen->post_type; 
        return $slugs;
    }

}