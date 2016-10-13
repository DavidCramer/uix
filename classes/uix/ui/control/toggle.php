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
 * Pretty little on/off type toggle switch
 *
 * @since 1.0.0
 */
class toggle extends \uix\ui\control {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'toggle';

	/**
	 * The on and off icons to use
	 *
	 * @since       1.0.0
	 * @access public
	 * @var        array
	 */
	public $icons = array( 'on' => 'dashicons-yes', 'off' => '' );


	/**
	 * Gets the classes for the control input
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function classes() {

		$classes = array(
			'toggle-checkbox',
		);

		if ( ! empty( $this->struct['on_icon'] ) ) {
			$this->icons['on'] = $this->struct['on_icon'];
		}

		if ( ! empty( $this->struct['off_icon'] ) ) {
			$this->icons['off'] = $this->struct['off_icon'];
		}

		return $classes;
	}

	/**
	 * Define core UIX styles - override to register core ( common styles for uix type )
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_assets() {

		// Initilize core styles
		$this->assets['style']['toggle'] = $this->url . 'assets/controls/toggle/css/toggle' . UIX_ASSET_DEBUG . '.css';

		// Initilize core scripts
		$this->assets['script']['toggle-control-init'] = array(
			'src'       => $this->url . 'assets/controls/toggle/js/toggle' . UIX_ASSET_DEBUG . '.js',
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
		if ( ! empty( $this->struct['toggle_all'] ) ) {
			$this->attributes['data-toggle-all'] = 'true';
		}

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

		$input = '<label class="switch setting_toggle_alert" data-for="' . esc_attr( $this->id() ) . '-control">';
		$input .= '<input type="checkbox" value="1" ' . $this->build_attributes() . ' data-value="' . esc_attr( $this->get_value() ) . '">';
		$input .= '<span class="toggle-on dashicons ' . esc_attr( $this->icons['on'] ) . '"></span>';
		$input .= '<span class="toggle-off dashicons ' . esc_attr( $this->icons['off'] ) . '"></span>';
		$input .= '<div class="box"></div>';
		$input .= '</label>';

		return $input;
	}

	/**
	 * Returns the description for the control
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string description string
	 */
	public function sdescription() {
		$output = null;
		if ( isset( $this->struct['description'] ) ) {
			$output .= '<span class="uix-toggle-description">' . esc_html( $this->struct['description'] ) . '</span>';
		}

		return $output;
	}

	/**
	 * Enqueues specific tabs assets for the active pages
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function enqueue_active_assets() {

		echo '<style type="text/css">';

		if ( ! empty( $this->struct['base_color'] ) ) {
			echo '.' . $this->id() . ' > .uix-control-input > .switch.active {background: ' . $this->struct['base_color'] . ';}';
		}

		if ( ! empty( $this->struct['off_color'] ) ) {
			echo '.' . $this->id() . '> .uix-control-input > .switch { background: ' . $this->struct['off_color'] . ';}';
		}

		echo '</style>';

	}

}
