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
class select extends \uixv2\ui\controls{

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
                <select class="widefat" name="<?php echo esc_attr( $this->name( $slug ) ); ?>">
                <?php 
                    if( !isset( $control['value'] ) ){
                        echo '<option></option>';
                    }
                    foreach ($control['choices'] as $option_value => $option_label) {
                        $sel = null;
                        if( option_value == $value )
                            $sel = ' selected="selected"';

                        echo '<option value="' . esc_attr( $option_value ) . '"' . $sel . '>' . esc_html( $option_label ) . '</option>';
                    }
                ?>
                </select>
                <?php if( !empty( $control['description'] ) ){ ?>
                    <span class="uix-control-description"><?php echo esc_html( $control['description'] ); ?></span>
                <?php } ?>
            </label>
        </div>
        <?php
        }
    }    

}