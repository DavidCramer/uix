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
namespace uixv2\ui\control;

/**
 * UIX Control class.
 *
 * @since 2.0.0
 */
class textarea extends \uixv2\ui\controls{

    /**
     * sanitize data
     *
     * @since 1.0.0
     * @param string $slug slug of the object
     * @param array $data array of data to be saved
     *
     * @return string $data sanitized data string
     */
    public function sanitize( $slug, $data ){
        $control = $this->get( $slug );
        if( !empty( $control['sanitize_callback'] ) ){
            if( is_callable( $control['sanitize_callback'] ) ){
                return call_user_func( $control['sanitize_callback'], $data );
            }
        }
        return sanitize_textare( $data );
    }


    public function render( $slug ){
        $control = $this->get( $slug );
        $value = $this->get_data( $slug );

        if( !empty( $control['template'] ) && file_exists( $control['template'] ) ){
            include $control['template'];
        }else{
        ?>
        <div id="control-<?php echo esc_attr( $slug ); ?>" class="uix-control uix-control-text">
            <label>
                <?php if( !empty( $control['label'] ) ){ ?>
                    <span class="uix-control-label"><?php echo esc_html( $control['label'] ); ?></span>
                <?php } ?>
                <textarea class="widefat" name="<?php echo esc_attr( $this->name( $slug ) ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
                <?php if( !empty( $control['description'] ) ){ ?>
                    <span class="uix-control-description"><?php echo esc_html( $control['description'] ); ?></span>
                <?php } ?>
            </label>
        </div>
        <?php
        }
    }	 

}