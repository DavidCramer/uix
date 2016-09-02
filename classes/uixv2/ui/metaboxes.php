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
class metaboxes extends uix implements \uixv2\data\save,\uixv2\data\load {

    /**
     * The type of object
     *
     * @since 1.0.0
     *
     * @var      string
     */
    protected $type = 'metabox';

    /**
     * Holds the current instance
     *
     * @since 1.0.0
     *
     * @var      object|\uix\metabox
     */
    private static $instance = null;

    /**
     * Holds the current post object
     *
     * @since 1.0.0
     *
     * @var      object|WP_Post
     */
    public $post = null;

    /**
     * Return an instance of this class.
     *
     * @since 1.0.0
     *
     * @return    object|\uix\metabox    A single instance of pages
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
     * Register the UIX objects
     *
     * @since 1.0.0
     *
     * @param array $objects object structure array
     */
    public function set_objects( array $objects ) {
        
        parent::set_objects( $objects );

        foreach( $this->objects as $object_id => &$object ){
            
            if( empty( $object['sections'] ) ){ continue; }

            foreach( $object['sections'] as $section_slug => &$section ){
                $section['metabox'] = $object_id;
            }
            uixv2()->load( 'sections', $object['sections'] );
        }
    }

    /**
     * Add metaboxes
     *
     * @since 0.0.1
     *
     * @uses "add_meta_boxes" hook
     */
    public function add_metaboxes(){

        $this->locate();

        if( empty( $this->active_slugs ) ){
            return;
        }
        // Add metabox style
        $styles = array(
            'metabox'        =>  $this->url . 'assets/css/metabox' . $this->debug_styles . '.css'
        );
        $this->styles( $styles );

        $scripts = array(
            'metabox'        =>  $this->url . 'assets/js/uix-metaboxes' . $this->debug_scripts . '.js'
        );
        $this->scripts( $scripts );

        // post type metaboxes
        $configs = array();
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
     * render metabox
     *
     * @since 1.0.0
     *
     */
    public function create_metabox( $post, $metabox ){

        $slug = $metabox['id'];
        $uix = $this->get( $slug );
        
        $this->set_data( $slug, $post );

        $this->render( $slug );

    }


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
     * @since 1.0.0
     *
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
     * Returns a list of sections for a metabox
     *
     * @since 0.0.1
     * @param uix/section $sections of the metabox
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
     * Saves a metabox config
     *
     * @uses "save_post" hook
     *
     * @since 0.0.1
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

                    $this->save_data( $meta_key, $meta_value );

                }

            }

        }

    }

    /**
     * save data
     *
     * @since 1.0.0
     * @param string $slug slug of the object
     * @param array $data array of data to be saved
     *
     * @return bool true on successful save
     */
    public function save_data( $slug, $data ){

        $prev = get_post_meta( $this->post->ID, $slug, true );

        if ( null === $data && $prev ){
            delete_post_meta( $this->post->ID, $slug );
        }elseif ( $data !== $prev ) {
            update_post_meta( $this->post->ID, $slug, $data );
        }    

    }


    /**
     * get the objects data store key
     * @since 1.0.0
     *
     * @return string $store_key the defined option name for this UIX object
     */
    public function store_key( $slug ){
        return sanitize_key( $slug );
    }

    /**
     * Loads a UIX config
     * @since 1.0.0
     *
     * @return mixed $data the saved data fro the specific UIX object
     */
    public function get_data( $slug ){

        $store_key = $this->store_key( $slug );

        $data = $this->get_sections_data( $slug );

        /**
         * Filter config object
         *
         * @param array $config_object The object as retrieved from data
         * @param array $uix the UIX structure
         * @param array $slug the UIX object slug
         */
        return apply_filters( 'uix_data-' . $this->type, $data, $slug );

    }

    /**
     * Get data for a section
     * @since 1.0.0
     *
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
     * sets the active objects structures
     *
     * @since 1.0.0
     *
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
     * Determin if a UIX object should be loaded for this screen
     * Intended to be ovveridden
     * @since 0.0.1
     *
     * @return array $array of slugs of a registered structures relating to this screen
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