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
namespace uix\ui;

/**
 * Base UIX Control class.
 *
 * @since       1.0.0
 */
class control extends \uix\data\data{

    /**
     * The type of object
     *
     * @since       1.0.0
     * @access public
     * @var         string
     */
    public $type = 'control';

    /**
     * Register the UIX objects
     *
     * @since 1.0.0
     * @access public
     * @param string $slug Object slug
     * @param array $object object structure array
     * @return object|\uix object instance
     */
    public static function register( $slug, $object, $parent = null ) {

        $caller = get_called_class();
        // get the current instance
        if( empty( $object['type'] ) || !uix()->is_callable( 'control\\' . $object['type'] ) )
            $object['type'] = 'text';

        $caller = $caller . '\\' . $object['type'];
        return new $caller( $slug, $object, $parent );

    }

    /**
     * Sets the controls data
     *
     * @since 1.0.0
     * @see \uix\uix
     * @access public
     */
    public function setup() {
        // run parents to setup sanitization filters
        parent::setup();
        $data = uix()->request_vars( 'post' );

        $this->set_data('');

        if( !empty( $this->struct['value'] ) )
            $this->set_data( $this->struct['value'] );

        if( isset( $data[ $this->id() ] ) )
            $this->set_data( $data[ $this->id() ] );

        // base attributes defined
        $this->attributes['name']      =  $this->name();
        $this->attributes['id']        =  $this->id() . '-control';

    }

    /**
     * Handy method for setting data-* attributes using the setup parameter
     * @since  1.0.0
     * @access public
     */
    public function set_config() {

        foreach( $this->struct['config'] as $key=>$setting )
            $this->attributes['data-' . $key ] = $setting;

    }



    /**
     * Gets the attributes for the control.
     *
     * @since  1.0.0
     * @access public
     * @return array Attributes for the input field
     */
    public function set_attributes() {

        if( !empty( $this->struct['config'] ) )
            $this->set_config();

        $this->attributes['class']     =  implode( ' ', $this->classes() );

        parent::set_attributes();

    }

    /**
     * Define core page styles
     *
     * @since 1.0.0
     * @access public
     */
    public function set_assets() {
        $this->assets['style']['controls']  =   $this->url . 'assets/css/control' . UIX_ASSET_DEBUG . '.css';
        parent::set_assets();
    }

    /**
     * Create and Return the control's input name
     *
     * @since 1.0.0
     * @access public
     * @return string The control name
     */
    public function name(){
        return $this->id();
    }


    /**
     * Gets the classes for the control input
     *
     * @since  1.0.0
     * @access public
     * @return array
     */
    public function classes() {

        return array(
            'widefat'
        );

    }


    /**
     * Returns the main input field for rendering
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string Input field HTML striung
     */
    public function input(){

        return '<input type="' . esc_attr( $this->type ) . '" value="' . esc_attr( $this->get_data() ) . '" ' . $this->build_attributes() . '>';
    }    

    /**
     * Returns the label for the control
     *
     * @since 1.0.0
     * @access public
     * @return string label of control
     */
    public function label(){
        $output = null;
        if( isset( $this->struct['label'] ) )
            $output .= '<label for="' . esc_attr( $this->id() ) . '-control"><span class="uix-control-label">' . esc_html( $this->struct['label'] ) . '</span></label>';

        return $output;
    }


    /**
     * Returns the description for the control
     *
     * @since 1.0.0
     * @access public
     * @return string description string 
     */
    public function description(){
        $output = null;
        if( isset( $this->struct['description'] ) )
            $output .= '<span class="uix-control-description">' . esc_html( $this->struct['description'] ) . '</span>';

        return $output;
    }


    /**
     * Render the Control
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string HTML of rendered control
     */
    public function render(){

        $output = '<div id="' . esc_attr( $this->id() ) . '" class="uix-control uix-control-' . esc_attr( $this->type ) . ' ' . esc_attr( $this->id() ) . '">';

        $output .= $this->label();
        $output .= '<div class="uix-control-input">';
            $output .= $this->input();
        $output .= '</div>';
        $output .= $this->description();

        $output .= '</div>';

        return $output;
    }

    /**
     * checks if the current control is active
     *
     * @since 1.0.0
     * @access public
     */
    public function is_active(){
        if( !empty( $this->parent ) )
            return $this->parent->is_active();

        return parent::is_active();
    }

}