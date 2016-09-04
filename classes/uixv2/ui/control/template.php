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
class template extends \uixv2\ui\control{


    /**
     * Returns the main input field for rendering
     *
     * @since 2.0.0
     * @see \uixv2\ui\uix
     * @access public
     * @return string 
     */
    public function input(){
        
        if( !empty( $this->struct['template'] ) && file_exists( $this->struct['template'] ) )
            include $this->struct['template'];
    }    
     

}