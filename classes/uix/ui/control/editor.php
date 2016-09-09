<?php
/**
 * UIX Control
 *
 * @package   controls
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
namespace uix\ui\control;

/**
 * WordPress Content Editor 
 *
 * @since 1.0.0
 */
class editor extends \uix\ui\control\textarea {

    /**
     * Returns the main input field for rendering
     *
     * @since 1.0.0
     * @see \uix\ui\uix
     * @access public 
     */
    public function input() {

        $settings = array( 'textarea_name' => $this->name() );
        if ( ! empty( $this->struct[ 'settings' ] ) && is_array( $this->struct[ 'settings' ] ) )
            $settings = array_merge( $this->struct[ 'settings' ], $settings );

        wp_editor( $this->get_data(), 'control-' . $this->id(), $settings );

    }    

}