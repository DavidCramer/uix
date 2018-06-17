<?php
/**
 * UIX Grid
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

namespace uix\ui;

/**
 * A Grid system for layout control
 *
 * @since 1.0.0
 * @see \uix\uix
 */
class grid extends section {

	/**
	 * The type of object
	 *
	 * @since 1.0.0
	 * @access public
	 * @var      string
	 */
	public $type = 'grid';

	/**
	 * All objects loaded - application method for finishing off loading objects
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function setup() {
		if ( ! empty( $this->struct['size'] ) ) {
			$this->struct['size'] = explode( ' ', $this->struct['size'] );
		} else {
			$this->struct['size'][] = 1;
		}
		parent::setup();
	}

	/**
	 * Get Data from all controls of this section
	 *
	 * @since 1.0.0
	 * @see \uix\load
	 * @return array|null Array of sections data structured by the controls
	 */
	public function get_data() {

		$data = $this->get_child_data();
		if ( empty( $data ) ) {
			$data = null;
		}

		return $data;
	}

	/**
	 * Sets the data for all children
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_data( $data ) {

		foreach ( $this->child as $child ) {
			if ( method_exists( $child, 'set_data' ) ) {
				$child->set_data( $data );
			}
		}
		$this->data = $data;

	}


	/**
	 * Sets the wrappers attributes
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_attributes() {

		if ( ! empty( $this->struct['size'] ) ) {
			$this->attributes['class'][] = 'uix-grid';
			$this->attributes['class'][] = 'col-' . implode( '-', $this->struct['size'] );
		}

		parent::set_attributes();
	}

	/**
	 * Render the complete section
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string|null HTML of rendered notice
	 */
	public function render() {

		$output = '<div ' . $this->build_attributes() . '>';
		$output .= $this->render_children();
		$output .= '</div>';

		return $output;
	}

	/**
	 * Enqueues specific tabs assets for the active pages
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_assets() {

		$this->assets['style']['grid'] = $this->url . 'assets/css/grid' . UIX_ASSET_DEBUG . '.css';

		parent::set_assets();
	}

	/**
	 * Enqueues specific tabs assets for the active pages
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function set_active_styles() {

		if ( ! empty( $this->struct['size'] ) ) {
			$str = '';
			foreach ( $this->struct['size'] as $fr ) {
				$str .= $fr . 'fr ';
			}
			$style = '.uix-grid.col-' . implode( '-', $this->struct['size'] ) . '{grid-template-columns:' . $str . ';}';
			$style .= '@media screen and (max-width: 600px){';
			$style .= '.uix-grid.col-' . implode( '-', $this->struct['size'] ) . '{grid-template-columns:1fr;}';
			$style .= '}';
			uix_share()->set_active_styles( $style );
		}
	}

}
