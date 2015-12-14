<?php
/**
 * UIX Setting.
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer
 */
namespace uix;

/**
 * Settings class
 * @package uix
 * @author  David Cramer
 */
class admin extends core{


	/**
	 * Constructor for class
	 *
	 * @since 0.0.1
	 */
	public function __construct(){

		// add admin page
		add_action( 'admin_menu', array( $this, 'add_settings_pages' ), 25 );

		// save config
		add_action( 'wp_ajax_uix_save_config', array( $this, 'save_config') );

		// exporter
		add_action( 'init', array( $this, 'check_exporter' ) );

		// create new
		add_action( 'wp_ajax_uix_create_uix', array( $this, 'create_new_uix') );

		// delete
		add_action( 'wp_ajax_uix_delete_uix', array( $this, 'delete_uix') );

		// add pages filter
		add_filter( 'uix_get_admin_pages', array( $this, 'get_admin_pages') );

	}

	public function add_help(){
		

		$page = $this->get_page();
		
		if( !empty( $page['help'] ) ){

			$screen = get_current_screen();
			
			foreach( (array) $page['help'] as $help_slug => $help ){

				if( is_file( $help['content'] ) && file_exists( $help['content'] ) ){
					ob_start();
					include $help['content'];
					$content = ob_get_clean();
				}else{
					$content = $help['content'];
				}

				$screen->add_help_tab( array(
					'id'       =>	$help_slug,
					'title'    =>	$help['title'],
					'content'  =>	$content
				));
			}
			
			// Help sidebars are optional
			if(!empty( $page['help_sidebar'] ) ){
				$screen->set_help_sidebar( $page['help_sidebar'] );
			}
		}

		}


	
	/**
	 * builds an export
	 *
	 * @uses "wp_ajax_uix_check_exporter" hook
	 *
	 * @since 0.0.1
	 */
	public function check_exporter(){

		if( current_user_can( 'manage_options' ) ){

			if( !empty( $_REQUEST['download'] ) && !empty( $_REQUEST['cf-io-export'] ) && wp_verify_nonce( $_REQUEST['cf-io-export'], 'cf-io' ) ){

				$data = options::get_single( $_REQUEST['download'] );

				header( 'Content-Type: application/json' );
				header( 'Content-Disposition: attachment; filename="cf-io-export.json"' );
				echo wp_json_encode( $data );
				exit;

			}
			
		}
	}

	/**
	 * Saves a config
	 *
	 * @uses "uix_get_admin_pages" hook
	 *	 
	 * @since 0.0.1
	 * @param array $pre_pages  Array structure of pages to be created
	 *
	 * @return array $pages Array structure of pages to be created
	 */
	public function get_admin_pages( array $pre_pages ){

		$pages = include UIX_PATH . 'includes/pages.php';

		return array_merge( $pre_pages, $pages );
	}

	/**
	 * Saves a config
	 *
	 * @uses "wp_ajax_uix_save_config" hook
	 *
	 * @since 0.0.1
	 */
	public function save_config(){

		if( ! empty( $_POST[ 'config' ] ) ){

			$config = json_decode( stripslashes_deep( $_POST[ 'config' ] ), true );

			if(	wp_verify_nonce( $_POST['uix_setup'], 'uix' ) ){
				/**
				 * Filter settings pages to be created
				 *
				 * @param array $pages Page structures to be created
				 */
				$pages = apply_filters( 'uix_get_admin_pages', array() );
				$page_slug = sanitize_text_field( $_POST['page_slug'] );

				if( !empty( $pages[ $page_slug ] ) ){
					$success = __('Settings saved.', 'uix');
					if( !empty( $pages[ $page_slug ]['saved_message'] ) ){
						$success = $pages[ $page_slug ]['saved_message'];
					}
					$option_tag = '_uix_' . $page_slug;
					if( !empty( $pages[ $page_slug ]['option_name'] ) ){
						$option_tag = $pages[ $page_slug ]['option_name'];
					}

					update_option( $option_tag, $config );
					wp_send_json_success( $success );
				}

			}

		}

		// nope
		wp_send_json_error( $config );

	}

	/**
	 * Array of "internal" fields not to mess with
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function internal_config_fields() {
		return array( '_wp_http_referer', 'id', '_current_tab' );

	}


	/**
	 * Deletes an item
	 *
	 *
	 * @uses 'wp_ajax_uix_create_uix' action
	 *
	 * @since 0.0.1
	 */
	public function delete_uix(){
		$can = options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( __( 'Access denied', 'cf-io' ) );
		}

		$deleted = options::delete( strip_tags( $_POST[ 'block' ] ) );

		if ( $deleted ) {
			wp_send_json_success( $_POST );
		}else{
			wp_send_json_error( $_POST );
		}

	}


	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 1.0.0
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts() {

		$uix = $this->get_page();
		if( false === $uix ){
			return;
		}

		// allow for minimized scripts
		$prefix = null;
		if( !defined( 'DEBUG_SCRIPTS' ) ){
			$prefix = '.min';
		}

		// base styles
		wp_enqueue_style( 'uix-base-styles', UIX_URL . 'assets/css/admin' . $prefix . '.css' );
		// enqueue scripts
		wp_enqueue_script( 'handlebars', UIX_URL . 'assets/js/handlebars.min-latest.js', array(), null, true );
		wp_enqueue_script( 'uix-helpers', UIX_URL . 'assets/js/uix-helpers' . $prefix . '.js', array( 'handlebars' ), null, true );
		wp_enqueue_script( 'uix-core-admin', UIX_URL . 'assets/js/uix-core' . $prefix . '.js', array( 'jquery', 'handlebars' ), null, true );

		// enqueue admin runtime styles
		if( !empty( $uix[ 'styles'] ) ){
			foreach( $uix[ 'styles'] as $style_key => $style ){
				if( is_int( $style_key ) ){
					wp_enqueue_style( $style );
				}else{
					wp_enqueue_style( $style_key, $style );
				}
			}
		}
		// enqueue admin runtime scripts
		if( !empty( $uix[ 'scripts'] ) ){
			foreach( $uix[ 'scripts'] as $script_key => $script ){
				if( is_int( $script_key ) ){
					wp_enqueue_script( $script );
				}else{
					wp_enqueue_script( $script_key, $script );
				}
			}
		}

		wp_localize_script( 'uix-core-admin', 'uix', $uix );
	}


	/**
	 * Create a new item
	 *
	 * @uses "wp_ajax_uix_create_uix"  action
	 *
	 * @since 0.0.1
	 */
	public function create_new_uix(){

		$can = options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( __( 'Access denied', 'cf-io' ) );
		}


		if( !empty( $_POST['import'] ) ){
			$config = json_decode( stripslashes_deep( $_POST[ 'import' ] ), true );

			if( empty( $config['name'] ) || empty( $config['slug'] ) ){
				wp_send_json_error( $_POST );
			}
			$id = null;
			if( !empty( $config['id'] ) ){
				$id = $config['id'];
			}
			options::create( $config[ 'name' ], $config[ 'slug' ] );
			options::update( $config );
			wp_send_json_success( $config );
		}

		$new = options::create( $_POST[ 'name' ], $_POST[ 'slug' ], $_POST[ 'formid' ] );

		if ( is_array( $new ) ) {
			wp_send_json_success( $new );

		}else {
			wp_send_json_error( $_POST );

		}

	}

	/**
	 * get the config for the current page
	 *
	 * @since 0.0.1
	 *
	 * @return array $page array structure of current uix page
	 */
	private function get_page(){
		
		// check that the scrren object is valid to be safe.
		$screen = get_current_screen();

		if( empty( $screen ) || !is_object( $screen ) ){
			return false;
		}

		/**
		 * Filter settings pages to be created
		 *
		 * @param array $pages Page structures to be created
		 */
		$pages = apply_filters( 'uix_get_admin_pages', array() );

		// get the page slug from base ID
		$page_slug = array_search( $screen->base, $this->plugin_screen_hook_suffix );
		if( empty( $page_slug ) || empty( $pages[ $page_slug ] ) ){
			return false; // in case its not found or the array item is no longer valid, just leave.
		}
		// return the base array
		$uix = $pages[ $page_slug ];
		if( empty( $uix['option_name'] ) ){
			$uix['option_name'] = '_uix_' . sanitize_text_field( $page_slug );
		}
		// get config object
		$config_object = get_option( $uix['option_name'], array() );

		/**
		 * Filter config object
		 *
		 * @param array $config_object The object as retrieved from DB
		 * @param array $page_slug The page slug this object belongs to.
		 */
		$uix['config'] = apply_filters( 'uix_get_config', $config_object, $page_slug );
		$uix['page_slug'] = $page_slug;

		return $uix;
	}

	/**
	 * Add options page
	 *
	 * @since 0.0.1
	 *
	 * @uses "admin_menu" hook
	 */
	public function add_settings_pages(){

		/**
		 * Filter settings pages to be created
		 *
		 * @param array $pages Page structures to be created
		 */
		$pages = apply_filters( 'uix_get_admin_pages', array() );

		foreach( (array) $pages as $page_slug => $page ){
			
			if( empty( $page[ 'page_title' ] ) || empty( $page['menu_title'] ) ){
				continue;
			}

			$args = array(
				'capability'	=> 'manage_options',
				'icon'			=>	null,
				'position'		=> null
			);
			$args = array_merge( $args, $page );

			if( !empty( $page['parent'] ) ){

				$this->plugin_screen_hook_suffix[ $page_slug ] = add_submenu_page(
					$args[ 'parent' ],
					$args[ 'page_title' ],
					$args[ 'menu_title' ],
					$args[ 'capability' ], 
					$page_slug,
					array( $this, 'create_admin_page' )
				);

			}else{

				$this->plugin_screen_hook_suffix[ $page_slug ] = add_menu_page(
					$args[ 'page_title' ],
					$args[ 'menu_title' ],
					$args[ 'capability' ], 
					$page_slug,
					array( $this, 'create_admin_page' ),
					$args[ 'icon' ],
					$args[ 'position' ]
				);
			}
			add_action( 'admin_print_styles-' . $this->plugin_screen_hook_suffix[ $page_slug ], array( $this, 'enqueue_admin_stylescripts' ) );
			add_action( 'load-' . $this->plugin_screen_hook_suffix[ $page_slug ], array( $this, 'add_help' ) );
		}
	}

	/**
	 * Options page callback
	 *
	 * @since 0.0.1
	 */
	public function create_admin_page(){
		
		$uix = $this->get_page();

		include UIX_PATH . 'includes/admin-ui.php';

	}
	
}

