<?php
/**
 * UIX Panel
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * UIX panel. a holder to contain sections. a panel with multiple sections creates a tabbed interface to switch between sections areas.
 *
 * @package uix\ui
 * @author  David Cramer
 */
class panel extends \uix\data\data {

	/**
	 * The type of object
	 *
	 * @since 1.0.0
	 * @access public
	 * @var      string
	 */
	public $type = 'panel';

	/**
	 * Define core panel styles ans scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_assets() {

		$this->assets['script']['panel'] = $this->url . 'assets/js/panel' . UIX_ASSET_DEBUG . '.js';
		$this->assets['style']['panel']  = $this->url . 'assets/css/panel' . UIX_ASSET_DEBUG . '.css';

		parent::set_assets();

	}

	/**
	 * Get Data from all controls of this section
	 *
	 * @since 1.0.0
	 * @see \uix\load
	 * @return array|null Array of sections data structured by the controls or null if none.
	 */
	public function get_data() {

		if ( empty( $this->data ) ) {
			$this->data = array(
				$this->slug => $this->get_child_data(),
			);
		}

		return $this->data;
	}

	/**
	 * Sets the data for all children
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array data of the child objects.
	 */
	protected function get_child_data() {

		$data = array();
		foreach ( $this->child as $child ) {
			if ( null !== $child->get_data() ) {
				$data += $child->get_data();
			}
		}
		if ( ! empty( $this->struct['data'] ) ) {
			$data = array_merge( $this->struct['data'], $data );
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

		if ( ! empty( $data[ $this->slug ] ) ) {
			foreach ( $this->child as $child ) {
				if ( method_exists( $child, 'set_data' ) ) {
					$child->set_data( $data[ $this->slug ] );
				}
			}
			$this->data = $data[ $this->slug ];
		}

	}

	/**
	 * Render the panel
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {
		$output = null;

		if ( $this->child_count() > 0 ) {

			$output .= '<div id="panel-' . esc_attr( $this->id() ) . '" class="uix-' . esc_attr( $this->type ) . '-inside ' . esc_attr( $this->wrapper_class_names() ) . '">';
			// render a lable
			$output .= $this->label();
			// render a desciption
			$output .= $this->description();
			// render navigation tabs
			$output .= $this->navigation();
			// sections
			$output .= $this->panel_section();

			$output .= '</div>';
		}

		$output .= $this->render_template();

		return $output;
	}

	/**
	 * Determines the number of useable children for tab display
	 *
	 * @since 1.0.0
	 * @access public
	 * @return int Number of tabable children
	 */
	public function child_count() {

		$count = 0;
		if ( ! empty( $this->child ) ) {
			foreach ( $this->child as $child ) {
				if ( $this->is_section_object( $child ) ) {
					$count ++;
				}
			}
		}

		return $count;
	}

	/**
	 * Check if child is a section object
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param uix Object to test if it is to be rendered in a section
	 *
	 * @return string|null HTML of rendered description
	 */
	public function is_section_object( $section ) {
		return ! in_array( $section->type, array( 'help', 'header', 'footer' ) );
	}

	/**
	 * Returns the class names for the tab wrapper
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wrapper_class_names() {

		$wrapper_class_names = array(
			'uix-panel-inside',
		);

		if ( $this->child_count() > 1 ) {
			$wrapper_class_names[] = 'uix-has-tabs';

			if ( ! empty( $this->struct['top_tabs'] ) ) {
				$wrapper_class_names[] = 'uix-top-tabs';
			}
		}

		return implode( ' ', $wrapper_class_names );
	}

	/**
	 * Render the panels label
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string|null rendered html of label
	 */
	public function label() {
		$output = null;
		if ( ! empty( $this->struct['label'] ) ) {
			$output .= '<div class="uix-' . esc_attr( $this->type ) . '-heading"><h3 class="uix-' . esc_attr( $this->type ) . '-title">' . esc_html( $this->struct['label'] ) . '</h3></div>';
		}

		return $output;
	}

	/**
	 * Render the panels Description
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string|null HTML of rendered description
	 */
	public function description() {
		$output = null;
		if ( ! empty( $this->struct['description'] ) ) {
			$output .= '<div class="uix-' . esc_attr( $this->type ) . '-heading"><p class="uix-' . esc_attr( $this->type ) . '-subtitle description">' . esc_html( $this->struct['description'] ) . '</p></div>';
		}

		return $output;
	}

	/**
	 * Render the panels navigation tabs
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string|null Html of rendered navigation tabs
	 */
	public function navigation() {
		$output = null;

		if ( $this->child_count() > 1 ) {

			$output .= '<ul class="uix-' . esc_attr( $this->type ) . '-tabs uix-panel-tabs">';
			$active = 'true';
			foreach ( $this->child as $child ) {
				if ( $this->is_section_object( $child ) ) {
					$output .= $this->tab_label( $child, $active );
					$active = 'false';
				}
			}
			$output .= '</ul>';
		}

		return $output;
	}

	/**
	 * Render the tabs label
	 *
	 * @since 1.0.0
	 *
	 * @param object $child Child object to render tab for.
	 * @param string $active Set the tabactive or not.
	 *
	 * @access private
	 * @return string|null html of rendered label
	 */
	private function tab_label( $child, $active ) {

		$output = null;

		$label = esc_html( $child->struct['label'] );

		if ( ! empty( $child->struct['icon'] ) ) {
			$label = '<i class="dashicons ' . $child->struct['icon'] . '"></i><span class="label">' . esc_html( $child->struct['label'] ) . '</span>';
		}

		$output .= '<li aria-selected="' . esc_attr( $active ) . '">';
		$output .= '<a href="#' . esc_attr( $child->id() ) . '" data-parent="' . esc_attr( $this->id() ) . '" class="uix-tab-trigger">' . $label . '</a>';
		$output .= '</li>';

		return $output;
	}

	/**
	 * Render the panels Description
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string|null HTML of rendered description
	 */
	public function panel_section() {
		$output = null;

		// render the section wrapper
		$output .= '<div class="uix-' . esc_attr( $this->type ) . '-sections uix-sections">';

		$hidden = 'false';
		foreach ( $this->child as $section ) {

			if ( ! $this->is_section_object( $section ) ) {
				continue;
			}

			$section->struct['active'] = $hidden;
			$output .= $section->render();
			$hidden = 'true';
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Render a template based object
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string|null HTML of rendered template
	 */
	public function render_template() {
		// template
		$_output = null;

		if ( ! empty( $this->struct['template'] ) ) {
			ob_start();
			include $this->struct['template'];
			$_output .= ob_get_clean();
		}

		return $_output;
	}

	/**
	 * Enqueues specific tabs assets for the active pages
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function set_active_styles() {
		$style = '#panel-' . $this->id() . ' > .uix-panel-tabs > li[aria-selected="true"] a {box-shadow: 3px 0 0 ' . $this->base_color() . ' inset;}';
		$style .= '#panel-' . $this->id() . '.uix-top-tabs > .uix-panel-tabs > li[aria-selected="true"] a {	box-shadow: 0 3px 0 ' . $this->base_color() . ' inset;}';
		uix_share()->set_active_styles( $style );
	}
}
