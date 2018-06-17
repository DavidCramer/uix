<?php
/**
 * UIX repeat
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

namespace uix\ui;

/**
 * A repetable container for repeatable areas.
 *
 * @since 1.0.0
 * @see   \uix\uix
 */
class repeat extends panel {

	/**
	 * The type of object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var      string
	 */
	public $type = 'repeat';

	/**
	 * The instance of this object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var      int|string
	 */
	public $instance = 0;

	/**
	 * total instances of this object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var      int|string
	 */
	public $instances = 0;

	/**
	 * The templates to render in the footer
	 *
	 * @since  1.0.0
	 * @access public
	 * @var      string
	 */
	public $templates = null;

	/**
	 * Button Label
	 *
	 * @since  1.0.0
	 * @access public
	 * @var      string
	 */
	public $button_label;

	/**
	 * Define core repeat styles ans scripts
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_assets() {

		$this->assets['script'][ $this->type ] = $this->url . 'assets/js/' . $this->type . UIX_ASSET_DEBUG . '.js';
		$this->assets['style'][ $this->type ]  = $this->url . 'assets/css/' . $this->type . UIX_ASSET_DEBUG . '.css';

		parent::set_assets();

	}

	/**
	 * Sets the data for all children
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_data( $data ) {

		$this->instance = 0;
		foreach ( (array) $data[ $this->slug ] as $instance => $instance_data ) {
			foreach ( $this->child as $child ) {
				if ( method_exists( $child, 'set_data' ) ) {
					$child->set_data( $instance_data );
				}
			}
			$this->instance ++;
		}
		$this->instances = $this->instance;
		$this->instance  = 0;

	}

	/**
	 * Render the complete section
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string|null HTML of rendered repeatable
	 */
	public function render() {

		add_action( 'admin_footer', [
			$this,
			'render_repeatable_script',
		] );
		add_action( 'wp_footer', [ $this, 'render_repeatable_script' ] );

		$output = '<div data-uix-template="' . esc_attr( $this->id() ) . '" ' . $this->build_attributes() . '>';
		$output .= $this->render_instances();
		$output .= '</div>';

		$output .= $this->render_repeatable_more();

		return $output;
	}

	/**
	 * Repeatable instance object id.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string The object ID
	 */
	public function id() {

		return parent::id() . '-' . $this->instance;
	}

	/**
	 * Render each instance from data
	 *
	 * @since  1.0.0
	 * @access private
	 * @return string|null HTML of rendered instances
	 */
	private function render_instances() {
		$data           = $this->get_data();
		$output         = null;
		$this->instance = 0;
		if ( ! empty( $data[ $this->slug ] ) ) {

			$data = array_filter( $data[ $this->slug ] );

			foreach ( (array) $data as $instance_id => $instance ) {
				$this->instance = $instance_id;
				$has_data       = array_filter( $instance );
				if ( empty( $has_data ) ) {
					continue;
				}
				if ( ! isset( $this->struct['active'] ) ) {
					$this->struct['active'] = 'true';
				}

				$output .= $this->render_repeatable();

			}
		}

		return $output;
	}

	/**
	 * Sets the data for all children
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function get_data() {

		$data           = [
			$this->slug => [],
		];
		$this->instance = 0;
		$has_data       = true;
		while ( true === $has_data ) {
			$this_data = $this->get_child_data();
			$this_data = array_filter( $this_data );
			if ( ! empty( $this_data ) ) {
				$data[ $this->slug ][] = $this_data;
			} else {
				$has_data = false;
			}
			$this->instance ++;
		}

		return $data;
	}

	/**
	 * Render the internal section
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param bool $reset Flag to indicate reset repeatbles.
	 *
	 * @return string|null HTML of rendered object
	 */
	public function render_repeatable( $reset = false ) {

		$output = '<div class="uix-repeat" >';
		$output .= $this->render_template();
		if ( ! empty( $this->child ) ) {
			$output .= $this->render_children( $reset );
		}

		$output .= '<button type="button" class="button button-small uix-remover"><span class="dashicons dashicons-no"></span></button> </div>';

		return $output;

	}

	/**
	 * Render the child objects
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param bool $reset Flag to reset the instances of child repeatables.
	 *
	 * @return string|null
	 */
	public function render_children( $reset = false ) {
		$output = null;
		foreach ( $this->child as $child ) {
			if ( true === $reset && 'repeat' === $child->type ) {
				$child->instances = 0;
				$child->instance  = 0;
			}
			if ( 'repeat' === $child->type && $reset === false ) {
				$id = 1;
			}
			$output .= $child->render();
		}

		return $output;
	}

	/**
	 * Render the add more button and template
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string|null HTML of rendered object
	 */
	public function render_repeatable_more() {

		$label = __( 'Add Another', 'uix' );

		if ( ! empty( $this->struct['label'] ) ) {
			$label = $this->struct['label'];
		}

		$this->instance  = '{{_inst_}}';
		$this->templates = $this->render_repeatable( true );
		$this->instance  = 0;
		$output          = '<div class="repeatable-footer"><button type="button" class="button" data-uix-repeat="' . esc_attr( $this->id() ) . '">' . esc_html( $label ) . '</button></div>';

		return $output;

	}

	/**
	 * Render the script footer template
	 *
	 * @since  1.0.0
	 * @see    \uix\ui\uix
	 * @access public
	 */
	public function render_repeatable_script() {
		$output = null;
		if ( ! empty( $this->templates ) ) {
			$output .= '<script type="text/html" id="' . esc_attr( $this->id() ) . '-tmpl">';
			$output .= $this->templates;
			$output .= '</script>';
		}

		echo $output;
	}

	/**
	 * Enqueues specific tabs assets for the active pages
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function set_active_styles() {

		parent::set_active_styles();
		$style = '#' . $this->id() . ' .uix-repeat{ box-shadow: 1px 0 0 ' . $this->base_color() . ' inset, -37px 0 0 #f5f5f5 inset, -38px 0 0 #ddd inset, 0 2px 3px rgba(0, 0, 0, 0.05); };';
		uix_share()->set_active_styles( $style );
	}

}
