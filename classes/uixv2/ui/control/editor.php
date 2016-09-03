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
class editor extends \uixv2\ui\control\textarea{

    /**
     * Returns the main input field for rendering
     *
     * @since 2.0.0
     * @see \uixv2\ui\uix
     * @param string $slug Control slug to be rendered
     * @return string 
     */
    public function input( $slug ){

        $control = $this->get( $slug );
        $settings = array( 'textarea_name' => $this->name( $slug ) );
        if( !empty( $control['settings'] ) && is_array( $control['settings'] ) )
            $settings = array_merge( $control['settings'], $settings );

        wp_editor( $this->get_data( $slug ), $this->id( $slug ), $settings );

    }    

}