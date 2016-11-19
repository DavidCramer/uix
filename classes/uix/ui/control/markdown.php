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
class markdown extends \uix\ui\control\textarea {

	/**
	 * The type of object
	 *
	 * @since       1.0.0
	 * @access public
	 * @var         string
	 */
	public $type = 'markdown';

	/**
	 * Gets the attributes for the control.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_attributes() {

		parent::set_attributes();
		$this->attributes['rows']  = '5';
		$this->attributes['class'] = 'markdown';

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
		$this->assets['style']['markdown']      = 'https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css';
		$this->assets['script']['markdown'] = 'https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js';
		$this->assets['script']['markdown-init'] = $this->url . 'assets/controls/markdown/markdown-init' . UIX_ASSET_DEBUG . '.js';

		parent::set_assets();
	}

}
