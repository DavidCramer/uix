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
 * Template file include. for including custom control html/php
 *
 * @since 1.0.0
 */
class handlebars extends \uix\ui\control {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'handlebars';

	/**
	 * set the object's data
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $data the data to be set
	 */
	public function set_data( $data ) {
		if ( isset( $data[ $this->slug ] ) ) {
			if ( is_string( $data[ $this->slug ] ) ) {
				$is_json = json_decode( $data[ $this->slug ], ARRAY_A );
				if ( ! empty( $is_json ) ) {
					$data[ $this->slug ] = $is_json;
				} else {
					$data[ $this->slug ] = str_replace( '{{@root', '{{json @root', $data[ $this->slug ] );
				}
			}
			$this->data[ $this->id() ][ $this->slug ] = apply_filters( 'uix_' . $this->slug . '_sanitize_' . $this->type, $data[ $this->slug ], $this );
		}

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

		$output = '<div class="uix-tab-canvas" data-app="' . esc_attr( $this->name() ) . '" ' . esc_attr( $this->build_attributes() ) . '></div>';
		$output .= '<input type="hidden" name="' . esc_attr( $this->name() ) . '" value="' . esc_attr( $this->get_value() ) . '" data-data="' . esc_attr( $this->get_value() ) . '">';
		add_action( 'admin_footer', array( $this, 'input' ) );
		add_action( 'wp_footer', array( $this, 'input' ) );

		return $output;
	}

	/**
	 * get this controls value
	 *
	 * @since 1.0.0
	 * @access public
	 * @return mixed the controls value
	 */
	public function get_value() {
		$value  = parent::get_value();

		if ( is_array( $value ) ) {
			$value = json_encode( $value );
		}

		return $value;
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
		$output = '<script type="text/html" data-template="' . esc_attr( $this->name() ) . '">';
		if ( ! empty( $this->struct['template'] ) && file_exists( $this->struct['template'] ) ) {
			ob_start();
			include $this->struct['template'];
			$output .= ob_get_clean();
		}
		$output .= '</script>';
		echo $output;

		return $output;
	}

	/**
	 * register scritps and styles
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_assets() {

		// Initilize core styles
		$this->assets['script']['baldrick']            = array(
			'src'  => $this->url . 'assets/js/jquery.baldrick' . UIX_ASSET_DEBUG . '.js',
			'deps' => array( 'jquery' ),
		);
		$this->assets['script']['handlebars']          = array(
			'src' => $this->url . 'assets/js/handlebars-latest' . UIX_ASSET_DEBUG . '.js',
		);
		$this->assets['script']['handlebars-control']  = array(
			'src'  => $this->url . 'assets/controls/handlebars/handlebars-control' . UIX_ASSET_DEBUG . '.js',
			'deps' => array( 'baldrick' ),
		);
		$this->assets['script']['baldrick-handlebars'] = array(
			'src'  => $this->url . 'assets/js/handlebars.baldrick' . UIX_ASSET_DEBUG . '.js',
			'deps' => array( 'baldrick' ),
		);
		parent::set_assets();
	}
}
