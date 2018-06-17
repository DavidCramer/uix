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
	 * Setup and prepare data.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function setup() {
		parent::setup();
	}
	/**
	 * All objects loaded - application method for finishing off loading objects
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {
		$this->prepare_data();
	}
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
	 * Prepares Data for extraction and saving
	 *
	 * @since  1.0.0
	 * @see    \uix\uix
	 * @access public
	 */
	public function prepare_data() {
		$submit_data = uix()->request_vars( 'post' );
		if ( ! empty( $submit_data ) ) {

			$parent = $this->parent;

			$id = $this->id();
			$instances = array_filter( array_keys( $submit_data ), [
				$this,
				'compare_var_key',
			] );

			$instances = array_map( [
				$this,
				'build_instance_count',
			], $instances );
			$instances = array_unique( $instances );
			$this->push_instance_setup( $instances );

		}
		$this->instance = 0; // reset instance;
	}

	/**
	 * Sets the data for all children
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_data( $data ) {

		$this->instance = 0;

		foreach ( (array) $data as $instance => $instance_data ) {
			// Check that the data is a repeat item.
			if ( is_int( $instance ) ) {
				foreach ( $this->child as $child ) {
					$child->set_data( $instance_data );
				}
				$this->instance ++;
			}

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

		$output = '<div data-instance="{{json this}}" data-uix-template="' . esc_attr( $this->id() ) . '" ' . $this->build_attributes() . '>';
		$output .= $this->render_instances();
		$output .= '</div>';

		$output .= $this->render_repeatable_more();

		return $output;
	}

	/**
	 * uix object id
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
		$data   = $this->get_data();
		$output = null;
		$data   = array_filter( $data );

		foreach ( (array) $data as $instance_id ) {
			if ( ! isset( $this->struct['active'] ) ) {
				$this->struct['active'] = 'true';
			}

			$output .= $this->render_repeatable();

			$this->instance ++;

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

		//if ( empty( $this->data ) ) {
			return $this->set_instance_data();
		//}

		return $this->data;

	}

	/**
	 * @return array
	 */
	public function set_instance_data() {
		$data           = [];
		$this->instance = 0;
		while ( $this->instance < $this->instances ) {

			if ( ! isset( $data[ $this->instance ] ) ) {
				$data[ $this->instance ] = [];
			}

			if ( null !== $this->get_instance_data() ) {
				$data[ $this->instance ] += $this->get_instance_data();
			}

			$this->instance ++;
		}
		$this->instance = 0;

		return $data;
	}

	/**
	 * @return array
	 */
	public function get_instance_data() {
		$data = [];
		foreach ( $this->child as $child ) {
			if ( method_exists( $child, 'get_data' ) ) {
				if ( null !== $child->get_data() ) {
					$data += $child->get_data();
				}
			}
		}

		return $data;
	}

	/**
	 * Render the internal section
	 *
	 * @since  1.0.0
	 * @access public
	 * @param bool $reset Flag to indicate reset repeatbles.
	 * @return string|null HTML of rendered object
	 */
	public function render_repeatable( $reset = false ) {

		$output = '<div class="uix-repeat">';
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
	 * @param bool $reset Flag to reset the instances of child repeatables.
	 * @return string|null
	 */
	public function render_children( $reset = false ) {
		$output = null;
		foreach ( $this->child as $child ) {
			if ( true === $reset && 'repeat' === $child->type ) {
				$child->instances = 0;
			}
			if( 'repeat' === $child->type && $reset === false ){
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

	/**
	 * Compares the key of submitted fields to match instances
	 *
	 * @since  1.0.0
	 * @see    \uix\uix
	 * @access private
	 *
	 * @param string $key Key to compare
	 *
	 * @return bool
	 */
	private function compare_var_key( $key ) {
		$id_parts = $this->id_base_parts();
		$compare  = implode( '-', $id_parts ) . '-';
		$current  = substr( $key, 0, strlen( $compare ) );
		$this_id = $this->id();
		return $current == $compare;
	}

	/**
	 * Breaks apart the ID to get the base parts without the instance number
	 *
	 * @access private
	 * @return array
	 */
	private function id_base_parts() {
		$id_parts = explode( '-', $this->id() );
		array_pop( $id_parts );

		return $id_parts;
	}

	/**
	 * Pushes the children to initilize setup in order to capture the instance
	 * data
	 *
	 * @access private
	 *
	 * @param $instances
	 */
	private function push_instance_setup( $instances ) {

		$this->instances = count( $instances );
		$this->instance = 0;
		while ( $this->instance < $this->instances ) {
			if ( ! isset( $this->data[ $this->instance ] ) ) {
				$this->data[ $this->instance ] = [];
			}
			$id = $this->id();
			foreach ( $this->child as $child ) {
				$child->setup();
				$this->data[ $this->instance ] += $child->get_data();
			}
			$this->instance++;
		}
	}

	/**
	 * Removes the instance number from the submission key
	 *
	 * @access private
	 *
	 * @param $key
	 *
	 * @return int
	 */
	private function build_instance_count( $key ) {
		$key_parts = explode( '-', $key );
		$id_parts  = $this->id_base_parts();

		return (int) $key_parts[ count( $id_parts ) ];
	}


}
