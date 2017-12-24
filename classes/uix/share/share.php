<?php
/**
 * UIX shared Core
 *
 * @package   share
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

namespace uix\share;

/**
 * Outputs shared resources for UIX objects.
 *
 * @package uix
 * @author  David Cramer
 */
class share {

	/**
	 * Holds the shared instance
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var      share
	 */
	protected static $instance = null;

	/**
	 * Active style assets for rendering in header
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var      string
	 */
	protected $active_styles;

	/**
	 * Runs after assets have been enqueued.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $styles Styles to register for render.
	 */
	public function set_active_styles( $styles ) {
		$this->active_styles .= $styles;
	}

	/**
	 * Runs after active assets have been set.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	public function render_active_styles() {

		if ( ! empty( $this->active_styles ) ) {
			echo '<style type="text/css" media="screen" id="uix-share-styles">';
			echo $this->active_styles; // WPCS: XSS ok, sanitation ok.
			echo '</style>';
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @codeCoverageIgnore
	 * @since 1.0.0
	 * @return share A single instance of the share class
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Constructor. Sets up action to render styles
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [
			$this,
			'render_active_styles',
		], 100 );
		add_action( 'wp_print_styles', [ $this, 'render_active_styles' ], 100 );
	}
}