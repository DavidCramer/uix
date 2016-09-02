<?php
/**
 * UIX Metaboxes
 *
 * @package   uixv2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uixv2\ui;

/**
 * Metaboxes class
 * @package uixv2\ui
 * @author  David Cramer
 */
class metaboxes extends uix {

    /**
     * The type of object
     *
     * @since 2.0.0
     * @access protected
     * @var      string
     */
    protected $type = 'metabox';

    /**
     * Holds the current post object
     *
     * @since 2.0.0
     * @access protected
     * @var      object|WP_Post
     */
    public $post = null;


    /**
     * setup actions and hooks to add metaboxes and save metadata
     *
     * @since 2.0.0
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
     * Sets the Metaboxes to the current instance and registers it's Sections 
     *
     * @since 2.0.0
     * @see \uixv2\uix
     * @param array $objects object structure array
     */
    public function set_objects( array $objects ) {
        
        // do parent set first to ensure they are added to the objects list
        parent::set_objects( $objects );

        foreach( $this->objects as $object_id => &$object ){
            
            if( empty( $object['sections'] ) )
                continue;

            foreach( $object['sections'] as $section_slug => &$section ){
                $section['metabox'] = $object_id;
            }
            uixv2()->load( 'sections', $object['sections'] );
        }
    }

    /**
     * set metabox styles
     *
     * @since 2.0.0
     * @see \uixv2\ui\uix
     */
    public function uix_styles() {
        // Add metabox style
        $styles = array(
            'metabox'        =>  $this->url . 'assets/css/metabox' . $this->debug_styles . '.css'
        );
        $this->styles( $styles );
    }

    /**
     * set metabox scripts
     *
     * @since 2.0.0
     * @see \uixv2\ui\uix
     */
    public function uix_scripts() {
        $scripts = array(
            'metabox'        =>  $this->url . 'assets/js/uix-metaboxes' . $this->debug_scripts . '.js'
        );
        $this->scripts( $scripts );
    }


    /**
     * Add metaboxes to screen
     *
     * @since 2.0.0
     *
     * @uses "add_meta_boxes" hook
     */
    public function add_metaboxes(){

        $this->locate();

        if( empty( $this->active_slugs ) )
            return;

        // metabox defaults
        $defaults = array(
            'screen' => null,
            'context' => 'advanced',
            'priority' => 'default',
        );
        foreach( (array) $this->active_slugs as $metabox_slug ){

            $object = $this->get( $metabox_slug );
                        
            $metabox = array_merge( $defaults, $object );

            add_meta_box(
                $metabox_slug,
                $metabox['name'],
                array( $this, 'create_metabox' ),
                $metabox['screen'],
                $metabox['context'],
                $metabox['priority']
            );

        }

    }
    
    /**
     * Callback for the `add_meta_box` that sets the metabox data and renders it
     *
     * @since 2.0.0
     * @uses "add_meta_box" function
     * @param object/wp_post $post Current post for the metabox
     * @param array $metabox Metabox args array
     */
    public function create_metabox( $post, $metabox ){

        $slug = $metabox['id'];
        $uix = $this->get( $slug );
        
        // Set current data for the metabox
        $this->set_data( $slug, $post );

        $this->render( $slug );

    }

    /**
     * Render the Metabox
     *
     * @since 2.0.0
     * @param array $slug Section slug to be rendered
     */
    public function render( $slug ){
        
        $uix = $this->get( $slug );

        if( !empty( $uix['base_color'] ) ){
            $text_color = "#fff";
            if( !empty( $uix['base_text_color'] ) ){
                $text_color = $uix['base_text_color'];
            }
        ?><style type="text/css">
        #side-sortables #<?php echo $slug; ?> .uix-metabox-tabs li[aria-selected="true"] a{
            box-shadow: 0 3px 0 <?php echo $uix['base_color']; ?> inset;
        }
        #<?php echo $slug; ?> .uix-metabox-tabs li[aria-selected="true"] a {
          box-shadow: 3px 0 0 <?php echo $uix['base_color']; ?> inset;
        }
        </style>
        <?php
        }
        if( !empty( $uix['template'] ) ){
            ?>
            <div class="uix-item" data-uix="<?php echo esc_attr( $slug ); ?>">
            <?php
                if( file_exists( $uix['template'] ) ){
                    include $uix['template'];
                }else{
                    echo esc_html__( 'Template not found: ', 'text-domain' ) . $uix['template'];
                }
            ?>
            </div>
        <?php
            }elseif( !empty( $uix['sections'] ) ){
                // render fields setup
                $this->build_metabox( $slug );

            }else{
                echo esc_html__( 'No sections or template found', 'text-domain' );
            }
        ?>
        <script type="text/javascript">
            <?php if( !empty( $uix['chromeless'] ) ){ ?>
                jQuery('#<?php echo $slug; ?>').addClass('uix-chromeless');
            <?php } ?>
            jQuery('#<?php echo $slug; ?>').addClass('uix-metabox');
        </script>        
        <?php
        
    }
    
    /**
     * build metabox
     *
     * @since 2.0.0
     * @param string $slug metabox id to build
     */
    public function build_metabox( $slug ){    
        
        $metabox = $this->get( $slug );
        if( count( $metabox['sections'] ) > 1 ){
            echo '<div class="uix-metabox-inside uix-has-tabs">';
                echo '<ul class="uix-metabox-tabs">';
                foreach( $metabox['sections'] as $section_id=>$section ){
                    if( empty( $section['controls'] ) && empty( $section['template'] ) ){ continue; }
                    $label = esc_html( $section['label'] );
                    if( empty( $section['active'] ) ){
                        $section['active'] = 'false';
                    }else{
                        $section['active'] = 'true';
                    }
                    if( !empty( $section['icon'] ) ){
                        $label = '<i class="dashicons ' . $section['icon'] . '"></i><span class="label">' . esc_html( $section['label'] ) . '</span>';
                    }
                    echo '<li aria-selected="' . esc_attr( (string) $section['active'] ) . '">';
                        echo '<a href="#' . esc_attr( $section_id ) . '" data-metabox="' . esc_attr( $slug ) . '" class="uix-tab-trigger">' . $label . '</a>';
                    echo '</li>';
                }
                echo '</ul>';
        }else{
            echo '<div class="uix-metabox-inside">';
        }

            echo '<div class="uix-metabox-sections">';
            foreach( $metabox['sections'] as $section_id=>$section ){
                uixv2()->sections->render( $section_id );
            }
            echo '</div>';
        echo '</div>';
    }

    /**
     * Sets the data for all sections and thier controls
     *
     * @since 2.0.0
     * @param string $slug slug of the metabox to set data for
     * @param object/wp_post $post Current post of the metabox
     */    
    public function set_data( $slug, $post ){

        $metabox = $this->get( $slug );
        $data = array();
        if( !empty( $metabox['sections'] ) ){
            foreach( $metabox['sections'] as $section_id => $section ){
                if( !empty( $section['controls'] ) ){
                    foreach( $section['controls'] as $control_id => $control ) {
                        $store_key = uixv2()->control[ $control['type'] ]->store_key( $control_id );
                        $data = get_post_meta( $post->ID, $store_key, true );
                        uixv2()->control[ $control['type'] ]->save_data( $control_id, $data );
                    }
                }
            }
        }

    }

    /**
     * Saves a metabox data
     *
     * @uses "save_post" hook
     * @since 2.0.0
     * @param int $post_id ID of the current post being saved
     * @param object/wp_post $post Current post being saved
     */
    public function save_meta( $post_id, $post ){
        $this->post = $post;
        $this->locate();
        if( empty( $this->active_slugs ) || empty( $_POST['uix'] ) ){
            return;
        }

        foreach ( $this->active_slugs as $slug) {            
            $uix = $this->get( $slug );
            $meta_data = $this->get_sections_data( $slug );
            foreach( $uix['sections'] as $section_id=>$section ){
                if( !isset( $meta_data[ $section_id ] ) ){ continue; }
                
                $data = $meta_data[ $section_id ];
                foreach( $data as $meta_key=>$meta_value ){

                    $this->save_meta_data( $meta_key, $meta_value );

                }

            }

        }

    }

    /**
     * Save the meta data for the post
     *
     * @since 2.0.0
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
     * @param string $slug The slug of the metabox to get sections data for
     */
    public function get_sections_data( $slug ){
        
        $metabox = $this->get( $slug );
        $data = array();
        if( !empty( $metabox['sections'] ) ){
            foreach( $metabox['sections'] as $section_id => $section ){
                $data[ $section_id ] = uixv2()->sections->get_data( $section_id );
            }
        }
        return $data;
    }

    /**
     * sets the active metabox and all it's sections
     *
     * @since 2.0.0
     * @param $slug     Slug of the metabox to set as active
     */
    public function set_active( $slug ){
        if( !in_array( $slug, $this->active_slugs ) ){
            $metabox = $this->get( $slug );
            if( !empty( $metabox['sections'] ) ){
                $sections = array_keys( $metabox['sections'] );
                foreach( $sections as $section_id ) {
                    uixv2()->sections->set_active( $section_id );
                }
            }
            $this->active_slugs[] = $slug;
        }
    }

    /**
     * Determin which metaboxes are used for the current screen and set them active
     * @since 2.0.0
     */
    protected function locate(){

        $slugs = array();
        if( !empty( $this->post ) ){
            // post already loaded just find slugs that match this type
            foreach( (array) $this->objects as $slug=>$object ) {
                if( empty( $object['screen'] ) || in_array( $this->post->post_type, (array) $object['screen'] ) ){
                    $this->set_active( $slug );
                }
            }

        }else{
            if( function_exists( 'get_current_screen' ) ){
                global $post;
                // check that the screen object is valid to be safe.
                $screen = get_current_screen();
                if( empty( $screen ) || !is_object( $screen ) || $screen->base !== 'post' ){
                    return;
                }

                foreach( (array) $this->objects as $slug=>$object ) {
                    if( !empty( $screen->post_type ) && ( empty( $object['screen'] ) || ( is_array( $object['screen'] ) && in_array( $screen->id, $object['screen'] ) ) ) ){
                        $this->set_active( strip_tags( $slug ) );
                    }
                }
                // set current post
                $this->post = $post;
            }
        }        
    }
}