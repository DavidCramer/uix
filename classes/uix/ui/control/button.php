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
 * Button field
 *
 * @since 1.0.0
 */
class button extends template{
    
    /**
     * The type of object
     *
     * @since       1.0.0
     * @access public
     * @var         string
     */
    public $type = 'button';

    /**
     * Gets the classes for the control input
     *
     * @since  1.0.0
     * @access public
     * @return array
     */
    public function classes() {

        $classes = array(
            'button'
        );

        if( !empty( $this->struct['attributes']['class'] ) )
            $classes = (array) $this->struct['attributes']['class'];

        return $classes;
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

        return '<button ' . $this->build_attributes() . '>' . esc_html( $this->struct['label'] ) . '</button>';
    }




}