<?php
/**
 * UIX Box
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix2\ui;

/**
 * Box class
 * @package uix2\ui
 * @author  David Cramer
 */
class box extends panel implements \uix2\data\save, \uix2\data\load{

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access public
     * @var      string
     */
    public $type = 'box';

    /**
     * Sets the controls data
     *
     * @since 2.0.0
     * @see \uix2\uix
     * @access public
     */
    public function init() {
        // run parents to setup sanitization filters
        parent::init();
        
        $data = uix2()->request_vars( 'post' );
        if( isset( $data[ 'uixNonce_' . $this->id() ] ) && wp_verify_nonce( $data[ 'uixNonce_' . $this->id() ], $this->id() ) ){
            
            $this->save_data();

        }else{  
            // load data normally
            $this->set_data( $this->load_data() );
        }

    }

    /**
     * save data to database
     *
     * @since 2.0.0
     * @access public
     */
    public function save_data(){
        return update_option( $this->store_key(), $this->get_data() );
    }

    /**
     * Get data
     *
     * @since 2.0.0
     * @access public
     * @return mixed $data Requested data of the object
     */
    public function load_data(){
        return get_option( $this->store_key(), $this->get_data() );
    }

    /**
     * get the objects data store key
     * @since 2.0.0
     * @access public
     * @return string $store_key the defined option name for this UIX object
     */
    public function store_key(){
        if( !empty( $this->struct['store_key'] ) )
            return $this->struct['store_key'];
        return sanitize_key( $this->slug );
    }


    /**
     * Render the Control
     *
     * @since 2.0.0
     * @see \uix2\ui\uix
     * @access public
     */
    public function render(){

        wp_nonce_field( $this->id(), 'uixNonce_' . $this->id() );
        parent::render();
    }

}