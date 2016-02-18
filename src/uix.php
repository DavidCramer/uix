<?php
/**
 * UIX Setting Class.
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace ui;

/**
 * Settings class
 * @package uix
 * @author  David Cramer
 */
class uix{

	/**
	 * The slug for this plugin
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = null;

	/**
	 * List of registered pages
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	protected $pages = array();

	/**
	 * List of registered metaboxes
	 *
	 * @since 1.0.0
	 *
	 * @var      array
	 */
	protected $metaboxes = array();	

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object|\uix
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
	private function __construct( $pages, $slug ) {


		// set slug
		$this->plugin_slug = $slug;

		// add admin page
		add_action( 'admin_menu', array( $this, 'add_settings_pages' ), 25 );

		// add metaboxes
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes'), 25 );

		// save config
		add_action( 'wp_ajax_' . $this->plugin_slug . '_save_config', array( $this, 'save_config') );

	}


	public function add_metaboxes() {
    	//add_meta_box( 'meta-box-id', __( 'My Meta Box', 'textdomain' ), array( $this, 'render_metabox' ), 'post' );
	}

	public function render_metabox( $a ){
		var_dump( $a );
		die;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object|\uix\uix    A single instance of this class.
	 */
	public static function get_instance( $pages, $slug ) {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self( $pages, $slug );
		}

		return self::$instance;

	}

	/**
	 * Register the admin pages
	 *
	 * @since 1.0.0
	 *
	 */
	public function register_pages( $pages ) {
		// register pages
		$this->pages = $pages;
	}

	/**
	 * Register metaboxes
	 *
	 * @since 1.0.0
	 *
	 */
	public function register_metaboxes( $metaboxes ) {
		// register pages
		$this->metaboxes = $metaboxes;
	}


	/**
	 * Add defined contextual help to admin page
	 *
	 * @since 1.0.0
	 */
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
	 * Saves a config
	 *
	 * @uses "wp_ajax_uix_save_config" hook
	 *
	 * @since 0.0.1
	 */
	public function save_config(){

		if( ! empty( $_POST[ 'config' ] ) ){

			$config = json_decode( stripslashes_deep( $_POST[ 'config' ] ), true );

			if(	wp_verify_nonce( $_POST['uix_setup'], $this->plugin_slug ) ){
				/**
				 * Filter settings pages to be created
				 *
				 * @param array $pages Page structures to be created
				 */
				$pages = apply_filters( $this->plugin_slug . '_get_admin_pages', $this->pages );
				$page_slug = sanitize_text_field( $_POST['page_slug'] );

				if( !empty( $pages[ $page_slug ] ) ){
					$success = __( 'Settings saved.', $this->plugin_slug );
					if( !empty( $pages[ $page_slug ]['saved_message'] ) ){
						$success = $pages[ $page_slug ]['saved_message'];
					}
					$option_tag = '_' . $this->plugin_slug . '_' . $page_slug;
					if( !empty( $pages[ $page_slug ]['option_name'] ) ){
						$option_tag = $pages[ $page_slug ]['option_name'];
					}

					update_option( $option_tag, $config );
					wp_send_json_success( $success );
				}

			}else{
				wp_send_json_error( esc_html__( 'Could not verify nonce', $this->plugin_slug ) );
			}

		}

		// nope
		wp_send_json_error( esc_html__( 'Could not save, sorry.', $this->plugin_slug ) );
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

		$uix['slug'] = $this->plugin_slug;

		// allow for minimized scripts
		$prefix = '.min';
		$uix_url = plugin_dir_url( dirname( __FILE__ ) );
		if( defined( 'DEBUG_SCRIPTS' ) ){
			$prefix = null;
		}
		// base styles
		wp_enqueue_style( $this->plugin_slug . '-base-styles', $uix_url . 'assets/css/admin' . $prefix . '.css' );
		// enqueue scripts
		wp_enqueue_script( 'handlebars', $uix_url . 'assets/js/handlebars.min-latest.js', array(), null, true );
		wp_enqueue_script( $this->plugin_slug . '-helpers', $uix_url . 'assets/js/uix-helpers' . $prefix . '.js', array( 'handlebars' ), null, true );
		wp_enqueue_script( $this->plugin_slug . '-core-admin', $uix_url . 'assets/js/uix-core' . $prefix . '.js', array( 'jquery', 'handlebars' ), null, true );

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

		wp_localize_script( $this->plugin_slug . '-core-admin', 'uix', $uix );
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
		$pages = apply_filters( $this->plugin_slug . '_get_admin_pages', $this->pages );

		// get the page slug from base ID
		$page_slug = array_search( $screen->base, $this->plugin_screen_hook_suffix );
		if( empty( $page_slug ) || empty( $pages[ $page_slug ] ) ){
			return false; // in case its not found or the array item is no longer valid, just leave.
		}
		// return the base array
		$uix = $pages[ $page_slug ];
		if( empty( $uix['option_name'] ) ){
			$uix['option_name'] = '_' . $this->plugin_slug . '_' . sanitize_text_field( $page_slug );
		}
		// get config object
		$config_object = get_option( $uix['option_name'], array() );

		/**
		 * Filter config object
		 *
		 * @param array $config_object The object as retrieved from DB
		 * @param array $page_slug The page slug this object belongs to.
		 */
		$uix['config'] = apply_filters( $this->plugin_slug . '_get_config', $config_object, $page_slug );
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
		$pages = apply_filters( $this->plugin_slug . '_get_admin_pages-' . $this->plugin_slug, $this->pages );

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
		$template_path = plugin_dir_path( dirname( __FILE__ ) );
		?>
		<div class="wrap">
			<h1 class="uix-title"><?php esc_html_e( $uix['page_title'] , $this->plugin_slug ); ?>
				<?php if( !empty( $uix['save_button'] ) ){ ?>
				<a class="page-title-action" href="#save-object" data-save-object="true">
					<span class="spinner uix-save-spinner"></span>
					<?php esc_html_e( $uix['save_button'], $this->plugin_slug ); ?>
				</a>
				<?php } ?>
			</h1>
			<?php if( !empty( $uix['tabs'] ) ){ ?>
			<nav class="uix-sub-nav" <?php if( count( $uix['tabs'] ) === 1 ){ ?>style="display:none;"<?php } ?>>
				<?php foreach( (array) $uix['tabs'] as $tab_slug => $tab ){ ?><a data-tab="<?php echo esc_attr( $tab_slug ); ?>" href="#<?php echo esc_attr( $tab_slug ) ?>"><?php echo esc_html( $tab['menu_title'] ); ?></a><?php } ?>
			</nav>
			<?php } ?>
			<?php wp_nonce_field( $this->plugin_slug, 'uix_setup' ); ?>
			<?php 
			if( !empty( $uix['tabs'] ) ){
				foreach( (array) $uix['tabs'] as $tab_slug => $tab ){ ?>
					<div class="uix-tab-canvas" data-app="<?php echo esc_attr( $tab_slug ); ?>"></div>
					<script type="text/html" data-template="<?php echo esc_attr( $tab_slug ); ?>">
						<?php 
							if( !empty( $tab['page_title'] ) ){ echo '<h4>' . $tab['page_title']; }
							if( !empty( $tab['page_description'] ) ){ ?> <small><?php echo $tab['page_description']; ?></small> <?php } 
							if( !empty( $tab['page_title'] ) ){ echo '</h4>'; }
							// include this tabs template
							if( !empty( $tab['template'] ) && file_exists( $template_path . $tab['template'] ) ){
								include $template_path . $tab['template'];
							}else{
								echo esc_html__( 'Template not found: ', $this->plugin_slug ) . $tab['page_title'];
							}
						?>
					</script>
					<?php if( !empty( $tab['partials'] ) ){
						foreach( $tab['partials'] as $partial_id => $partial ){
							?>
							<script type="text/html" id="__partial_<?php echo esc_attr( $partial_id ); ?>" data-handlebars-partial="<?php echo esc_attr( $partial_id ); ?>">
								<?php
									// include this tabs template
									if( !empty( $partial ) && file_exists( $template_path . $partial ) ){
										include $template_path . $partial;
									}else{
										echo esc_html__( 'Partial Template not found: ', $this->plugin_slug ) . $partial_id;
									}
								?>
							</script>
							<?php
						}
					}
				}
			}else{
				if( !empty( $uix['template'] ) && file_exists( $uix['template'] ) ){
					include $uix['template'];
				}
			}
			?>			
		</div>

		<script type="text/html" data-template="__notice">
		<div class="{{#if success}}updated{{else}}error{{/if}} notice uix-notice is-dismissible">
			<p>{{{data}}}</p>
			<button class="notice-dismiss" type="button">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
		</script>
		<script type="text/html" id="__partial_save">		
			<button class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="save">
				Save Changes
			</button>
		</script>
		<script type="text/html" id="__partial_create">
			<button class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="add">
				Create
			</button>
		</script>
		<script type="text/html" id="__partial_delete">
			<button style="float:left;" class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="delete">
				Remove
			</button>
		</script>
		<?php
	}
	
}

