<?php
/**
 * UIX Box
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * Unlike metaboxes, the box can be rendered via code and will enqueue assets on the page where its declared.
 * A box also has save on a submission. Data is saved as an array structure based on the tree of child objects.
 * 
 * @package uix\ui
 * @author  David Cramer
 */
class box extends panel implements \uix\data\save, \uix\data\load{

    /**
     * The type of object
     *
     * @since 1.0.0
     * @access public
     * @var      string
     */
    public $type = 'box';

    /**
     * Sets the controls data
     *
     * @since 1.0.0
     * @see \uix\uix
     * @access public
     */
    public function init() {
        // run parents to setup sanitization filters
        $data = uix()->request_vars( 'post' );
        if( isset( $data[ 'uixNonce_' . $this->id() ] ) && wp_verify_nonce( $data[ 'uixNonce_' . $this->id() ], $this->id() ) ){
            $this->save_data();
        }else{
            // load data normally
            $this->set_data( $this->load_data() );
        }
    }


    /**
     * set metabox styles
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     */
    public function set_assets() {

        $this->assets['script']['baldrick'] = array(
            'src' => $this->url . 'assets/js/jquery.baldrick' . UIX_ASSET_DEBUG . '.js',
            'deps' => array( 'jquery' ),
        );
        $this->assets['script']['uix-ajax'] = array(
            'src' => $this->url . 'assets/js/ajax' . UIX_ASSET_DEBUG . '.js',
            'deps' => array( 'baldrick' ),
        );
        $this->assets['style']['uix-ajax'] =  $this->url . 'assets/css/ajax' . UIX_ASSET_DEBUG . '.css';

        parent::set_assets();
    }

    /**
     * save data to database
     *
     * @since 1.0.0
     * @access public
     */
    public function save_data(){

        $data = $this->get_data();
        return update_option( $this->store_key(), $this->get_data() );
    }

    /**
     * Get data
     *
     * @since 1.0.0
     * @access public
     * @return mixed $data Requested data of the object
     */
    public function load_data(){
        $data = array(
            $this->slug => get_option( $this->store_key(), $this->get_data() )
        );

        return $data;
    }

    /**
     * Get Data from all controls of this section
     *
     * @since 1.0.0
     * @see \uix\load
     * @return array Array of sections data structured by the controls
     */
    public function get_data(){

        if( empty( $this->data ) ){
            $data = parent::get_data();
            if( !empty( $data[ $this->slug ] ) )
                $this->data = $data[ $this->slug ];
        }

        return $this->data;
    }

    /**
     * get the objects data store key
     * @since 1.0.0
     * @access public
     * @return string $store_key the defined option name for this UIX object
     */
    public function store_key(){
        if( !empty( $this->struct['store_key'] ) )
            return $this->struct['store_key'];
        return sanitize_key( $this->slug );
    }

    /**
     * Sets the wrappers attributes
     *
     * @since 1.0.0
     * @access public
     */
    public function set_attributes(){

        $action = uix()->request_vars('server');
        $this->attributes += array(
            'enctype'   =>  'multipart/form-data',
            'method'    =>  'POST',
            'class'     =>  'uix-ajax uix-' . $this->type,
            'data-uix'  =>  $this->slug,
            'action'    =>  $action['REQUEST_URI'],
        );

        parent::set_attributes();

    }

    /**
     * Render the box
     *
     * @since 1.0.0
     * @access public
     * @return string HMTL of rendered page
     */
    public function render(){
        $output = null;

        $output .= '<form ' . $this->build_attributes() . '>';

        $output .= $this->render_header();
        $output .= parent::render();
        $output .= wp_nonce_field( $this->id(), 'uixNonce_' . $this->id(), true, false );

        $output .= '</form>';

        return $output;
    }

    /**
     * Render the page
     *
     * @since 1.0.0
     * @access public
     * @return string HMTL of rendered page
     */
    public function render_header(){

        $output = null;
        if( !empty( $this->child ) ){
            foreach( $this->child as $child ){
                if( $child->type == 'header' )
                    $output .= $child->render();
            }
        }

        return $output;

    }
}