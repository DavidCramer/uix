<?php

/**
 * Interface for data saving
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\data;

interface save extends data {

    /**
     * save data
     *
     * @since 2.0.0
     * @param string $slug slug of the object
     * @param mixed $data Data to be saved for the object
     */
    public function save_data( $slug, $data );

}
