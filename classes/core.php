<?php
/**
 * IO.
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 David Cramer
 */
namespace uix;

/**
 * Main plugin class.
 *
 * @package uix
 * @author  David Cramer
 */
class core {

	/**
	 * The slug for this plugin
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'uix';

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object|\uix\core
	 */
	protected static $instance = null;

	/**
	 * Holds the option screen prefix
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load front style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_stylescripts' ) );

	}


	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object|\uix\core    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		//load settings class in admin
		if ( is_admin() ) {
			include_once UIX_PATH . 'classes/admin.php';
			new admin();
		}

		return self::$instance;

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, false, basename( UIX_PATH ) . '/languages');

	}

	/**
	 * Register and enqueue front-specific style sheet.
	 *
	 * @since 1.0.0
	 *
	 * @return    null
	 */
	public function enqueue_front_stylescripts() {

		// Front end scripts and styles

	}

}