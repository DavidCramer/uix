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

    /**
     * The type of object
     *
     * @since       2.0.0
     *
     * @var         string
     */
    protected $type = 'select';


    /**
     * Gets the classes for the control input
     *
     * @since  2.0.0
     *
     * @return array
     */
    public function classes() {

        $classes = array( 
            'select-field'
        );
        
        return $classes;
    }

    /**
     * Returns the main input field for rendering
     *
     * @since 2.0.0
     * @see \uixv2\ui\uix
     * @param string $slug Control slug to be rendered
     * @return string 
     */
    public function input( $slug ){
        
        $control    = $this->get( $slug );
        $input      = '<' . esc_html( $this->type ) . ' ' . $this->build_attributes( $slug ) . '>';
        $value      = $this->get_data( $slug );

        if( !isset( $control['value'] ) ){
            $input .= '<option></option>';
        }

        foreach ($control['choices'] as $option_value => $option_label) {
            $sel = null;
            if( $option_value == $value )
                $sel = ' selected="selected"';

            $input .= '<option value="' . esc_attr( $option_value ) . '"' . $sel . '>' . esc_html( $option_label ) . '</option>';
        }
        $input .= '</' . esc_html( $this->type ) . '>';

        return $input;
    }  

}