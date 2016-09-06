<?php
/**
 * UIX Metaboxes
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix2\ui;

/**
 * Metabox class
 * @package uix2\ui
 * @author  David Cramer
 */
class metabox extends panel {

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access public
     * @var      string
     */
    public $type = 'metabox';

    /**
     * Holds the current post object
     *
     * @since 2.0.0
     * @access public
     * @var      object|WP_Post
     */
    public $post = null;


    /**
     * setup actions and hooks to add metaboxes and save metadata
     *
     * @since 2.0.0
     * @access protected
     */
    protected function actions() {

        // run parent to keep init and enqueuing assets
        parent::actions();
        // add metaboxes
        add_action( 'add_meta_boxes', array( $this, 'add_metaboxes'), 25 );
        // save metabox
        add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 );
    }

    /**
     * set metabox styles
     *
     * @since 2.0.0
     * @see \uix2\ui\uix
     * @access public
     */
    public function uix_styles() {
        parent::uix_styles();
        // Add metabox style
        $styles = array(
            'metabox'        =>  $this->url . 'assets/css/uix-metabox' . $this->debug_styles . '.css'
        );
        $this->styles( $styles );
    }


    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 2.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){
        ?><style type="text/css">
        #<?php echo $this->id(); ?>.uix-top-tabs > .uix-panel-tabs > li[aria-selected="true"] a,
        #side-sortables #<?php echo $this->id(); ?> > .uix-panel-tabs > li[aria-selected="true"] a{
        box-shadow: 0 3px 0 <?php echo $this->base_color(); ?> inset;
        }
        #<?php echo $this->id(); ?> > .uix-panel-tabs > li[aria-selected="true"] a {
        box-shadow: 3px 0 0 <?php echo $this->base_color(); ?> inset;
        }
        </style>
        <?php
    }


    /**
     * Add metaboxes to screen
     *
     * @since 2.0.0
     * @access public
     * @uses "add_meta_boxes" hook
     */
    public function add_metaboxes(){

        if( ! $this->is_active() ){ return; }

        // metabox defaults
        $defaults = array(
            'screen' => null,
            'context' => 'advanced',
            'priority' => 'default',
        );
                    
        $metabox = array_merge( $defaults, $this->struct );

        add_meta_box(
            $this->slug,
            $metabox['name'],
            array( $this, 'create_metabox' ),
            $metabox['screen'],
            $metabox['context'],
            $metabox['priority']
        );

    }
    
    /**
     * Callback for the `add_meta_box` that sets the metabox data and renders it
     *
     * @since 2.0.0
     * @uses "add_meta_box" function
     * @access public
     * @param object/wp_post $post Current post for the metabox
     * @param array $metabox Metabox args array
     */
    public function create_metabox( $post, $metabox ){

        $this->post = $post;    
        
        $data = get_post_meta( $post->ID );
        
        $this->set_data( $data );

        $this->render();

    }

    /**
     * Render the Metabox
     *
     * @since 2.0.0
     * @access public
     */
    public function render(){
        
        if( !empty( $this->struct['template'] ) ){
            ?>
            <div class="uix-item" data-uix="<?php echo esc_attr( $this->slug ); ?>">
            <?php
                if( file_exists( $this->struct['template'] ) ){
                    include $this->struct['template'];
                }else{
                    echo esc_html__( 'Template not found: ', 'text-domain' ) . $this->struct['template'];
                }
            ?>
            </div>
        <?php
            }elseif( !empty( $this->child ) ){
                // render fields setup
                parent::render();

            }else{
                echo esc_html__( 'No sections or template found', 'text-domain' );
            }
        ?>
        <script type="text/javascript">
            <?php if( !empty( $this->struct['chromeless'] ) ){ ?>
                jQuery('#<?php echo $this->slug; ?>').addClass('uix-chromeless');
            <?php } ?>
            jQuery('#<?php echo $this->slug; ?>').addClass('uix-metabox');
        </script>        
        <?php
        
    }
    

    /**
     * Sets the data for all sections and thier controls
     *
     * @since 2.0.0
     * @access public
     * @param array $data Metadata to push to controls
     */    
    public function set_data( $data ){
        if( empty( $this->child ) ){ return; }

        foreach( $this->child as $section ){
            if( empty( $section->child ) ){ continue; }
            foreach( $section->child as $control ){

                if( isset( $data[ $control->slug ] ) )
                    foreach( $data[ $control->slug ] as $meta_value )
                        $control->set_data( $meta_value );
            }
        }

    }

    /**
     * Saves a metabox data
     *
     * @uses "save_post" hook
     * @since 2.0.0
     * @access public
     * @param int $post_id ID of the current post being saved
     * @param object/wp_post $post Current post being saved
     */
    public function save_meta( $post_id, $post ){
        $this->post = $post;
        if( ! $this->is_active() ){ return; }

        $data = uix2()->request_vars( 'post' );


        foreach( $this->child as $section ){
            $section_data = $section->get_data();
            if( null === $section_data ){ continue; }
            foreach( (array) $section_data as $meta_key=>$meta_value ){
                $this->save_meta_data( $meta_key, $meta_value );
            }
        }

    }

    /**
     * Save the meta data for the post
     *
     * @since 2.0.0
     * @access public
     * @param string $slug slug of the meta_key
     * @param mixed $data Data to be saved
     */
    public function save_meta_data( $slug, $data ){

        $prev = get_post_meta( $this->post->ID, $slug, true );

        if ( null === $data && $prev ){
            delete_post_meta( $this->post->ID, $slug );
        }elseif ( $data !== $prev ) {
            update_post_meta( $this->post->ID, $slug, $data );
        }    

    }

    /**
     * Get current data for all sections of the metabox
     * @since 2.0.0
     * @access public
     * @param string $slug The slug of the metabox to get sections data for
     */
    public function get_sections_data( $slug ){
        
        $metabox = $this->get( $slug );
        $data = array();
        if( !empty( $metabox['sections'] ) ){
            foreach( $metabox['sections'] as $section_id => $section ){
                $data[ $section_id ] = uix2()->ui->sections->get_data( $section_id );
            }
        }
        return $data;
    }

    /**
     * Determin which metaboxes are used for the current screen and set them active
     * @since 2.0.0
     * @access public
     */
    public function is_active(){
        if( !is_admin() ){ return false; }

        if( !empty( $this->post ) ){
            if( isset( $this->struct['screen'] ) && in_array( $this->post->post_type, (array) $this->struct['screen'] ) ){
                return true;
            }
        }else{

            if( function_exists( 'get_current_screen' ) ){
                global $post;
                // check that the screen object is valid to be safe.
                $screen = get_current_screen();         
                if( empty( $screen ) || !is_object( $screen ) || $screen->base !== 'post' ){
                    return false;
                }                
                if( !empty( $screen->post_type ) && isset( $this->struct['screen'] ) && is_array( $this->struct['screen'] ) && in_array( $screen->id, $this->struct['screen'] ) ){
                    $this->post = $post;
                    return true;
                }
                // set current post                
            }
        }
        return parent::is_active();     
    }

}