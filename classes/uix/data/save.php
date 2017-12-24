<?php

/**
 * Interface for data saving
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

namespace uix\data;

interface save {


	/**
	 * Save data to database
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function save_data();

	/**
	 * Get the objects data store key
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string $store_key the defined option name for this UIX object
	 */
	public function store_key();

}

