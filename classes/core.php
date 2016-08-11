<?php
/**
 * UIX Core
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2;

/**
 * Settings class
 * @package uix
 * @author  David Cramer
 */
abstract class core{

	/**
	 * The type of object
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	protected $type = 'uix';

	/**
	 * List of registered objects
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	protected $objects = array();

	/**
	 * Active objects
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	protected $active = array();

	/**
	 * Data to be localized
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	protected $data = array();

	/**
	 * Base URL of this class
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	protected $url;

	/**
	 * List of core scripts
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	protected $scripts = array();

	/**
	 * List of core styles
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	protected $styles = array();

	/**
	 * prefix for min scripts
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	protected $debug_scripts = null;

	/**
	 * prefix for min styles
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	protected $debug_styles = null;	

	/**
	 * UIX constructor - override this to remove the core UIX styles and scripts
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct() {

		// init UIX 
		$this->actions();

		// set up globals vars
		$this->set_url();

		// detect debug scritps and styles		
		if( !defined( 'DEBUG_SCRIPTS' ) ){
			$this->debug_scripts = '.min';
		}
		// detect debug scritps and styles		
		if( !defined( 'DEBUG_STYLES' ) ){
			$this->debug_styles = '.min';
		}

		// Initilize core styles
		$core_styles = array(
			'icons'		=>	$this->url . 'assets/css/icons' . $this->debug_styles . '.css',
			'styles'	=>	$this->url . 'assets/css/admin' . $this->debug_styles . '.css',
			'grid'		=>	$this->url . 'assets/css/grid' . $this->debug_styles . '.css',
			'controls'		=>	$this->url . 'assets/css/controls' . $this->debug_styles . '.css',
		);
		$this->styles( $core_styles );

		// Initilize core scripts
		$core_scripts = array(
			'handlebars'	=>	$this->url . 'assets/js/handlebars.min-latest.js',
			'helpers'		=>	array(
				'src'			=>	$this->url . 'assets/js/uix-helpers' . $this->debug_scripts . '.js',
				'depends'		=>	array(
					'jquery'
				)
			),
			'admin'			=>	array(
				'src'			=>	$this->url . 'assets/js/uix-core' . $this->debug_scripts . '.js',
				'depends'		=>	array(
					'jquery',
					'handlebars'
				)				
			),
			'modals'			=>	array(
				'src'			=>	$this->url . 'assets/js/uix-modals' . $this->debug_scripts . '.js',
				'depends'		=>	array(
					'jquery'
				)				
			)
		);
		$this->scripts( $core_scripts );


		// init UIX headers
		add_action( 'admin_head', array( $this, 'head' ) );

	}

	/**
	 * setup actions and hooks - ovveride to add specific hooks 
	 *
	 * @since 1.0.0
	 *
	 */
	protected function actions() {}

	/**
	 * Register the core UIX scripts
	 *
	 * @since 1.0.0
	 *
	 */
	public function scripts( array $scripts ) {

		$this->scripts = array_merge( $this->scripts, $scripts );

	}

	/**
	 * Register the core UIX styles
	 *
	 * @since 1.0.0
	 *
	 */
	public function styles( array $styles ) {
		
		$this->styles = array_merge( $this->styles, $styles );

	}

	/**
	 * Register the UIX objects
	 *
	 * @since 1.0.0
	 *
	 * @param array $objects object structure array
	 */
	public function register( array $objects ) {

		/**
		 * Filter objects to be created
		 *
		 * @param array $objects array of UIX object structures to be registered
		 */
		$this->objects = apply_filters( 'uix_register_objects-' . $this->type, $objects );

		
	}

	/**
	 * load UIX for the current page
	 *
	 * @since 1.0.0
	 *
	 */
	public function head() {

		// attempt to get a config
		$slugs = $this->locate();

		if( empty( $slugs ) ){
			return;
		}

		// set active
		$this->active = (array) $slugs;

		// set enqueue prefix
		$prefix = $this->type;

		// enqueue core scripts and styles
		$assets = array(
			'scripts' => $this->scripts,
			'styles' => $this->styles,
		);
		// enqueue core scripts and styles
		$this->enqueue( $assets, $prefix );

		// localize data for this screen
		$this->localize_data();

	}

	/**
	 * sets the URL scope
	 *
	 * @since 1.0.0
	 *
	 */
	protected function set_url(){

		// setup the base URL
		$this->url = plugin_dir_url( dirname( __FILE__ ) );

	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 1.0.0
	 *
	 * @return    null
	 */
	public function enqueue_object( $slug ) {

		$uix = $this->get( $slug );

		// enqueue UIX structure specific scripts & styles
		$this->enqueue( $uix, $this->type . '-' . $slug );

	}

	/**
	 * enqueue a set of styles and scripts
	 *
	 * @since 0.0.1
	 *
	 */
	protected function enqueue( $set, $prefix ){
		// go over the set to see if it has styles or scripts

		// setup default args for array type includes
		$arguments_array = array(
			"src"		=> false,
			"deps"		=> array(),
			"ver"		=> false,
			"in_footer"	=> false,
			"media"		=> false
		);

		// enqueue set specific runtime styles
		if( !empty( $set[ 'styles'] ) ){
			foreach( $set[ 'styles'] as $style_key => $style ){
				if( is_int( $style_key ) ){
					wp_enqueue_style( $style );
				}else{
					if( is_array( $style ) ){
						$args = array_merge( $arguments_array, $style );
						wp_enqueue_style( $prefix . '-' . $script_key, $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );
					}else{
						wp_enqueue_style( $prefix . '-' . $style_key, $style );
					}
				}
			}
		}
		// enqueue set specific runtime scripts
		if( !empty( $set[ 'scripts'] ) ){
			foreach( $set[ 'scripts'] as $script_key => $script ){
				if( is_int( $script_key ) ){
					wp_enqueue_script( $script );
				}else{
					if( is_array( $script ) ){
						$args = array_merge( $arguments_array, $script );
						wp_enqueue_script( $prefix . '-' . $script_key, $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );
					}else{
						wp_enqueue_script( $prefix . '-' . $script_key, $script );
					}
				}
			}
		}

	}	

	/**
	 * localize settings for this screen
	 *
	 * @since 1.0.0
	 */
	protected function localize_data(){		
		// build data
		foreach( (array) $this->active as $slug ){
			// enqueue stlyes and scripts
			$this->enqueue_object( $slug );

			$this->data[ $slug ] = array(
				'data' 		=> $this->get_data( $slug ),
				'structure'	=> $this->get( $slug )
			);
		}
		
		wp_localize_script( $this->type . '-admin', 'UIX', $this->data );
	
	}

	/**
	 * Save a UIX config
	 * @since 1.0.0
	 *
	 * @return bool true on successful save
	 */
	public function save_data( $slug, $config ){
		
		$uix = $this->get( $slug );

		/**
		 * Filter config object
		 *
		 * @param array $config the config array to save
		 * @param array $uix the uix config to be saved for
		 */
		$config = apply_filters( 'uix_get_save_config_' . $this->type, $config, $uix );

		$success = __( 'Settings saved.' );
		if( !empty( $uix['saved_message'] ) ){
			$success = $uix['saved_message'];
		}
		$option_name = $this->option_name( $slug );

		// save object
		return update_option( $option_name, $config );
		
	}

	/**
	 * Loads a UIX config
	 * @since 1.0.0
	 *
	 * @return mixed $data the saved data fro the specific UIX object
	 */
	public function get_data( $slug ){

		$uix = $this->get( $slug );

		// get config object
		$config_object = get_option( $this->option_name( $slug ), array() );


		/**
		 * Filter config object
		 *
		 * @param array $config_object The object as retrieved from data
		 * @param array $uix the UIX structure
		 * @param array $slug the UIX object slug
		 */
		return apply_filters( 'uix_data-' . $this->type, $config_object, $uix, $slug );		

	}

	/**
	 * get a UIX config option name
	 * @since 1.0.0
	 *
	 * @return string $option_name the defiuned option name for this UIX object
	 */
	public function option_name( $slug ){
		
		$uix = $this->get( $slug );
		$option_name = 'uix-' . $this->type . '-' . sanitize_text_field( $slug );
		if( !empty( $uix['option_name'] ) ){
			$option_name = $uix['option_name'];
		}

		return $option_name;
	}

	/**
	 * Determin if a UIX object should be loaded for this screen
	 * Intended to be ovveridden
	 * @since 0.0.1
	 *
	 * @return array $array of slugs of a registered structures relating to this screen
	 */
	protected function locate(){

		$slugs = array();
		
		// default is to use a GET['uix']
		if( isset( $_GET['uix'] ) ){
			foreach( (array) $_GET['uix'] as $uix_slug ) {
				if( !empty( $this->objects[ strip_tags( $uix_slug ) ] ) ){
					$slugs[] = strip_tags( $uix_slug );
				}
			}
		}
		
		return $slugs;
	}

	/**
	 * get the uix config
	 *
	 * @since 0.0.1
	 * @param array $slug registered UIX slug to fetch
	 *
	 * @return array|null $uix array structure of current uix point or null if invalid
	 */
	protected function get( $slug ){

		$uix = null;

		// get the slug from base ID
		if( !empty( $this->objects[ $slug ] ) ){
			$uix = $this->objects[ $slug ];
			/**
			 * Filter UIX object
			 *
			 * @param array $uix The uix object array.
			 * @param string $slug slug of object being loaded
			 */		
			$uix = apply_filters( 'uix_load-' . $this->type, $uix, $slug );
		}

		return $uix;

	}


}