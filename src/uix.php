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
	private function __construct( $slug ) {


		// set slug
		$this->plugin_slug = $slug;

		// add admin page
		add_action( 'admin_menu', array( $this, 'add_settings_pages' ), 25 );

		// add metaboxes
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes'), 25 );

		// save config
		add_action( 'wp_ajax_' . $this->plugin_slug . '_save_config', array( $this, 'save_config') );

		// save metabox
		add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 );

	}

	/**
	 * Add metaboxes
	 *
	 * @since 0.0.1
	 *
	 * @uses "add_meta_boxes" hook
	 */
	public function add_metaboxes(){

		$screen = get_current_screen();
		if( !is_object( $screen ) || $screen->base != 'post' ){
			return;
		}
		// post type metaboxes
		$configs = array();
		foreach( (array) $this->metaboxes as $metabox_slug => $metabox ){
			
			// only process this post type
			if( empty($metabox['post_type']) || !in_array( $screen->post_type, (array) $metabox['post_type'] ) || empty( $metabox[ 'name' ] )  ){
				continue;
			}

			add_meta_box(
				$metabox_slug,
				$metabox['name'],
				array( $this, 'render_metabox' ),
				$screen->post_type,
				$metabox['context'],
				$metabox['priority'],
				$metabox
			);

			// do scripts
			$uix = $this->get_metabox( $metabox_slug );
			if( false !== $uix ){
				$configs[ $metabox_slug ] = $uix;
			}

		}
		// scripts
		$this->enqueue_metabox_stylescripts( $configs, $screen->post_type );

	}


	public function render_metabox( $post, $metabox ){

		$uix = $metabox['args'];
		$template_path = plugin_dir_path( dirname( __FILE__ ) );
		if( !empty( $uix['base_color'] ) ){
		?><style type="text/css">.uix-modal-title > h3,.wrap a.page-title-action:hover{background: <?php echo $uix['base_color']; ?>;}</style>
		<?php
		}
		?>
		<input id="uix_<?php echo esc_attr( $metabox['id'] ); ?>" name="uix[<?php echo esc_attr( $metabox['id'] ); ?>]" value="" type="hidden">
		<div class="uix-tab-canvas" data-app="<?php echo esc_attr( $metabox['id'] ); ?>"></div>
		<script type="text/html" data-template="<?php echo esc_attr( $metabox['id'] ); ?>">
			<?php 
				if( !empty( $uix['template'] ) && file_exists( $template_path . $uix['template'] ) ){
					include $template_path . $uix['template'];
				}else{
					echo esc_html__( 'Template not found: ', $this->plugin_slug ) . $uix['template'];
				}
			?>
		</script>
		<?php if( !empty( $uix['partials'] ) ){
			foreach( $uix['partials'] as $partial_id => $partial ){
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


		if( !empty( $uix['modals'] ) ){
			foreach( $uix['modals'] as $modal_id => $modal ){
				?>
				<script type="text/html" id="__modal_<?php echo esc_attr( $modal_id ); ?>" data-handlebars-partial="<?php echo esc_attr( $modal_id ); ?>">
					<?php
						// include this tabs template
						if( !empty( $modal ) && file_exists( $template_path . $modal ) ){
							include $template_path . $modal;
						}else{
							echo esc_html__( 'Modal Template not found: ', $this->plugin_slug ) . $modal_id;
						}
					?>
				</script>
				<?php
			}
		}
		?>			


		<script type="text/html" id="__partial_save">		
			<button class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="save" 
				{{#if __callback}}data-callback="{{__callback}}"{{/if}}
				{{#if __before}}data-before="{{__before}}"{{/if}}
			>
				Save Changes
			</button>
		</script>
		<script type="text/html" id="__partial_create">
			<button class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="add" 
				{{#if __callback}}data-callback="{{__callback}}"{{/if}}
				{{#if __before}}data-before="{{__before}}"{{/if}}
			>
				Create
			</button>
		</script>
		<script type="text/html" id="__partial_delete">
			<button style="float:left;" class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="delete" 
				{{#if __callback}}data-callback="{{__callback}}"{{/if}}
				{{#if __before}}data-before="{{__before}}"{{/if}}
			>
				Remove
			</button>
		</script>
		<?php if( !empty( $uix['chromeless'] ) ){ ?>
		<script type="text/javascript">
			jQuery('#<?php echo $metabox['id']; ?>').addClass('uix-metabox');
		</script>
		<?php } ?>
		<script type="text/javascript">
			jQuery( document ).on('submit', '#post', function( e ){
				
				var uix_config = conduitPrepObject( '<?php echo $metabox['id']; ?>' );
				jQuery('#uix_<?php echo $metabox['id']; ?>').val( JSON.stringify( uix_config.<?php echo $metabox['id']; ?> ) );
				
			});
		</script>
		<?php
		
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object|\uix\uix    A single instance of this class.
	 */
	public static function get_instance( $slug ) {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self( $slug );
		}

		return self::$instance;

	}

	/**
	 * Return a setting
	 *
	 * @since 1.0.0
	 *
	 * @return    string/array    the requested setting
	 */
	public static function get_setting( $path, $manual = false ) {

		$path = explode( '.', $path );
		$temp = null;
		$page_slug = array_shift( $path );

		if ( null == self::$instance || true === $manual ) {
			if( false === $manual ){
				trigger_error( 'Cannot request a value without a UIX instance. Set second argument to TRUE for manual lookup.' );
				return;
			}
			// attempt a manual lookup - requires the full option name
			$option_tag = $page_slug;
		}else{
			if( !empty( self::$instance->pages[ $page_slug ]['option_name'] ) ){
				$option_tag = self::$instance->pages[ $page_slug ]['option_name'];
			}else{
				$option_tag = '_' . self::$instance->plugin_slug . '_' . $page_slug;
			}
		}
		$temp = get_option( $option_tag );
		foreach ($path as $index => $value) {
			if( !isset( $temp[ $value ] ) ){
				return null;
			}
			$temp = $temp[ $value ];
		}

		return $temp;

	}
	/**
	 * Register the admin pages
	 *
	 * @since 1.0.0
	 *
	 */
	public function register_pages( $pages ) {

		/**
		 * Filter settings pages to be created
		 *
		 * @param array $pages Page structures to be created
		 */

		$this->pages = apply_filters( $this->plugin_slug . '_set_admin_pages', $pages );
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
	 * Saves a metabox config
	 *
	 * @uses "save_post" hook
	 *
	 * @since 0.0.1
	 */
	public function save_meta( $post_id ){
		
		if( !empty( $_POST['uix'] ) ){
			
			foreach( ( array) $_POST['uix'] as $slug => $data ){
				if( empty( $this->metaboxes[ $slug ] ) ){
					continue;
				}

				$uix = $this->metaboxes[ $slug ];
				$config = json_decode( stripslashes_deep( $data ), true );

				if( empty( $uix['meta_name'] ) ){
					$uix['meta_name'] = '_' . $this->plugin_slug . '_' . sanitize_text_field( $slug );
				}
				// get config object

				$config_object = update_post_meta( $post_id, $uix['meta_name'], $config );

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

				$page_slug = sanitize_text_field( $_POST['page_slug'] );

				if( !empty( $this->pages[ $page_slug ] ) ){
					$params = null;
					if( !empty( $_POST['params'] ) ){
						$params = $_POST['params'];
					}
					/**
					 * Filter page settings pre save
					 *
					 * @param array $page_config the page config array
					 * @param array $params any defined save_params.
					 */
					$page = apply_filters( $this->plugin_slug . '_get_page_save', $this->pages[ $page_slug ], $params );

					/**
					 * Filter config object
					 *
					 * @param array $config the config array to save
					 * @param array $page the page config to be saved for
					 */
					$config = apply_filters( $this->plugin_slug . '_pre_save_config', $config, $page );


					$success = __( 'Settings saved.', $this->plugin_slug );
					if( !empty( $page['saved_message'] ) ){
						$success = $page['saved_message'];
					}
					$option_tag = '_' . $this->plugin_slug . '_' . $page_slug;
					if( !empty( $page['option_name'] ) ){
						$option_tag = $page['option_name'];
					}
					// push backup if not autosave
					if( empty( $_POST['autosave'] ) ){
						$previous = get_option( $option_tag );
						if( !empty( $previous ) ){
							update_option( $option_tag .'-' . current_time( 'timestamp' ), $previous );
						}
					}
					// save object
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
		$uix_url = plugin_dir_url( __FILE__ );
		if( defined( 'DEBUG_SCRIPTS' ) ){
			$prefix = null;
		}
		// base styles
		wp_enqueue_style( $this->plugin_slug . '-base-icons', $uix_url . 'assets/css/icons' . $prefix . '.css' );
		wp_enqueue_style( $this->plugin_slug . '-base-styles', $uix_url . 'assets/css/admin' . $prefix . '.css' );
		// enqueue scripts
		wp_enqueue_script( 'handlebars', $uix_url . 'assets/js/handlebars.min-latest.js', array(), null, true );
		// if has modals
		if( !empty( $uix['modals'] ) ){
			wp_enqueue_script( $this->plugin_slug . '-core-modals', $uix_url . 'assets/js/uix-modals' . $prefix . '.js', array( 'jquery', 'handlebars' ), null, true );
		}
		wp_enqueue_script( $this->plugin_slug . '-helpers', $uix_url . 'assets/js/uix-helpers' . $prefix . '.js', array( 'handlebars' ), null, true );
		wp_enqueue_script( $this->plugin_slug . '-core-admin', $uix_url . 'assets/js/uix-core' . $prefix . '.js', array( 'jquery', 'handlebars' ), null, true );

		// enqueue admin runtime styles
		$this->enqueue_set( $uix, $this->plugin_slug . '-' . $uix['page_slug'] );

		// enqueue tab specific runtime styles
		if( !empty( $uix[ 'tabs'] ) ){
			foreach( $uix['tabs'] as $tab_slug => $tab ){
				$this->enqueue_set( $tab, $this->plugin_slug . '-' . $uix['page_slug'] . '-' . $tab_slug );
			}
		}	

		wp_localize_script( $this->plugin_slug . '-core-admin', 'uix', $uix );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 1.0.0
	 *
	 * @return    null
	 */
	public function enqueue_metabox_stylescripts( $metaboxes, $post_type ) {
		

		$uix = array(
			'config'	=> array(),
			'slug'		=> $this->plugin_slug,
			'page_slug'	=> $post_type
		);


		// allow for minimized scripts
		$prefix = '.min';
		$uix_url = plugin_dir_url( __FILE__ );
		if( defined( 'DEBUG_SCRIPTS' ) ){
			$prefix = null;
		}
		// base styles
		wp_enqueue_style( $this->plugin_slug . '-base-icons', $uix_url . 'assets/css/icons' . $prefix . '.css' );
		wp_enqueue_style( $this->plugin_slug . '-base-styles', $uix_url . 'assets/css/metabox' . $prefix . '.css' );
		// enqueue scripts
		wp_enqueue_script( 'handlebars', $uix_url . 'assets/js/handlebars.min-latest.js', array(), null, true );
		// if has modals

		if( !empty( $uix['modals'] ) ){
			wp_enqueue_script( $this->plugin_slug . '-core-modals', $uix_url . 'assets/js/uix-modals' . $prefix . '.js', array( 'jquery', 'handlebars' ), null, true );
		}
		wp_enqueue_script( $this->plugin_slug . '-helpers', $uix_url . 'assets/js/uix-helpers' . $prefix . '.js', array( 'handlebars' ), null, true );
		wp_enqueue_script( $this->plugin_slug . '-core-admin', $uix_url . 'assets/js/uix-core' . $prefix . '.js', array( 'jquery', 'handlebars' ), null, true );

		foreach( $metaboxes as $slug=>$metabox ){
			if( !empty( $metabox['modals'] ) ){
				$uix['modals'] = true;
			}
			$uix['config'][ $slug ] = $metabox['config'];
			// enqueue admin runtime styles
			$this->enqueue_set( $metabox, $this->plugin_slug . '-' . $slug );

		}

		wp_localize_script( $this->plugin_slug . '-core-admin', 'uix', $uix );
	}


	/**
	 * enqueue a set of styles and scripts
	 *
	 * @since 0.0.1
	 *
	 */
	private function enqueue_set( $set, $prefix ){
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

		// get the page slug from base ID
		$page_slug = array_search( $screen->base, $this->plugin_screen_hook_suffix );
		if( empty( $page_slug ) || empty( $this->pages[ $page_slug ] ) ){
			return false; // in case its not found or the array item is no longer valid, just leave.
		}
		/**
		 * Filter page object
		 *
		 * @param array $page The page object array.
		 */		
		$uix = apply_filters( $this->plugin_slug . '_get_page', $this->pages[ $page_slug ] );
		
		if( empty( $uix['option_name'] ) ){
			$uix['option_name'] = '_' . $this->plugin_slug . '_' . sanitize_text_field( $page_slug );
		}
		// get config object
		$config_object = get_option( $uix['option_name'], array() );

		$uix['page_slug'] = $page_slug;
		/**
		 * Filter config object
		 *
		 * @param array $config_object The object as retrieved from DB
		 * @param array $page_slug The page slug this object belongs to.
		 */
		$uix['config'] = apply_filters( $this->plugin_slug . '_get_config', $config_object, $uix );		


		return $uix;
	}

	/**
	 * get the config for the current metabox
	 *
	 * @since 0.0.1
	 *
	 * @return array $metabox array structure of current uix metabox
	 */
	private function get_metabox( $slug ){
		global $post;

		// check that the scrren object is valid to be safe.
		$screen = get_current_screen();

		if( empty( $screen ) || !is_object( $screen ) || empty( $screen->post_type ) ){
			return false;
		}

		// get the page slug from base ID
		if( empty( $this->metaboxes[ $slug ] ) ){
			return false; // in case its not found or the array item is no longer valid, just leave.
		}
		/**
		 * Filter page object
		 *
		 * @param array $page The page object array.
		 */		
		$uix = apply_filters( $this->plugin_slug . '_get_metabox', $this->metaboxes[ $slug ] );
		
		if( empty( $uix['meta_name'] ) ){
			$uix['meta_name'] = '_' . $this->plugin_slug . '_' . sanitize_text_field( $slug );
		}
		// get config object

		$config_object = get_post_meta( $post->ID, $uix['meta_name'], true );

		$uix['slug'] = $slug;
		/**
		 * Filter config object
		 *
		 * @param array $config_object The object as retrieved from DB
		 * @param array $slug The page slug this object belongs to.
		 */
		$uix['config'] = apply_filters( $this->plugin_slug . '_get_meta_config', $config_object, $uix );

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

		foreach( (array) $this->pages as $page_slug => $page ){
			
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
					$this->plugin_slug . '-' . $page_slug,
					array( $this, 'create_admin_page' )
				);

			}else{

				$this->plugin_screen_hook_suffix[ $page_slug ] = add_menu_page(
					$args[ 'page_title' ],
					$args[ 'menu_title' ],
					$args[ 'capability' ], 
					$this->plugin_slug . '-' . $page_slug,
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
		if( !empty( $uix['base_color'] ) ){
		?><style type="text/css">.contextual-help-tabs .active {border-left: 6px solid <?php echo $uix['base_color']; ?>;}.wrap > h1 {box-shadow: 0 0 2px rgba(0, 2, 0, 0.1),11px 0 0 <?php echo $uix['base_color']; ?> inset;}.uix-modal-title > h3,.wrap a.page-title-action:hover{background: <?php echo $uix['base_color']; ?>;}</style>
		<?php
		}
		?>
		<div class="wrap">
			<h1 class="uix-title"><?php esc_html_e( $uix['page_title'] , $this->plugin_slug ); ?>
				<?php if( !empty( $uix['version'] ) ){ ?><small><?php esc_html_e( $uix['version'], $this->plugin_slug ); ?></small><?php } ?>
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
			if( !empty( $uix['modals'] ) ){
				foreach( $uix['modals'] as $modal_id => $modal ){
					?>
					<script type="text/html" id="__modal_<?php echo esc_attr( $modal_id ); ?>" data-handlebars-partial="<?php echo esc_attr( $modal_id ); ?>">
						<?php
							// include this tabs template
							if( !empty( $modal ) && file_exists( $template_path . $modal ) ){
								include $template_path . $modal;
							}else{
								echo esc_html__( 'Modal Template not found: ', $this->plugin_slug ) . $modal_id;
							}
						?>
					</script>
					<?php
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
			<button class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="save" 
				{{#if __callback}}data-callback="{{__callback}}"{{/if}}
				{{#if __before}}data-before="{{__before}}"{{/if}}
			>
				Save Changes
			</button>
		</script>
		<script type="text/html" id="__partial_create">
			<button class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="add" 
				{{#if __callback}}data-callback="{{__callback}}"{{/if}}
				{{#if __before}}data-before="{{__before}}"{{/if}}
			>
				Create
			</button>
		</script>
		<script type="text/html" id="__partial_delete">
			<button style="float:left;" class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="delete" 
				{{#if __callback}}data-callback="{{__callback}}"{{/if}}
				{{#if __before}}data-before="{{__before}}"{{/if}}
			>
				Remove
			</button>
		</script>
		<?php
	}
	
}