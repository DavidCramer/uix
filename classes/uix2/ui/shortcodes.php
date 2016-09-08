<?php
/**
 * UIX Shortcode
 *
 * @package   uix2
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix2\ui;

/**
 * Shortcodes class
 * @package uix2\ui
 * @author  David Cramer
 */
class shortcodes extends \uix2\data\localized{

    /**
     * The type of object
     *
     * @since 1.0.0
     *
     * @var      string
     */
    public $type = 'shortcode';

    /**
     * found shortcode args
     *
     * @since 1.0.0
     *
     * @var      array
     */
    protected $args = array();

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
        
        // set action head to front head
        add_action( 'wp_head', array( $this, 'enqueue_core' ) );        
    }

    /**
     * Register the UIX shortcodes
     *
     * @since 1.0.0
     *
     * @param array $objects object structure array
     */
    public static function register( array $objects ) {

        // do parent
        $uix = parent::register( $objects );
        
        foreach( $objects as $slug=>$def ){
            add_shortcode( $slug, array( $uix, 'render_shortcode') );
        }
        // remove admin styles
        unset( $uix->styles['styles'] );

        return $uix;
    }

    /**
     * render shortcode
     *
     * @since 1.0.0
     *
     */
    public function render_shortcode( $args, $content, $slug ){

        $uix = $this->get( $slug );

        if( !empty( $uix['base_color'] ) ){
            $text_color = "#fff";
            if( !empty( $uix['base_text_color'] ) ){
                $text_color = $uix['base_text_color'];
            }
        ?><style type="text/css">.uix-modal-title .uix-modal-closer{ color: <?php echo $text_color; ?>; }.uix-modal-wrap .uix-modal-title > h3,.wrap a.page-title-action:hover{background: <?php echo $uix['base_color']; ?>; color: <?php echo $text_color; ?>;}</style>
        <?php
        }
        ?>
        <div class="uix-item" data-uix="<?php echo esc_attr( $slug ); ?>">
            <div class="uix-tab-canvas" style="display:block;" data-app="<?php echo esc_attr( $slug ); ?>"></div>
        </div>
        <script type="text/html" data-template="<?php echo esc_attr( $slug ); ?>">
            <?php 
                if( !empty( $uix['template'] ) && file_exists( $uix['template'] ) ){
                    include $uix['template'];
                }else{
                    echo esc_html__( 'Template not found: ', 'text-domain' ) . $uix['template'];
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
                            echo esc_html__( 'Partial Template not found: ', 'text-domain' ) . $partial_id;
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
                            echo esc_html__( 'Modal Template not found: ', 'text-domain' ) . $modal_id;
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
                <?php esc_html_e( 'Save Changes', 'text-domain' ); ?>
            </button>
        </script>
        <script type="text/html" id="__partial_create">
            <button class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="add" 
                {{#if __callback}}data-callback="{{__callback}}"{{/if}}
                {{#if __before}}data-before="{{__before}}"{{/if}}
            >
                <?php esc_html_e( 'Create', 'text-domain' ); ?>
            </button>
        </script>
        <script type="text/html" id="__partial_delete">
            <button style="float:left;" class="button" type="button" data-modal-node="{{__node_path}}" data-app="{{__app}}" data-type="delete" 
                {{#if __callback}}data-callback="{{__callback}}"{{/if}}
                {{#if __before}}data-before="{{__before}}"{{/if}}
            >
                <?php esc_html_e( 'Remove', 'text-domain' ); ?>
            </button>
        </script>

        <?php
        
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
        $config_object[ $slug ] = $this->args[ $slug ];

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
        if( is_admin() ){ return array(); }
        
        global $post;

        $slugs = array();

        $pattern = get_shortcode_regex();
        preg_match_all( '/'. $pattern .'/s', $post->post_content, $found);

        if( empty( $found ) || empty( $found[2] ) ){
            return $slugs;
        }

        foreach( (array) $found[2] as $index=>$slug ) {

            $args = array();
            if( !empty( $found[3][ $index ] ) ){
                $args = shortcode_parse_atts( $found[3][ $index ] );
            }
            $this->args[ $slug ] = $args;

            if( !empty( $this->objects[ $slug ] ) ){
                $slugs[] = strip_tags( $slug ); 
            }
        }

        return $slugs;
    }
}