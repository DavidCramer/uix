<?php
/**
 * UIX Controls
 *
 * @package   controls
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      https://github.com/IonDen/ion.rangeSlider
 * @copyright 2016 David Cramer
 */
namespace uix\ui\control;

/**
 * Implementaion of io.rangeSlider
 *
 * @link http://ionden.com/a/plugins/ion.rangeSlider/en.html
 * @since 1.0.0
 */
class slider extends \uix\ui\control\text {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'slider';

	/**
	 * Define Sliders styles and Scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_assets() {

		// Initilize core styles
		$this->assets['style']['slider-control']       = $this->url . 'assets/controls/slider/css/ion.rangeSlider' . UIX_ASSET_DEBUG . '.css';
		$this->assets['style']['slider-control-theme'] = $this->url . 'assets/controls/slider/css/ion.rangeSlider.skinHTML5' . UIX_ASSET_DEBUG . '.css';

		// Initilize core scripts
		$this->assets['script']['slider-control']      = $this->url . 'assets/controls/slider/js/ion.rangeSlider' . UIX_ASSET_DEBUG . '.js';
		$this->assets['script']['slider-control-init'] = array(
			'src'       => $this->url . 'assets/controls/slider/js/ion.rangeSlider.init' . UIX_ASSET_DEBUG . '.js',
			'in_footer' => true,
		);

		parent::set_assets();
	}

	/**
	 * sets the classes for the control input
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function classes() {

		$classes = array(
			'uix-slider',
		);

		return $classes;
	}

	/**
	 * Gets the attributes for the control.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_attributes() {

		$this->attributes['data-type']                   = 'single';
		$this->attributes['data-input-values-separator'] = ';';

		parent::set_attributes();

	}

	/**
	 * Sets styling colors
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function enqueue_active_assets() {

		if ( ! empty( $this->struct['base_color'] ) ) {
			echo '<style type="text/css">';

			echo '.' . $this->id() . ' .irs-grid-pol {background: ' . $this->struct['base_color'] . ';}';
			echo '.' . $this->id() . ' .irs-bar {border-top: 1px solid ' . $this->struct['base_color'] . ';border-bottom: 1px solid ' . $this->struct['base_color'] . ';background: ' . $this->struct['base_color'] . ';}';
			echo '.' . $this->id() . ' .irs-bar-edge {border: 1px solid ' . $this->struct['base_color'] . ';background: ' . $this->struct['base_color'] . ';}';
			echo '.' . $this->id() . ' .irs-from, .' . $this->id() . ' .irs-to, .' . $this->id() . ' .irs-single {background: ' . $this->struct['base_color'] . ';}';

			echo '</style>';
		}

	}


}
