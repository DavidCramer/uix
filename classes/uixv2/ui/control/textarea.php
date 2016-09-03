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
class textarea extends \uixv2\ui\controls{

    /**
     * The type of object
     *
     * @since       2.0.0
     *
     * @var         string
     */
    protected $type = 'textarea';

    /**
     * Gets the attributes for the control.
     *
     * @since  2.0.0
     * @access private
     * @param string $slug Slug of the control 
     * @return array
     */
    public function attributes( $slug ) {

        $attributes         = parent::attributes( $slug );
        $attributes['rows'] = '5';
        
        $control = $this->get( $slug );
        if( !empty( $control['rows'] ) ){
            $attributes['rows'] = $control['rows'];
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
    public function input( $slug ){

        return '<' . esc_html( $this->type ) . ' ' . $this->build_attributes( $slug ) . '>' . esc_textarea( $this->get_data( $slug ) ) . '</' . esc_html( $this->type ) . '>';
    }    

}