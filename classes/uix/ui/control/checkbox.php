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
 * Checkbox Fields group
 *
 * @since 1.0.0
 */
class checkbox extends \uix\ui\control\radio {

    /**
     * The type of object
     *
     * @since       1.0.0
     * @access public
     * @var         string
     */
    public $type = 'checkbox';


    /**
     * Gets the attributes for the control.
     *
     * @since  1.0.0
     * @access public
     * @return array
     */
    public function attributes() {

        $attributes = parent::attributes();
        $attributes[ 'name' ] .= '[]';

        return $attributes;
    }

}