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
 * Dropdown select
 *
 * @since 1.0.0
 */
class select extends \uix\ui\control {

    /**
     * The type of object
     *
     * @since       1.0.0
     * @access public
     * @var         string
     */
    public $type = 'select';


    /**
     * Gets the classes for the control input
     *
     * @since  1.0.0
     * @access public
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
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public
     * @return string 
     */
    public function input() {
        
        $input      = '<' . esc_html( $this->type ) . ' ' . $this->build_attributes() . '>';
        $value      = $this->get_data();

        if ( ! isset( $this->struct[ 'value' ] ) ) {
            $input .= '<option></option>';
        }
        if ( ! empty( $this->struct[ 'choices' ] ) ) {
            foreach ( $this->struct[ 'choices' ] as $option_value => $option_label ) {
                $sel = null;
                if ( $option_value == $value )
                    $sel = ' selected="selected"';

                $input .= '<option value="' . esc_attr( $option_value ) . '"' . $sel . '>' . esc_html( $option_label ) . '</option>';
            }
        }
        $input .= '</' . esc_html( $this->type ) . '>';

        return $input;
    }  

}