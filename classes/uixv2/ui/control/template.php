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
class template extends \uixv2\ui\controls{

    public function render( $slug ){
        $control = $this->get( $slug );
        $value = $this->get_data( $slug );
        if( !empty( $control['template'] ) && file_exists( $control['template'] ) ){ ?>
            <div id="control-<?php echo esc_attr( $slug ); ?>" class="uix-control uix-control-<?php echo esc_attr( $control['type'] ); ?>">
            <?php include $control['template']; ?>
            </div>
        <?php }
    }    
     

}