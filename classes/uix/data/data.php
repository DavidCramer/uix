<?php

/**
 * Base data interface
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\data;

abstract class data extends \uix\ui\uix {

	/**
	 * object data
	 *
	 * @since 1.0.0
	 * @access private
	 * @var     array
	 */
	protected $data = array();

	/**
	 * Sets the objects sanitization filter
	 *
	 * @since 1.0.0
	 * @access public
	 * @see \uix\uix
	 */
	public function setup() {
		if ( ! empty( $this->struct['sanitize_callback'] ) ) {
			add_filter( 'uix_' . $this->slug . '_sanitize_' . $this->type, $this->struct['sanitize_callback'] );
		}

		parent::setup();
	}

	/**
	 * get the object's value
	 * @since 1.0.0
	 * @access public
	 * @return mixed $data
	 */
	public function get_value() {
		$data = $this->get_data();
		if ( isset( $data[ $this->slug ] ) ) {
			$data = $data[ $this->slug ];
		}

		return $data;
	}

	/**
	 * get the object's data
	 * @since 1.0.0
	 * @access public
	 * @return mixed $data
	 */
	public function get_data() {
		$data = array(
			$this->slug => null,
		);
		if ( isset( $this->data[ $this->id() ] ) ) {
			$data = $this->data[ $this->id() ];
		}

		return $data;
	}

	/**
	 * set the object's data
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $data the data to be set
	 */
	public function set_data( $data ) {

		if ( isset( $data[ $this->slug ] ) ) {
			$this->data[ $this->id() ][ $this->slug ] = apply_filters( 'uix_' . $this->slug . '_sanitize_' . $this->type, $data[ $this->slug ], $this );
		}

	}

}
