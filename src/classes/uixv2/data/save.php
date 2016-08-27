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
     * @since 1.0.0
     * @param string $slug slug of the object
     * @param array $data array of data to be saved
     *
     * @return bool true on successful save
     */
    public function save_data( $slug, $data );

}
