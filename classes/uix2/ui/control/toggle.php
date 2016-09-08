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
class toggle extends \uix2\ui\control{
    
    /**
     * The type of object
     *
     * @since       2.0.0
     * @access public
     * @var         string
     */
    public $type = 'toggle';

    /**
     * Gets the classes for the control input
     *
     * @since  2.0.0
     * @access public
     * @return array
     */
    public function classes() {

        $classes = array( 
            'toggle-checkbox'
        );
        
        return $classes;
    }

    /**
     * Define core UIX styles - override to register core ( common styles for uix type )
     *
     * @since 2.0.0
     * @access public
     */
    public function uix_styles() {

        parent::uix_styles();
        // Initilize core styles
        $styles = array(
            'toggle' => $this->url . 'assets/controls/toggle/css/toggle' . $this->debug_scripts . '.css',
        );
        // push to activly register styles
        $this->styles( $styles );

    }

    /**
     * Define core UIX scripts - override to register core ( common scripts for uix type )
     *
     * @since 2.0.0
     * @access public
     */
    public function uix_scripts() {
        // Initilize core scripts
        $scripts = array(
            'toggle-control-init'   => array(
                "src"       => $this->url . 'assets/controls/toggle/js/toggle' . $this->debug_scripts . '.js',
                "in_footer" => true
            )
        );
        // push to activly register scripts
        $this->scripts( $scripts );
    }


    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 2.0.0
     * @access protected
     */
    protected function enqueue_active_assets(){
        parent::enqueue_active_assets();
        ?><style type="text/css">
        #<?php echo $this->id(); ?> > .switch.active {
            background: <?php echo $this->base_color(); ?>;
        }
        <?php if( !empty( $this->struct['off_color'] ) ){ ?>
            #<?php echo $this->id(); ?> > .switch {
                background: <?php echo $this->struct['off_color']; ?>;
            }
        <?php } ?>
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
        
        $value          = $this->get_data();
        $status_class   = '';
        if( !empty( $value ) )
            $status_class = ' active';        

        $input = '<label class="switch setting_toggle_alert' . esc_attr( $status_class ) . '" data-for="control-' . esc_attr( $this->id() ) . '">';
            $input .= '<input type="checkbox" value="1" ' . $this->build_attributes() . ' ' . checked( 1, $value, false ) . '>';
            $input .= '<div class="box"></div>';
        $input .= '</label>';

        return $input;
    }  

    /**
     * Returns the description for the control
     *
     * @since 2.0.0
     * @access public
     * @return string description string 
     */
    public function description(){
        
        if( isset( $this->struct['description'] ) )
            return '<span class="uix-toggle-description">' . esc_html( $this->struct['description'] ) . '</span>';

        return '';
    }

}