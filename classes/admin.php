<?php
/**
 * IO Setting.
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
	 * @uses "wp_ajax_uix_save_config" hook
	 *
	 * @since 0.0.1
	 */
	public function save_config(){

		$can = options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( __( 'Access denied', 'cf-io' ) );
		}

		if( empty( $_POST[ 'cf-io-setup' ] ) || ! wp_verify_nonce( $_POST[ 'cf-io-setup' ], 'cf-io' ) ){
			if( empty( $_POST['config'] ) ){
				return;

			}

		}

		if( ! empty( $_POST[ 'cf-io-setup' ] ) && empty( $_POST[ 'config' ] ) ){
			$config = stripslashes_deep( $_POST['config'] );

			options::update( $config );


			wp_redirect( '?page=uix&updated=true' );
			exit;

		}

		if( ! empty( $_POST[ 'config' ] ) ){

			$config = json_decode( stripslashes_deep( $_POST[ 'config' ] ), true );

			if(	wp_verify_nonce( $config['cf-io-setup'], 'cf-io' ) ){
				options::update( $config );
				wp_send_json_success( $config );

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

		$prefix = '.min';
		if( defined( 'DEBUG_SCRIPTS' ) ){
			$prefix = null;
		}

		wp_enqueue_style( 'uix-admin', UIX_URL . 'assets/css/admin' . $prefix . '.css' );
		wp_enqueue_script( 'uix-admin', UIX_URL . 'assets/js/admin' . $prefix . '.js' );

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
	 * Add options page
	 *
	 * @since 0.0.1
	 *
	 * @uses "admin_menu" hook
	 */
	public function add_settings_pages(){
			
		
		$this->plugin_screen_hook_suffix[ 'uix' ] = add_menu_page(
			esc_html__( 'UIX', 'uix' ),
			esc_html__( 'UIX', 'uix' ),
			'manage_options', 
			'uix',
			array( $this, 'create_admin_page' )
		);

		add_action( 'admin_print_styles-' . $this->plugin_screen_hook_suffix[ 'uix' ], array( $this, 'enqueue_admin_stylescripts' ) );
	}

	/**
	 * Options page callback
	 *
	 * @since 0.0.1
	 */
	public function create_admin_page(){

		include UIX_PATH . 'templates/admin-ui.php';

	}
	
}

