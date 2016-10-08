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
 * Layout Grid Editor
 *
 * @since 1.0.0
 */
class layout extends \uix\ui\control {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'layout';


	/**
	 * Define core UIX scripts - override to register core ( common scripts for uix type )
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_assets() {

		// set style
		$this->assets['style']['layout']      = $this->url . 'assets/controls/layout/css/layout' . UIX_ASSET_DEBUG . '.css';
		$this->assets['style']['layout-grid'] = $this->url . 'assets/controls/layout/css/layout-grid' . UIX_ASSET_DEBUG . '.css';

		// push to register script
		$this->assets['script']['layout'] = array(
			'src'       => $this->url . 'assets/controls/layout/js/layout' . UIX_ASSET_DEBUG . '.js',
			'deps'      => array(
				'jquery-ui-draggable',
				'jquery-ui-droppable',
				'jquery-ui-sortable',
			),
			'in_footer' => true,
		);

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
		$this->attributes['class'] = 'hidden';

	}

	/**
	 * Render the Control
	 *
	 * @since 1.0.0
	 * @see \uix\ui\uix
	 * @access public
	 * @return string HTML of rendered control
	 */
	public function render() {

		$output = '<div id="' . esc_attr( $this->id() ) . '" data-for="' . esc_attr( $this->id() ) . '-control" class="uix-control uix-control-' . esc_attr( $this->type ) . ' ' . esc_attr( $this->id() ) . '"></div>';
		$output .= $this->label();
		$output .= $this->input();

		return $output;
	}

	/**
	 * Returns the main input field for rendering
	 *
	 * @since 1.0.0
	 * @see \uix\ui\uix
	 * @access public
	 * @return string Input field HTML striung
	 */
	public function input() {
		return '<input type="hidden" value="' . esc_attr( $this->get_value() ) . '" ' . $this->build_attributes() . '>';
	}

	/**
	 * Returns the label for the control
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string label of control
	 */
	public function label() {
		$output = null;
		if ( isset( $this->struct['label'] ) ) {
			$output .= '<label for="' . esc_attr( $this->id() ) . '-control" class="uix-add-row"><span class="uix-control-label">' . esc_html( $this->struct['label'] ) . '</span></label>';
		}

		return $output;
	}

	/**
	 * Sets styling colors
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function enqueue_active_assets() {

		echo '<style type="text/css">';
		echo '.' . $this->id() . ' .dashicons.dashicons-plus-alt{ color: ' . $this->base_color() . ' !important;}';
		echo '.' . $this->id() . ' .column-handle{background-color: ' . $this->base_color() . ' !important;}';
		echo '</style>';

	}
}
