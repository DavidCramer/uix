<?php

/**
 * Interface for data load
 *
 * @package   uix
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
	 * @since 1.0.0
	 *
	 * @param string $slug
	 * @return array of data
	 */
	public function get_data( $slug );

}
