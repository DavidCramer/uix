<?php
/**
 * UIX Controls - Autocomplete
 *
 * @package   controls
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui\control;

/**
 * Standard text input field
 *
 * @since 1.0.0
 */
class autocomplete extends \uix\ui\control\select {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'autocomplete';

	/**
	 * Gets the classes for the control input
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function classes() {

		return array(
			'uix-select2',
		);

	}

	/**
	 * register scritps and styles
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_assets() {

		// Initilize core styles
		$this->assets['style']['select2'] = $this->url . 'assets/controls/autocomplete/css/select2' . UIX_ASSET_DEBUG . '.css';

		// Initilize core scripts
		$this->assets['script']['select2']      = array(
			'src'       => $this->url . 'assets/controls/autocomplete/js/select2' . UIX_ASSET_DEBUG . '.js',
			'in_footer' => true,
		);
		$this->assets['script']['select2-init'] = array(
			'src'       => $this->url . 'assets/controls/autocomplete/js/select2-init' . UIX_ASSET_DEBUG . '.js',
			'in_footer' => true,
		);

		parent::set_assets();
	}

	/**
	 * Sets styling colors
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function set_active_styles() {

		$style = '#select2-' . $this->id() . '-control-results .select2-results__option--highlighted[aria-selected] {background-color: ' . $this->base_color() . ';}';
		uix_share()->set_active_styles( $style );

	}

}
