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
 * @since       2.0.0
 */
class controls extends uix implements \uixv2\data\save,\uixv2\data\load{

    /**
     * The type of object
     *
     * @since       2.0.0
     *
     * @var         string
     */
    protected $type = 'control';

    /**
     * Temporary hold the control values
     *
     * @since   2.0.0
     *
     * @var     array
     */
    protected $data = array();

    /**
     * Register the UIX objects
     *
     * @since 2.0.0
     *
     * @param array $objects object structure array
     */
    public function set_objects( array $objects ) {

        parent::set_objects( $objects );

        foreach( $objects as $slug=>$object ){
            if( !empty( $object['sanitize_callback'] ) ){
                add_filter( 'uix_' . $object['section'] . '_sanitize_' . $slug, $object['sanitize_callback'] );
            }
        }

    }

    /**
     * get the objects data store key
     * @since 2.0.0
     *
     * @return string $store_key the defined option name for this UIX object
     */
    public function store_key( $slug ){
        return sanitize_key( $slug );
    }    

    /**
     * Get control name
     *
     * @since 2.0.0
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
     * @since 2.0.0
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
     * @since 2.0.0
     *
     * @param string $slug
     * @return array of data
     */
    public function get_data( $slug ){
        
        // get store key
        $store_key = $this->store_key( $slug );
        $control = $this->get( $slug );
        // if data was recently set, return it
        if( isset( $this->data[ $slug ] ) )
            return $this->data[ $slug ];
        // has data been submitted
        $value = null;

        if( isset( $_POST['uix'][ $control['section'] ][ $store_key ] ) ){
            $value = $_POST['uix'][ $control['section'] ][ $store_key ];
        }else{
            if( isset( $control['value'] ) )
                $value = $control['value'];
        }
        return $this->sanitize( $slug, $value );
    }

    /**
     * save data
     *
     * @since 2.0.0
     * @param string $slug slug of the object
     * @param array $data array of data to be saved
     *
     */
    public function save_data( $slug, $data ){

        $store_key = $this->store_key( $slug );
        $data = $this->sanitize( $slug, $data );

        $this->data[ $store_key ] = $data;
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

        return apply_filters( 'uix_' . $control['section'] . '_sanitize_' . $slug, $data );
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

}