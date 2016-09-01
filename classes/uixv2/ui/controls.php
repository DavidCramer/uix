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
 * UIX Control class.
 *
 * @since 2.0.0
 */
class controls extends uix implements \uixv2\data\save,\uixv2\data\load{

    /**
     * The type of object
     *
     * @since 1.0.0
     *
     * @var      string
     */
    protected $type = 'control';

    /**
     * Holds the current post object
     *
     * @since 1.0.0
     *
     * @var      object|WP_Post
     */
    public $post = null;

    /**
     * Holds the current active control
     *
     * @since 1.0.0
     *
     * @var      string
     */
    public $current_active = null;

    /**
     * setup actions and hooks - ovveride to add specific hooks. use parent::actions() to keep admin head
     *
     * @since 1.0.0
     *
     */
    public function init() {
        global $post;
        $this->post = $post;
        parent::init();
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


        foreach( $objects as $slug=>$object ){
            if( !empty( $object['sanitize_callback'] ) ){
               // var_dump( uixv2() );//->metaboxes->get_section( $object['section'] ) );
            }
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
     * Get control name
     *
     * @since 1.0.0
     *
     * @param string $slug
     * @return array of data
     */
    public function name( $slug ){
        $control = $this->get( $slug );
        if( null === $control ){ return null; }
        return 'uix[' . $control['section'] . '][' . $slug . ']';
    }

    /**
     * Get control ID
     *
     * @since 1.0.0
     *
     * @param string $slug
     * @return array of data
     */
    public function id( $slug ){
        $control = $this->get( $slug );
        if( null === $control ){ return null; }
        return 'uix_' . $control['section'] . '-' . $slug;
    }


    /**
     * Get data
     *
     * @since 1.0.0
     *
     * @param string $slug
     * @return array of data
     */
    public function get_data( $slug ){
        
        $control = $this->get( $slug );
        $value = get_post_meta( $this->post->ID, $slug );
        if( empty( $value ) ){
            if( isset( $control['value'] ) ){
                $value[] = $control['value'];
            }else{
                $value[] = null;
            }
        }

        return $value[0];
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

        $meta_key = $this->store_key( $slug );
        $data = $this->sanitize( $slug, $data );
        if( null === $data ){
            delete_post_meta( $this->post->ID, $meta_key );
        }else{
            update_post_meta( $this->post->ID, $meta_key, $data );
        }
    }

    /**
     * Sanitize data
     *
     * @since 2.0.0
     * @param string $slug slug of the object
     * @param string $data data to be sanitized
     *
     * @return string $data  sanitized data string
     */
    public function sanitize( $slug, $data ){
        $control = $this->get( $slug );
        if( !empty( $control['sanitize_callback'] ) ){
            if( is_callable( $control['sanitize_callback'] ) ){
                return call_user_func( $control['sanitize_callback'], $data );
            }
        }
        return $data;
    }
    

    public function render( $slug ){
        $control = $this->get( $slug );
        $value = $this->get_data( $slug );

        ?>
        <div id="control-<?php echo esc_attr( $slug ); ?>" class="uix-control uix-control-<?php echo esc_attr( $control['type'] ); ?>">
            <label>
                <?php if( !empty( $control['label'] ) ){ ?>
                    <span class="uix-control-label"><?php echo esc_html( $control['label'] ); ?></span>
                <?php } ?>
                <input type="text" class="widefat" name="<?php echo esc_attr( $this->name( $slug ) ); ?>" value="<?php echo esc_attr( $value ); ?>">
                <?php if( !empty( $control['description'] ) ){ ?>
                    <span class="uix-control-description"><?php echo esc_html( $control['description'] ); ?></span>
                <?php } ?>
            </label>
        </div>
        <?php
    }

    /**
     * Get data
     *
     * @since 1.0.0
     *
     * @param string $slug
     * @return array of data
     */
    public function get( $slug ){
        global $post;
        $this->post = $post;
        return parent::get( $slug );
    }

}