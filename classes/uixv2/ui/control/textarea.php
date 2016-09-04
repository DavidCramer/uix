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
class textarea extends \uixv2\ui\control{

    /**
     * The type of object
     *
     * @since       2.0.0
     * 
     * @var         string
     */
    public $type = 'textarea';

    /**
     * Gets the attributes for the control.
     *
     * @since  2.0.0
     * @access private
     * @param string $slug Slug of the control 
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
     * @see \uixv2\ui\uix
     * @param string $slug Control slug to be rendered
     * @return string 
     */
    public function input(){

        return '<' . esc_html( $this->type ) . ' ' . $this->build_attributes() . '>' . esc_textarea( $this->get_data() ) . '</' . esc_html( $this->type ) . '>';
    }    

}