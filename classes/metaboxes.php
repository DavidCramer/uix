<?php
/**
 * UIX Metaboxes
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
class metaboxes extends data\localized{

	/**
	 * The type of object
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	protected $type = 'metabox';

	/**
	 * Holds a pages instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object|\uix\pages
	 */
	private static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object|\uix\pages    A single instance of pages
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * register add settings pages
	 *
	 * @since 1.0.0
	 *
	 */
	protected function actions() {
		parent::actions();
		// add metaboxes
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes'), 25 );
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
		
		$slugs = $this->locate();
		
		if( empty( $slugs ) ){
			return;
		}
		
		// Add metabox style
		$styles = array(
			'styles'		=>	$this->url . 'assets/css/metabox' . $this->debug_styles . '.css'
		);
		$this->styles( $styles );

		// post type metaboxes
		$configs = array();
		foreach( (array) $slugs as $metabox_slug ){
			
			$metabox = $this->get( $metabox_slug );

			add_meta_box(
				$metabox_slug,
				$metabox['name'],
				array( $this, 'render_metabox' ),
				$metabox['post_type'],
				$metabox['context'],
				$metabox['priority']
				
			);

		}

	}

	/**
	 * render metabox
	 *
	 * @since 1.0.0
	 *
	 */
	public function render_metabox( $post, $metabox ){

		$uix = $this->get( $metabox['id'] );
		
		if( !empty( $uix['base_color'] ) ){
			$text_color = "#fff";
			if( !empty( $uix['base_text_color'] ) ){
				$text_color = $uix['base_text_color'];
			}
		?><style type="text/css">.uix-modal-title .uix-modal-closer{ color: <?php echo $text_color; ?>; }.uix-modal-wrap .uix-modal-title > h3,.wrap a.page-title-action:hover{background: <?php echo $uix['base_color']; ?>; color: <?php echo $text_color; ?>;}</style>
		<?php
		}
		?>
		<div class="uix-item" data-uix="<?php echo esc_attr( $metabox['id'] ); ?>">
			<input id="uix_<?php echo esc_attr( $metabox['id'] ); ?>" name="uix[<?php echo esc_attr( $metabox['id'] ); ?>]" value="" type="hidden">
			<div class="uix-tab-canvas" data-app="<?php echo esc_attr( $metabox['id'] ); ?>"></div>
		</div>
		<script type="text/html" data-template="<?php echo esc_attr( $metabox['id'] ); ?>">
			<?php 
				if( !empty( $uix['template'] ) && file_exists( $uix['template'] ) ){
					include $uix['template'];
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
						if( !empty( $partial ) && file_exists( $partial ) ){
							include $partial;
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
						if( !empty( $modal ) && file_exists( $modal ) ){
							include $modal;
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
	 * Saves a metabox config
	 *
	 * @uses "save_post" hook
	 *
	 * @since 0.0.1
	 */
	public function save_meta( $post_id ){
		
		if( !empty( $_POST['uix'] ) ){
			
			foreach( (array) $_POST['uix'] as $slug => $data ){

				$uix = $this->get( $slug );

				if( empty( $uix ) ){
					continue;
				}

				
				$config = json_decode( stripslashes_deep( $data ), true );

				$meta_name = $this->store_key( $slug );
				// set config object
				$config_object = update_post_meta( $post_id, $meta_name, $config );

			}

		}
		
	}

	/**
	 * Loads a UIX config
	 * @since 1.0.0
	 *
	 * @return mixed $data the saved data fro the specific UIX object
	 */
	public function get_data( $slug, $post_id = null ){

		if( $post_id === null ){
			global $post;
			if( empty( $post ) ){ return null; }
			$post_id = $post->ID;
		}
		
		$uix = $this->get( $slug );

		// get config object
		$config_object = array(
			$slug => get_post_meta( $post_id, $this->store_key( $slug ), true )
		);

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
	 * Determin if a UIX object should be loaded for this screen
	 * Intended to be ovveridden
	 * @since 0.0.1
	 *
	 * @return array $array of slugs of a registered structures relating to this screen
	 */
	protected function locate(){

		$slugs = array();

		// check that the scrren object is valid to be safe.
		$screen = get_current_screen();

		if( empty( $screen ) || !is_object( $screen ) || empty( $screen->post_type ) || 'post' !== $screen->base ){
			return $slugs;
		}

		foreach( (array) $this->objects as $slug=>$object ) {
			if( !empty( $object['post_type'] ) && in_array( $screen->post_type, $object['post_type'] ) ){
				$slugs[] = strip_tags( $slug );
			}
		}

		return $slugs;
	}
}