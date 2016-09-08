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
namespace uix2\ui\control;

/**
 * UIX Control class.
 *
 * @since 2.0.0
 */
class separator extends \uix2\ui\control{
    
    /**
     * The type of object
     *
     * @since       2.0.0
     * @access public
     * @var         string
     */
    public $type = 'separator';

    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 2.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){
        parent::enqueue_active_assets();
        ?><style type="text/css">
        #control-<?php echo $this->id(); ?> {
            border-color: <?php echo $this->base_color(); ?>;
        }
        #<?php echo $this->id(); ?> span.uix-control-label {
            color: <?php echo $this->base_color(); ?>;
        }
        </style>
        <?php
    }

    /**
     * Returns the main input field for rendering
     *
     * @since 2.0.0
     * @see \uix2\ui\uix
     * @access public
     * @return string 
     */
    public function input(){

        echo '<hr class="uix-separator" id="control-' . esc_attr( $this->id() ) . '" />';

    }

}