<?php
/**
 * UIX Control
 *
 * @package   controls
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui\control;

/**
 * Date picker
 *
 * @since 1.0.0
 */
class date extends \uix\ui\control\text {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'date';


	/**
	 * Define core UIX scripts - override to register core ( common scripts for uix type )
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_assets() {

		// set style
		$this->assets['style']['date']       = $this->url . 'assets/controls/datepicker/flatpickr.min.css';
		$this->assets['style']['date-theme'] = $this->url . 'assets/controls/datepicker/flatpickr.airbnb.min.css';
		$this->assets['script']['date']      = $this->url . 'assets/controls/datepicker/flatpickr.min.js';
		$this->assets['script']['date-init'] = $this->url . 'assets/controls/datepicker/flatpickr-init.js';

		parent::set_assets();
	}

	/**
	 * Gets the attributes for the control.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_attributes() {

		parent::set_attributes();
		$this->attributes['class'] = 'flatpickr';
	}

	/**
	 * Returns the main input field for rendering
	 *
	 * @since 1.0.0
	 * @see \uix\ui\uix
	 * @access public
	 * @return string Input field HTML string
	 */
	public function input() {

		return '<input type="text" value="' . esc_attr( $this->get_value() ) . '" ' . $this->build_attributes() . '>';
	}

	/**
	 * Enqueues specific tabs assets for the active pages
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function set_active_styles() {

		$style = '.flatpickr-day.selected, .flatpickr-day.selected:focus, .flatpickr-day.selected:hover { background: ' . $this->base_color() . ' !important; border-color: ' . $this->base_color() . ' !important;}';
		$style .= '.flatpickr-day.today:not(.selected){ border-bottom: 1px solid ' . $this->base_color() . '!important; border-color: transparent transparent ' . $this->base_color() . '!important; }';
		$style .= '.flatpickr-day.today:focus, .flatpickr-day.today:hover {background: ' . $this->base_color() . ' !important;	border-color: ' . $this->base_color() . ' !important;}';
		$style .= '.flatpickr-next-month svg:hover, .flatpickr-prev-month svg:hover {fill: ' . $this->base_color() . ' !important;}';
		uix_share()->set_active_styles( $style );

	}
}
