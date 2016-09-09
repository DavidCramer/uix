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
 * Standard text input field
 *
 * @since 1.0.0
 */
class text extends \uix\ui\control {
    
    /**
     * The type of object
     *
     * @since       1.0.0
     * @access public
     * @var         string
     */
    public $type = 'text';

    /**
     * Gets the classes for the control input
     *
     * @since  1.0.0
     * @access public
     * @return array
     */
    public function classes() {

        $classes = array(
            'regular-text'
        );

        return $classes;
    }

}