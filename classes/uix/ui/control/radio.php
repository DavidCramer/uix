<?php
/**
 * UIX Controls
 *
 * @package   controls
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui\control;

/**
 * Radio group input
 *
 * @since 1.0.0
 */
class radio extends \uix\ui\control {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'radio';

	/**
	 * Gets the attributes for the control.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_attributes() {
		parent::set_attributes();

		$this->attributes['type'] = $this->type;
		unset( $this->attributes['id'] );

	}


	/**
	 * Returns the main input field for rendering
	 *
	 * @since 1.0.0
	 * @see \uix\ui\uix
	 * @access public
	 * @return string
	 */
	public function input() {

		$input  = '';
		$values = (array) $this->get_value();
		$id     = $this->id();

		foreach ( $this->struct['choices'] as $option_value => $option_label ) {
			$sel       = null;
			$option_id = $id . '-' . sanitize_key( $option_value );
			if ( in_array( $option_value, $values ) ) {
				$sel = ' checked="checked"';
			}

			$input .= '<div class="uix-' . esc_attr( $this->type ) . '">';
			$input .= '<label for="' . $option_id . '">';
			$input .= '<input id="' . $option_id . '" ' . $this->build_attributes() . ' value="' . esc_attr( $option_value ) . '"' . $sel . '>';
			$input .= esc_html( $option_label );
			$input .= '</label>';
			$input .= '</div>';
		}

		return $input;
	}

}
