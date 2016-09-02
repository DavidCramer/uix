<?php

/**
 * Interface for data load
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\data;

interface load extends data {

    /**
     * Get data
     *
     * @since 2.0.0
     *
     * @param string $slug slug of the object
     * @return mixed $data Requested data of the object
     */
    public function get_data( $slug );

}
