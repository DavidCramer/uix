<?php

/**
 * Interface for data saving
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix2\data;

interface save {


    /**
     * save data to database
     *
     * @since 2.0.0
     * @access public
     */
    public function save_data();

    /**
     * get the objects data store key
     * @since 2.0.0
     * @access public
     * @return string $store_key the defined option name for this UIX object
     */
    public function store_key();    

}
