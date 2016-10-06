<?php
/**
 * UIX Post Type
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * UIX Post Type class for adding custom post types
 * @package uix\ui
 * @author  David Cramer
 */
class post_type extends uix {

	/**
	 * The type of object
	 *
	 * @since 1.0.0
	 * @access public
	 * @var      string
	 */
	public $type = 'post';

	/**
	 * Define core UIX styling to identify UIX post types
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_assets() {
		$this->assets['style']['post'] = $this->url . 'assets/css/post' . UIX_ASSET_DEBUG . '.css';
		parent::set_assets();
	}

	/**
	 * Render (register) the post type
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {

		if ( ! empty( $this->struct['settings'] ) ) {
			register_post_type( $this->slug, $this->struct['settings'] );
		}

	}

	/**
	 * Determin which post types are active and set them active and render some styling
	 * Intended to be ovveridden
	 * @since 1.0.0
	 * @access public
	 */
	public function is_active() {

		if ( ! is_admin() ) {
			return false;
		}

		$screen = get_current_screen();

		return $screen->post_type === $this->slug;

	}

	/**
	 * setup actions and hooks to register post types
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function actions() {

		// run parent actions ( keep 'admin_head' hook )
		parent::actions();
		// add settings page
		add_action( 'init', array( $this, 'render' ) );

	}

	/**
	 * Render the custom header styles
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function enqueue_active_assets() {
		// output the styles
		echo '<style type="text/css">';
		echo '.contextual-help-tabs .active {border-left: 6px solid ' . $this->base_color() . ' !important;}';

		echo '#wpbody-content .wrap > h1 {box-shadow: 0 0 2px rgba(0, 2, 0, 0.1), 11px 0 0 ' . $this->base_color() . 'inset;}';

		echo '#wpbody-content .wrap > h1 a.page-title-action:hover { background: ' . $this->base_color() . ';';
		echo 'border-color: ' . $this->base_color() . ';}';

		echo '#wpbody-content .wrap > h1 a.page-title-action:focus {';
		echo 'box-shadow: 0 0 2px ' . $this->base_color() . ';';
		echo 'border-color: ' . $this->base_color() . ';}';
		echo '</style>';
	}

}
