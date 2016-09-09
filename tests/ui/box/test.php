<?php
/**
 * Tests the box object
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */
$box = array(
    'saving'    => array(
        'label'     =>  'Saving',
        'control'   =>  array(
            'checkbox'  =>  array(
                'label' => 'text input',
                'description' => 'text input description',
                'type'  =>  'checkbox',
                'value' => 'one',
                'choices' => array(
                    'one',
                    'two' => 'two'
                )
            ),
            'radio'  =>  array(
                'label' => 'radio input',
                'type'  =>  'radio',
                'value' => 'one',
                'choices' => array(
                    'one',
                    'two' => 'two'
                )
            ),
            'select'  =>  array(
                'label' => 'select input',
                'type'  =>  'select',
                'value' => 'one',
                'choices' => array(
                    'one',
                    'two' => 'two'
                )
            ),
            'separator'  =>  array(
                'label' => 'separator',
                'description' => 'description',
                'type'  =>  'separator',
                'base_color' => '#ff00aa',
            ),
            'slider'  =>  array(
                'label' => 'slider',
                'type'  =>  'slider'
            ),
            'template'  =>  array(
                'label' => 'template',
                'type'  =>  'template',
                'template' => 'include_template.php',
            ),
            'file'  =>  array(
                'label' => 'file input',
                'type'  =>  'file'
            ),
            'hidden'  =>  array(
                'label' => 'hidden input',
                'type'  =>  'hidden'
            ),
            'number'  =>  array(
                'label' => 'number input',
                'type'  =>  'number'
            ),
            'text'  =>  array(
                'label' => 'text input',
                'type'  =>  'text'
            ),
            'textarea'  =>  array(
                'label' => 'testarea input',
                'type'  =>  'textarea',
                'attributes' => array(
                    'data-tested' => 'true'
                )
            ),
            'toggle'  =>  array(
                'label' => 'toggle input',
                'type'  =>  'toggle',
                'toggle_all' => 'true',
                'base_color' => '#ff00aa',
            ),
        )
    )
);

return $box;