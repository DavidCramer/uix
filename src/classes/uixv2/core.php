<?php
/**
 * UIXV2 Core Logic
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2;

/**
 * Core logic class
 * @package uixv2
 * @author  David Cramer
 */
class core{

	/**
	 * constructor for core plugin logic
	 *
	 * @since 2.0.0
	 *
	 */
	public function __construct() {

		// start defining your plugin logic

		// to get data from UIX use the below example
		// Full config array of a page
		$full_config = \uixv2\ui\pages::get_val('slug');
		// Just an array of a specific tab
		$tab_config = \uixv2\ui\pages::get_val('slug.tab');
		// Just a value of a specific field
		$tab_config = \uixv2\ui\pages::get_val('slug.tab.field');
		// If a specific field is a atructure, keep dot pathing it
		$tab_config = \uixv2\ui\pages::get_val('slug.tab.field.part.subpart.subsubpart[.etc]');

	}

}