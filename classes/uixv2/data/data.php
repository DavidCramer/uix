<?php

/**
 * Base data interface
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\data;

abstract class data extends \uixv2\ui\uix{

    /**
     * object data
     *
     * @since 2.0.0
     * @access protected
     * @var     mixed
     */
    protected $data;

    /**
     * set the object's data
     * @since 2.0.0
     * @param mixed $data the data to be set
     */
    public function set_data( $data ){
        $this->data = $data;
    }

    /**
     * get the object's data
     * @since 2.0.0
     * @return mixed $data
     */
    public function get_data(){
        return $this->data;
    }


}
