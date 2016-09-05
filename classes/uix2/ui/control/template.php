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
class template extends \uix2\ui\control{


    /**
     * Returns the main input field for rendering
     *
     * @since 2.0.0
     * @see \uix2\ui\uix
     * @access public
     * @return string 
     */
    public function input(){
        
        if( !empty( $this->struct['template'] ) && file_exists( $this->struct['template'] ) )
            include $this->struct['template'];
    }    
     

}