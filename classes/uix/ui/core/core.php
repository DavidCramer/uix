<?php

/**
 * Interface for WordPress core
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

namespace uix\ui\core;

class core {

	/**
	 * List of core object scripts ( common scripts )
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var      array
	 */
	protected $scripts = [];

	/**
	 * List of core object styles ( common styles )
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var      array
	 */
	protected $styles = [];


	/**
	 * setup actions and hooks - override to add specific hooks. use
	 * parent::actions() to keep admin head
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function actions() {

		// init uix after loaded.
		add_action( 'init', [ $this, 'init' ] );
		// set location.
		$location = 'wp_print_styles';
		if ( is_admin() ) {
			$location = 'admin_enqueue_scripts';
		}
		// init UIX headers.
		add_action( $location, [ $this, 'enqueue_core' ] );
	}


	/**
	 * Define core UIX styles - override to register core ( common styles for
	 * uix type )
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_assets() {
		if ( ! empty( $this->struct['style'] ) ) {
			$this->assets['style'] = array_merge( $this->assets['style'], $this->struct['style'] );
		}
		if ( ! empty( $this->struct['script'] ) ) {
			$this->assets['script'] = array_merge( $this->assets['script'], $this->struct['script'] );
		}
	}

	/**
	 * enqueue core assets
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_core() {

		// Register uix core asset.
		$this->core_assets();
		$this->enqueue_actions();
		// push assets to ui manager.
		uix()->set_assets( $this->assets );
		// done enqueuing - dpo inline or manual enqueue.
		$this->set_active_styles();
	}

	/**
	 * Do core enqueue actions.
	 *
	 * @since  3.0.0
	 * @access public
	 */
	private function enqueue_actions() {
		/**
		 * Do object initilisation.
		 *
		 * @param object current uix instance
		 */
		do_action( 'uix_admin_enqueue_scripts_' . $this->type, $this );

		/**
		 * Do object initilisation for specific slug.
		 *
		 * @param object current uix instance
		 */
		do_action( 'uix_admin_enqueue_scripts_' . $this->type . '_' . $this->slug, $this );
	}

	/**
	 * Register UIX depend js and css and call set assets
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function core_assets() {
		wp_register_script( 'uix', $this->url . 'assets/js/core' . UIX_ASSET_DEBUG . '.js' );
		wp_register_style( 'uix', $this->url . 'assets/css/core' . UIX_ASSET_DEBUG . '.css', [ 'dashicons' ] );
		wp_localize_script( 'uix', 'uixApi', [
			'root'  => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		] );

		// set assets . methods at before this point can set assets, after this not so much.
		$this->set_assets();
	}

	/**
	 * runs after assets have been enqueued
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function set_active_styles() {
	}

	/**
	 * Base color helper
	 *
	 * @since  1.0.0
	 * @access public
	 */
	protected function base_color() {
		$color = '#D84315';
		if ( empty( $this->struct['base_color'] ) ) {
			if ( ! empty( $this->parent ) ) {
				$color = $this->parent->base_color();
			}
		} else {
			$color = $this->struct['base_color'];
		}

		/**
		 * do object initilisation for specific slug
		 *
		 * @param object current uix instance
		 */
		return apply_filters( 'uix_base_color_' . $this->type . '_' . $this->slug, $color );

	}
}

