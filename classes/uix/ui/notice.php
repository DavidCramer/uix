<?php
/**
 * UIX Notice
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * UIX Notice. Handles displaying of admin notices.
 *
 * @package uix\ui
 * @author  David Cramer
 */
class notice extends \uix\data\data {

	/**
	 * The type of object
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $type = 'notice';

	/**
	 * The wrapper elements class names
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $classes = array( 'uix-notice', 'notice' );

	/**
	 * The wrapper elements main attributes
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $attributes;

	/**
	 * The state type of the notice ( warning, error, success )
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $state = 'notice';


	/**
	 * setup notice
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function setup() {

		if ( isset( $this->struct['description'] ) ) {
			$this->set_data( $this->struct['description'] );
		}

		$this->set_attributes();
		$this->set_dismissable();
		$this->set_state();

	}

	/**
	 * Sets the wrappers class names
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_dismissable() {
		if ( ! empty( $this->struct['dismissable'] ) ) {
			$this->classes[] = 'is-dismissible';
		}
	}

	/**
	 * Sets the wrappers class names
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_state() {
		if ( ! empty( $this->struct['state'] ) ) {
			$this->classes[] = 'notice-' . $this->struct['state'];
		} else {
			$this->classes[] = 'notice-warning';
		}
	}

	/**
	 * Render the panel
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string HTML of notice
	 */
	public function render() {
		$output = null;

		$this->attributes['class'] = implode( ' ', $this->classes );
		$note                      = $this->get_data();
		$output .= '<div ' . $this->build_attributes() . '>';
		$output .= '<p>';
		$output .= $note[ $this->slug ];
		$output .= '</p>';
		$output .= $this->dismiss();
		$output .= '</div>';

		return $output;

	}

	/**
	 * Render a dismiss button
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string|null HTML of dismiss button
	 */
	public function dismiss() {
		$output = null;
		if ( ! empty( $this->struct['dismissable'] ) ) {
			$output .= '<button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_attr__( 'Dismiss this notice.' ) . '</span></button>';
		}

		return $output;
	}

	/**
	 * Set hooks on when to load the notices
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function actions() {
		parent::actions();
		// init uix after loaded
		add_action( 'admin_notices', array( $this, 'render' ) );
	}

}
