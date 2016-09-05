<?php

/**
 * Base data interface
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix2\data;

abstract class data extends \uix2\ui\uix{

    /**
     * object data
     *
     * @since 2.0.0
     * @access private
     * @var     mixed
     */
    private $data;

    /**
     * Sets the objects sanitization filter
     *
     * @since 2.0.0
     * @access public
     * @see \uix2\uix
     */
    public function setup() {
        if( !empty( $this->struct['sanitize_callback'] ) )
            add_filter( 'uix_' . $this->slug . '_sanitize_' . $this->type, $this->struct['sanitize_callback'] );
    }

    /**
     * set the object's data
     * @since 2.0.0
     * @access public
     * @param mixed $data the data to be set
     */
    public function set_data( $data ){
        $this->data = apply_filters( 'uix_' . $this->slug . '_sanitize_' . $this->type, $data, $this );
    }

    /**
     * get the object's data
     * @since 2.0.0
     * @access public
     * @return mixed $data
     */
    public function get_data(){
        return $this->data;
    }


}
