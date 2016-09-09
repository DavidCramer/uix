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
 * textarea / paragraph input
 *
 * @since 1.0.0
 */
class textarea extends \uix\ui\control{

    /**
     * The type of object
     *
     * @since       1.0.0
     * @access public
     * @var         string
     */
    public $type = 'textarea';

    /**
     * Gets the attributes for the control.
     *
     * @since  1.0.0
     * @access public
     * @return array
     */
    public function attributes() {

        $attributes             = parent::attributes();
        $attributes['rows']     = '5';
        $attributes['class']    = 'widefat';
        
        if( !empty( $this->struct['rows'] ) ){
            $attributes['rows'] = $this->struct['rows'];
        }

        return $attributes;
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

        return '<' . esc_html( $this->type ) . ' ' . $this->build_attributes() . '>' . esc_textarea( $this->get_data() ) . '</' . esc_html( $this->type ) . '>';
    }    

}