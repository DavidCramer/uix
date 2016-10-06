<?php
/**
 * UIX Help
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui;

/**
 * UIX Help. Handles displaying of contextual help items in admin
 *
 * @package uix\ui
 * @author  David Cramer
 */
class help extends uix {

	/**
	 * The type of object
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $type = 'help';

	/**
	 * The current screen
	 *
	 * @since 1.0.0
	 * @access private
	 * @var screen
	 */
	private $screen;

	/**
	 * Add defined contextual help to current screen
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {

		$this->screen = get_current_screen();

		if ( ! $this->is_active() ) {
			return;
		}

		$this->screen->add_help_tab( array(
			'id'      => $this->slug,
			'title'   => $this->struct['title'],
			'content' => $this->struct['content'],
		) );

	}

	/**
	 * Determin if a help is on this page
	 * @since 1.0.0
	 * @access public
	 */
	public function is_active() {

		if ( ! empty( $this->struct['screen'] ) && ! in_array( $this->screen->id, (array) $this->struct['screen'] ) ) {
			return false;
		}

		return parent::is_active();
	}

	/**
	 * Set hooks on when to load the notices
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function actions() {
		parent::actions();
		// init uix after loaded
		// queue helps
		add_action( 'admin_head', array( $this, 'render' ) );
	}
}
