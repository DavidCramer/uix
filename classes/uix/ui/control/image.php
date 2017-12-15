<?php
/**
 * UIX Image Control
 *
 * @package   controls
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

namespace uix\ui\control;

/**
 * Image picker field
 *
 * @since 3.0.0
 */
class image extends \uix\ui\control {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access      public
	 * @var         string
	 */
	public $type = 'image';

	/**
	 * Checks if the current control is active.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function is_active() {
		$active = parent::is_active();

		if ( true === $active ) {
			wp_enqueue_media();
		}

		return $active;
	}

	/**
	 * Define core UIX scripts - override to register core ( common scripts for
	 * uix type )
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_assets() {

		$this->assets['style']['image-control']  = $this->url . 'assets/controls/image/css/image' . UIX_ASSET_DEBUG . '.css';
		$this->assets['script']['image-control'] = [
			'src'       => $this->url . 'assets/controls/image/js/image' . UIX_ASSET_DEBUG . '.js',
			'in_footer' => true,
		];
		parent::set_assets();
	}

	/**
	 * Returns the main input field for rendering
	 *
	 * @since  1.0.0
	 * @see    \uix\ui\uix
	 * @access public
	 * @return string Input field HTML string
	 */
	public function input() {

		$output       = '<input type="hidden" value="' . esc_attr( $this->get_value() ) . '" ' . $this->build_attributes() . '>';
		$button_text  = isset( $this->struct['add_label'] ) ? $this->struct['add_label'] : __( 'Select Image', 'uix' );
		$preview_size = isset( $this->struct['preview_size'] ) ? $this->struct['preview_size'] : 'medium';
		$output       .= '<div class="uix-image-control-wrapper" id="' . esc_attr( $this->id() ) . '-wrap">';
		if ( null !== $this->get_value() ) {
			$output .= $this->get_preview( $preview_size );
		}
		$output .= '</div>';
		$output .= '<button type="button" data-size="' . esc_attr( $preview_size ) . '" data-target="' . esc_attr( $this->id() ) . '" class="button button-small uix-image-control-button">' . esc_html( $button_text ) . '</button>';

		return $output;
	}

	/**
	 * Render the Control preview.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $preview_size The size of the preview.
	 *
	 * @return string HTML of rendered preview
	 */
	private function get_preview( $preview_size ) {
		$data   = wp_get_attachment_image_src( $this->get_value(), $preview_size );
		$return = null;
		if ( ! empty( $data ) ) {
			$return = '<img src="' . esc_attr( $data[0] ) . '" class="uix-image-control-preview"><a href="#" class="uix-image-control-remove" data-target="' . $this->id() . '"><span class="dashicons dashicons-no"></span></a>';
		}

		return $return;

	}
}
