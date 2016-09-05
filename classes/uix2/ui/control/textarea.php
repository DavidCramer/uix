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
class textarea extends \uix2\ui\control{

    /**
     * The type of object
     *
     * @since       2.0.0
     * @access public
     * @var         string
     */
    public $type = 'textarea';

    /**
     * Gets the attributes for the control.
     *
     * @since  2.0.0
     * @access public
     * @return array
     */
    public function attributes() {

        $attributes         = parent::attributes();
        $attributes['rows'] = '5';
        
        if( !empty( $this->struct['rows'] ) ){
            $attributes['rows'] = $this->struct['rows'];
        }

        return $attributes;
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

        return '<' . esc_html( $this->type ) . ' ' . $this->build_attributes() . '>' . esc_textarea( $this->get_data() ) . '</' . esc_html( $this->type ) . '>';
    }    

}