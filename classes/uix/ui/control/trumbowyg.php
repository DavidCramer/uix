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
 * textarea / paragraph input
 *
 * @since 1.0.0
 */
class trumbowyg extends \uix\ui\control\textarea {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'trumbowyg';

	/**
	 * Gets the attributes for the control.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_attributes() {

		parent::set_attributes();
		$this->attributes['rows']  = '15';
		$this->attributes['class'] = 'trumbowyg';

		if ( ! empty( $this->struct['rows'] ) ) {
			$this->attributes['rows'] = $this->struct['rows'];
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

		$this->assets['style']['trumbowyg']       = $this->url . 'assets/controls/trumbowyg/ui/trumbowyg' . UIX_ASSET_DEBUG . '.css';
		$this->assets['script']['trumbowyg'] = array(
			'src'  => $this->url . 'assets/controls/trumbowyg/trumbowyg' . UIX_ASSET_DEBUG . '.js',
			'deps' => array( 'jquery' ),
		);
		$this->assets['script']['trumbowyg-init'] = array(
			'src'  => $this->url . 'assets/controls/trumbowyg/trumbowyg-init' . UIX_ASSET_DEBUG . '.js',
			'deps' => array( 'trumbowyg' ),
		);

		parent::set_assets();
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

		return '<textarea ' . $this->build_attributes() . '>' . esc_textarea( $this->get_value() ) . '</textarea>';
	}

}
