<?php
/**
 * UIX Core
 *
 * @package   ui
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

namespace uix\ui;

/**
 * Core UIX abstract class.
 *
 * @package uix\ui
 * @author  David Cramer
 */
abstract class uix extends core\core {

	/**
	 * The type of UI object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var      string
	 */
	public $type = 'uix';

	/**
	 * Config Structure of object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var      array
	 */
	public $struct = [];

	/**
	 * Object slug
	 *
	 * @access public
	 * @since  1.0.0
	 * @var      string
	 */
	public $slug;

	/**
	 * Array of child objects
	 *
	 * @since  1.0.0
	 * @access public
	 * @var      array
	 */
	public $child = [];

	/**
	 * Objects parent
	 *
	 * @since  1.0.0
	 * @access public
	 * @var      object/uix
	 */
	public $parent;

	/**
	 * List of attributes to apply to the wrapper element
	 *
	 * @since  1.0.0
	 * @access public
	 * @var array
	 */
	public $attributes = [
		'class' => [],
	];

	/**
	 * Base URL of this class
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var      string
	 */
	protected $url;

	/**
	 * UIX constructor
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param string $slug Object slug.
	 * @param array  $object Objects structure array.
	 * @param uix    $parent Parent UIX Object.
	 */
	protected function __construct( $slug, $object, $parent = null ) {

		// set the slug.
		$this->slug = $slug;
		// set the object.
		$this->struct = $object;
		// set parent if given.
		$this->parent = $parent;
		// Set the root URL for this plugin.
		$this->set_url();
		// do setup.
		$this->setup();
		// Set required assets.
		$this->set_assets();
		// start internal actions to allow for automating post init.
		$this->actions();

	}

	/**
	 * Detects the root of the plugin folder and sets the URL
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_url() {

		$plugins_url = plugins_url();
		$this_url    = trim( substr( trailingslashit( plugin_dir_url( __FILE__ ) ), strlen( $plugins_url ) ), '/' );

		if ( false !== strpos( $this_url, '/' ) ) {
			$url_path = explode( '/', $this_url );
			// generic 3 path depth: classes/namespace/ui|data.
			array_splice( $url_path, count( $url_path ) - 3 );
			$this_url = implode( '/', $url_path );
		}
		// setup the base URL.
		$this->url = trailingslashit( $plugins_url . '/' . $this_url );
	}

	/**
	 * Autoload Children - Checks structure for nested structures
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function setup() {

		foreach ( $this->struct as $struct_key => $sub_struct ) {
			if ( is_array( $sub_struct ) && uix()->get_register_callback( $struct_key ) ) {
				$this->process_child( $struct_key );
			}
		}
	}

	/**
	 * Process type key child
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $type The type of child object.
	 */
	public function process_child( $type ) {

		if ( isset( $this->struct[ $type ]['id'] ) ) {
			$this->{$type}( $this->struct[ $type ]['id'], $this->struct[ $type ] );
		} else {
			$this->process_children( $type );
		}

	}

	/**
	 * Process all children under type key
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function process_children( $type ) {
		$this->struct[ $type ] = array_filter( $this->struct[ $type ], 'is_array' );
		foreach ( $this->struct[ $type ] as $sub_slug => $sub_structure ) {
			if ( ! empty( $sub_structure['id'] ) ) {
				$sub_slug = $sub_structure['id'];
			}

			$this->{$type}( $sub_slug, $sub_structure );
		}

	}

	/**
	 * Register the UIX objects
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $slug Object slug
	 * @param array  $object object structure array
	 *
	 * @return object|\uix object instance
	 */
	public static function register( $slug, $object, $parent = null ) {
		// get the current instance
		$caller = get_called_class();

		return new $caller( $slug, $object, $parent );
	}

	/**
	 * All objects loaded - application method for finishing off loading objects
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {
	}

	/**
	 * Magic caller for adding child objects
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $type Type of object to attempt to create
	 * @param array  $args arguments for the caller
	 *
	 * @return UIX|null
	 */
	public function __call( $type, $args ) {
		$init  = uix()->get_register_callback( $type );
		$child = null;
		if ( null !== $init ) {
			$this->sanitize_slug( $args[0] );
			$args[] = $this;
			$child  = call_user_func_array( $init, $args );
			if ( null !== $child ) {
				$this->child[ $args[0] ] = $child;
			}
		}

		return $child;
	}

	/**
	 * Create a slug for the object
	 *
	 * @since  1.0.0
	 *
	 * @param string $slug The slug to be checked and created
	 *
	 * @access private
	 */
	private function sanitize_slug( &$slug ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug ) {
			$slug = count( $this->child );
		}
	}

	/**
	 * Determin if a UIX object should be active for this screen
	 * Intended to be ovveridden
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function is_active() {
		if ( ! empty( $this->parent ) ) {
			return $this->parent->is_active();
		}

		return true; // base is_active will result in true.
	}

	/**
	 * Build Attributes for the input control
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string Attributes string for applying to an element
	 */
	public function build_attributes() {
		// setup attributes.
		$this->set_attributes();

		$attributes = [];
		foreach ( $this->attributes as $att => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( ' ', $value );
			}
			$attributes[] = sprintf( '%s="%s"', esc_html( $att ), esc_attr( $value ) );
		}

		return implode( ' ', $attributes );
	}

	/**
	 * Sets the wrappers attributes
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function set_attributes() {

		if ( empty( $this->attributes['id'] ) ) {
			$this->attributes['id'] = $this->id();
		}

		if ( ! empty( $this->struct['attributes'] ) ) {
			$this->attributes = array_merge( $this->attributes, $this->struct['attributes'] );
		}

	}

	/**
	 * uix object id
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string The object ID
	 */
	public function id() {
		$id = $this->slug;
		if ( ! empty( $this->parent ) ) {
			$id = $this->parent->id() . '-' . $this->slug;
		}

		return $id;
	}

	/**
	 * Render the UIX object
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string HTML of rendered object
	 */
	abstract public function render();

	/**
	 * Render the child objects
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string|null
	 */
	public function render_children() {
		$output = null;
		foreach ( $this->child as $child ) {
			$output .= $child->render();
		}

		return $output;
	}
}
