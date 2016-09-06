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
class editor extends \uix2\ui\control\textarea{

    /**
     * Returns the main input field for rendering
     *
     * @since 2.0.0
     * @see \uix2\ui\uix
     * @access public 
     */
    public function input(){

        $settings = array( 'textarea_name' => $this->name() );
        if( !empty( $this->struct['settings'] ) && is_array( $this->struct['settings'] ) )
            $settings = array_merge( $this->struct['settings'], $settings );

        wp_editor( $this->get_data(), 'control-' . $this->id(), $settings );

    }    

}