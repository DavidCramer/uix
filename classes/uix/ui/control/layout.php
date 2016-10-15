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

use uix\ui\modal;

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
	 * The component modals
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         array
	 */
	public $modals = array();

	/**
	 * Autoload Children - Checks structure for nested structures
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function setup() {

		// create components
		if ( ! empty( $this->struct['component'] ) ) {
			$this->register_components();
		}

		foreach ( $this->modals as $modal ) {

			if ( $modal->is_submitted() ) {
				wp_send_json_success( $modal->get_value() );
			}
		}

		parent::setup();
	}


	/**
	 * register components for layout
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_components() {

		$components = array();
		foreach ( $this->struct['component'] as $component_id => $component_struct ) {

			if ( ! empty( $component_struct['id'] ) ) {
				$component_id = $component_struct['id'];
			}

			$component_struct += array(
				'attributes' => array(
					'data-master' => true,
					'style'       => 'margin:6px 0 0 6px;',
				),
				'footer'     => array(
					'id'      => $component_id . '_foot',
					'control' => array(
						'set_component' => array(
							'label'      => 'Send to Layout',
							'type'       => 'button',
							'attributes' => array(
								'type' => 'submit',
							),
						),
					),
				),
			);

			if ( ! empty( $component_struct['setup'] ) ) {
				$component_struct['section'] = $component_struct['setup'];
				unset( $component_struct['setup'] );
			}

			$this->modals[] = $this->modal( $component_id, $component_struct );
		}

	}

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
		$this->assets['script']['layout']              = array(
			'src'       => $this->url . 'assets/controls/layout/js/layout' . UIX_ASSET_DEBUG . '.js',
			'deps'      => array(
				'jquery-ui-draggable',
				'jquery-ui-droppable',
				'jquery-ui-sortable',
			),
			'in_footer' => true,
		);
		$this->assets['script']['handlebars']          = array(
			'src' => $this->url . 'assets/js/handlebars-latest' . UIX_ASSET_DEBUG . '.js',
		);
		$this->assets['script']['baldrick-handlebars'] = array(
			'src'  => $this->url . 'assets/js/handlebars.baldrick' . UIX_ASSET_DEBUG . '.js',
			'deps' => array( 'baldrick' ),
		);

		// modals
		$this->assets['script']['modals'] = array(
			'src'  => $this->url . 'assets/js/modals' . UIX_ASSET_DEBUG . '.js',
			'deps' => array( 'baldrick' ),
		);
		$this->assets['style']['modals']  = $this->url . 'assets/css/modals' . UIX_ASSET_DEBUG . '.css';


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

		$output = '<div id="' . esc_attr( $this->id() ) . '" data-color="' . esc_attr( $this->base_color() ) . '" data-for="' . esc_attr( $this->id() ) . '-control" class="uix-control uix-control-' . esc_attr( $this->type ) . ' ' . esc_attr( $this->id() ) . '"></div>';
		$output .= $this->label();
		$output .= $this->input();

		$output .= $this->component_templates();

		$output .= $this->modal_template();

		return $output;
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
	 * Render component templates
	 *
	 * @since 1.0.0
	 * @see \uix\ui\uix
	 * @access public
	 * @return string HTML of templates for components
	 */
	public function component_templates() {
		$output = null;
		foreach ( $this->modals as $modal ) {

			$width  = 'data-width="950"';
			$height = 'data-height="550"';

			$output .= '<script type="text/html" ' . $width . ' ' . $height . ' id="' . esc_attr( $this->id() ) . '-' . esc_attr( $modal->slug ) . '">';
			if ( isset( $modal->struct['preview'] ) ) {
				$template = uix()->add( 'control', $modal->slug, array(
					'type'     => 'template',
					'template' => $modal->struct['preview'],
				) );

				$output .= $template->render();
			} else {
				$output .= '<div>' . $modal->struct['label'] . '</div>';
			}
			$output .= '</script>';

		}

		return $output;
	}

	/**
	 * Render the modal template
	 *
	 * @since 1.0.0
	 * @see \uix\ui\uix
	 * @access public
	 * @return string HTML of modals templates
	 */
	public function modal_template() {

		$output = '<script type="text/html" id="' . esc_attr( $this->id() ) . '-components">';
		foreach ( $this->modals as $modal ) {

			$label         = $modal->struct['label'];
			$data          = $modal->get_data();
			$data_template = $this->drill_in( $data[ $modal->slug ], '{{@root' );
			$modal->set_data( array( $modal->slug => $data_template ) );

			$modal->render();

			$setup = null;
			if ( count( $modal->child ) > 1 ) {
				$setup = ' data-setup="true" ';
			}

			$output .= '<button type="button" class="button uix-component-trigger" style="margin:12px 0 0 12px;" data-label="' . esc_attr( $modal->attributes['data-title'] ) . '" data-type="' . $modal->slug . '" ' . $setup . ' data-id="' . esc_attr( $modal->id() ) . '">' . $label . '</button> ';

		}
		$output .= '</script>';

		return $output;
	}

	/**
	 * builds the handlebars based structure for template render
	 *
	 * @param array $array the dat astructure to drill into
	 * @param string $tag, the final tag to replace the data with.
	 * @since 1.0.0
	 * @access public
	 * @return array array of the data structure
	 */
	public function drill_in( $array, $tag = null ) {
		$back = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) && ! empty( $value ) ) {
				$back[ $key ] = $this->drill_in( $value, $tag . '.' . $key );
			} else {
				$back[ $key ] = $tag . '.' . $key . '}}';
			}
		}

		return $back;
	}

	/**
	 * Sets styling colors
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function set_active_styles() {

		$style = '.' . $this->id() . ' .dashicons.dashicons-plus-alt{ color: ' . $this->base_color() . ' !important;}';
		$style .= '.' . $this->id() . ' .column-handle{background-color: ' . $this->base_color() . ' !important;}';
		$style .= '.' . $this->id() . ' .uix-component-toolbar{background-color: ' . $this->base_color() . ' !important;}';

		uix_share()->set_active_styles( $style );
	}
}
