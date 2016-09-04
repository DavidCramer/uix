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
class checkbox extends \uixv2\ui\control\radio{

    /**
     * The type of object
     *
     * @since       2.0.0
     * @access public
     * @var         string
     */
    public $type = 'checkbox';


    /**
     * Gets the attributes for the control.
     *
     * @since  2.0.0
     * @access public
     * @return array
     */
    public function attributes() {

        $attributes         = parent::attributes();
        $attributes['name'] .= '[]';

        return $attributes;
    }

}