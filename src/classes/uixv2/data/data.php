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

interface data {

    /**
     * get the objects data store key
     * @since 1.0.0
     *
     * @return string $store_key the defined option name for this UIX object
     */
    public function store_key( $slug );

}
