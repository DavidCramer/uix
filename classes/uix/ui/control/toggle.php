<?php
/**
 * UIX Controls
 *
 * @package   controls
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui\control;

/**
 * Pretty little on/off type toggle switch 
 *
 * @since 1.0.0
 */
class toggle extends \uix\ui\control{
    
    /**
     * The type of object
     *
     * @since       1.0.0
     * @access public
     * @var         string
     */
    public $type = 'toggle';

    /**
     * Gets the classes for the control input
     *
     * @since  1.0.0
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
     * @since 1.0.0
     * @access public
     */
    public function set_assets() {

        // Initilize core styles
        $this->assets['style']['toggle']    = $this->url . 'assets/controls/toggle/css/toggle' . UIX_ASSET_DEBUG . '.css';

        // Initilize core scripts
        $this->assets['script']['toggle-control-init']  = array(
            "src"       => $this->url . 'assets/controls/toggle/js/toggle' . UIX_ASSET_DEBUG . '.js',
            "in_footer" => true
        );

        parent::set_assets();
    }


    /**
     * Enqueues specific tabs assets for the active pages
     *
     * @since 1.0.0
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
     * Gets the attributes for the control.
     *
     * @since  1.0.0
     * @access public
     */
    public function set_attributes() {

        parent::set_attributes();
        if( !empty( $this->struct['toggle_all'] ) )
            $this->attributes['data-toggle-all'] = 'true';

    }

    /**
     * Returns the main input field for rendering
     *
     * @since 1.0.0
     * @see \uix\ui\uix
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
     * @since 1.0.0
     * @access public
     * @return string description string 
     */
    public function description(){
        
        if( isset( $this->struct['description'] ) )
            return '<span class="uix-toggle-description">' . esc_html( $this->struct['description'] ) . '</span>';

        return '';
    }

}