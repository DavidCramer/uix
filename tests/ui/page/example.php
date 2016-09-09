<?php
/**
 * Example nested settings page
 *
 * @package   uix
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 David Cramer
 */

// This array is for the Admin Pages. each element defines a page that is seen in the admin
$pages = array(
    'uixdemo'       => array(                                                         // this is the settings array. The key is the page slug
        'page_title'    =>  __( 'UIX Demo Page', 'text-domain'),                        // title of the page
        'menu_title'    =>  __( 'UIX Demo', 'text-domain'),                        // title seen on the menu link
        'save_button'   =>  __( 'Save Changes', 'text-domain'),
        'capability'    =>  'manage_options',                                     // required capability to access page
        'icon'          =>  'dashicons-welcome-learn-more',                              // Icon or image to be used on admin menu
        'base_color'    =>  '#27ae60',
        'section'       =>  array(
            'general'       =>  array(
                'label'         =>  __( 'First Page', 'text-domain' ),
                'description'   =>  __( 'An example page with some controls.', 'text-domain' ),       
                'control'       =>  array(
                    'title'         =>  array(
                        'label'             =>  __( 'Title', 'text-domain' ),
                        'description'       =>  __( 'A single text field control', 'text-domain' ),
                        'type'              =>  'text',
                        'sanitize_callback' =>  'sanitize_text_field',     
                    ),
                    'options'       => array(
                        'label'             =>  __( 'Options', 'text-domain' ),
                        'description'       =>  __( 'A select fields with some options', 'text-domain' ),
                        'type'              =>  'select',
                        'choices'           =>  array(
                            'first_option'       =>  __( 'The first option', 'text-domain' ),
                            'second_option'      =>  __( 'Second choice', 'text-domain' ),
                            'third_option'       =>  __( 'Choice no 3', 'text-domain' ),
                            'fourth_option'      =>  __( '4th and Last', 'text-domain' ),
                        )
                    ),
                    'description'   => array(
                        'label'         =>  __( 'Description', 'text-domain' ),
                        'description'   =>  __( 'A textarea description block.', 'text-domain' ),
                        'type'          => 'textarea'
                    ),
                ), 
                
            ),            
            'controls'            => array(
                'label'             =>  __( 'Controls', 'text-domain' ),
                'description'       =>  __( 'A Few controls, with nested panels and sections.', 'text-domain' ),
                'panel'             =>  array(
                    'first_nested'      => array(
                        'section'           =>  array(
                            'settings'      => array(
                                'label'         =>  __( 'Settings', 'text-domain' ),
                                'description'   =>  __( 'Control settings with an icon.', 'text-domain' ),
                                'icon'          =>  'dashicons-admin-settings',
                                'control'       =>  array(
                                    'background'    =>  array(
                                        'label'         =>  __( 'Background Color', 'text-domain' ),
                                        'description'   =>  __( 'Select a color.', 'text-domain' ),
                                        'type'          =>  'color',
                                        'value'         =>  '#e74c3c',
                                    ),
                                    'text_color'    =>  array(
                                        'label'         =>  __( 'Text Color', 'text-domain' ),
                                        'description'   =>  __( 'Select a color for text.', 'text-domain' ),
                                        'type'          =>  'color',
                                        'value'         =>  '#333333',
                                    ),
                                ),
                            ),
                            'notifications'         => array(
                                'label'         =>  __( 'Notifications', 'text-domain' ),
                                'description'   =>  __( 'Another nested panel with an icon', 'text-domain' ),
                                'icon'          =>  'dashicons-format-chat',
                                'control'       => array(
                                    'notice_subject'    => array(
                                        'label'         =>  __( 'Subject', 'text-domain' ),
                                        'description'   =>  __( 'Set a subject line.', 'text-domain' ),
                                        'type'          => 'text',
                                    )
                                ),
                            ),
                            'extended'              =>  array(
                                'label'                 =>  __( 'Nested', 'text-domain' ),
                                'icon'                  =>  'dashicons-menu',
                                'panel'                 =>  array(
                                    'extended_panel'        =>  array(
                                        'top_tabs'              =>  true,
                                        'base_color'            =>  '#e67e22',   
                                        'section'               =>  array(
                                            'nested_section'        =>  array(
                                                'label'                 =>  __( 'Section One', 'text-domain' ),
                                                'description'           =>  __( 'A Nested section in a panel.', 'text-domain' ),                                                
                                                'control'               => array(
                                                    'nested_editor'         => array(
                                                        'type'                  =>  'editor',
                                                    ),
                                                ),
                                            ),
                                            'second_nested'     =>  array(
                                                'label'             =>  __( 'Section Two', 'text-domain' ),
                                                'description'       =>  __( 'A Two Nested Section.', 'text-domain' ),        
                                                'panel'           =>  array(
                                                    'uber_nested'        =>  array(
                                                        'base_color'            =>  '#8e44ad',
                                                        'section'               =>  array(
                                                            'uber_nested_section'       =>  array(
                                                                'label'                 =>  __( 'Section One', 'text-domain' ),
                                                                'description'           =>  __( 'A Nested section in a panel.', 'text-domain' ),                                                
                                                                'control'               => array(
                                                                    'nested_editor'         => array(
                                                                        'type'                  =>  'editor',
                                                                    ),
                                                                ),
                                                            ),
                                                            'uber_second_nested'    =>  array(
                                                                'label'             =>  __( 'Section Two', 'text-domain' ),
                                                                'description'       =>  __( 'A Two Nested Section.', 'text-domain' ),        
                                                                'control'           =>  array(
                                                                    'uber_second_email'  => array(
                                                                        'label'             =>  __( 'Email', 'text-domain' ),
                                                                        'type'              =>  'email',
                                                                        'sanitize_callback' =>  'sanitize_email',
                                                                    ), 
                                                                    'uber_second_age'    => array(
                                                                        'label'             =>  __( 'Age', 'text-domain' ),
                                                                        'type'              =>  'number',
                                                                    ), 
                                                                ),
                                                            ),
                                                            'third_nested_uber'   =>  array(
                                                                'label'             =>  __( 'Section Three', 'text-domain' ),
                                                                'description'       =>  __( 'A Three Nested Section.', 'text-domain' ),        
                                                                'control'           =>  array(
                                                                    'ubr_third_name'   => array(
                                                                        'label'             =>  __( 'Name', 'text-domain' ),
                                                                        'type'              =>  'text',
                                                                        'sanitize_callback' =>  'sanitize_text_field',
                                                                    ), 
                                                                    'ubr_third_bio'    => array(
                                                                        'label'             =>  __( 'Bio', 'text-domain' ),
                                                                        'type'              =>  'textarea',
                                                                    ),
                                                                ),
                                                            ),
                                                            'fourth_nested_uber'   =>  array(
                                                                'label'             =>  __( 'Section Three', 'text-domain' ),
                                                                'description'       =>  __( 'A Three Nested Section.', 'text-domain' ),        
                                                                'control'           =>  array(
                                                                    'ubr_fourth_name'   => array(
                                                                        'label'             =>  __( 'Name', 'text-domain' ),
                                                                        'type'              =>  'text',
                                                                        'sanitize_callback' =>  'sanitize_text_field',
                                                                    ), 
                                                                    'ubr_fourth_email'  => array(
                                                                        'label'             =>  __( 'Email', 'text-domain' ),
                                                                        'type'              =>  'email',
                                                                        'sanitize_callback' =>  'sanitize_email',
                                                                    ), 
                                                                ),
                                                            ),
                                                            'fifth_nested_uber'   =>  array(
                                                                'label'             =>  __( 'Section Five', 'text-domain' ),
                                                                'description'       =>  __( 'A Five Nested Section.', 'text-domain' ),        
                                                                'control'           =>  array(
                                                                    'ubr_fifth_name'   => array(
                                                                        'label'             =>  __( 'Name', 'text-domain' ),
                                                                        'type'              =>  'text',
                                                                        'sanitize_callback' =>  'sanitize_text_field',
                                                                    ), 
                                                                    'ubr_fifth_email'  => array(
                                                                        'label'             =>  __( 'Email', 'text-domain' ),
                                                                        'type'              =>  'email',
                                                                        'sanitize_callback' =>  'sanitize_email',
                                                                    ), 
                                                                    'ubr_fifth_age'    => array(
                                                                        'label'             =>  __( 'Age', 'text-domain' ),
                                                                        'type'              =>  'number',
                                                                    ), 
                                                                ),
                                                            ),
                                                            'sixth_nested_uber'   =>  array(
                                                                'label'             =>  __( 'Section Size', 'text-domain' ),
                                                                'description'       =>  __( 'A Size Nested Section.', 'text-domain' ),        
                                                                'control'           =>  array(
                                                                    'ubr_sixth_email'  => array(
                                                                        'label'             =>  __( 'Email', 'text-domain' ),
                                                                        'type'              =>  'email',
                                                                        'sanitize_callback' =>  'sanitize_email',
                                                                    ), 
                                                                    'ubr_sixth_age'    => array(
                                                                        'label'             =>  __( 'Age', 'text-domain' ),
                                                                        'type'              =>  'number',
                                                                    ), 
                                                                    'ubr_sixth_bio'    => array(
                                                                        'label'             =>  __( 'Bio', 'text-domain' ),
                                                                        'type'              =>  'textarea',
                                                                    ),
                                                                ),
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                            'third_nested'     =>  array(
                                                'label'             =>  __( 'Section Three', 'text-domain' ),
                                                'description'       =>  __( 'A Three Nested Section.', 'text-domain' ),        
                                                'control'           =>  array(
                                                    'third_bio'    => array(
                                                        'label'             =>  __( 'Bio', 'text-domain' ),
                                                        'type'              =>  'textarea',
                                                    ),
                                                ),
                                            ),
                                            'fourth_nested'     =>  array(
                                                'label'             =>  __( 'Section Four', 'text-domain' ),
                                                'description'       =>  __( 'A Four Nested Section.', 'text-domain' ),        
                                                'control'           =>  array(
                                                    'fourth_name'   => array(
                                                        'label'             =>  __( 'Name', 'text-domain' ),
                                                        'type'              =>  'text',
                                                        'sanitize_callback' =>  'sanitize_text_field',
                                                    ), 
                                                ),
                                            ),
                                            'fifth_nested'     =>  array(
                                                'label'             =>  __( 'Section Five', 'text-domain' ),
                                                'description'       =>  __( 'A Five Nested Section.', 'text-domain' ),        
                                                'control'           =>  array(
                                                    'fifth_email'  => array(
                                                        'label'             =>  __( 'Email', 'text-domain' ),
                                                        'type'              =>  'email',
                                                        'sanitize_callback' =>  'sanitize_email',
                                                    ), 
                                                    'fifth_age'    => array(
                                                        'label'             =>  __( 'Age', 'text-domain' ),
                                                        'type'              =>  'number',
                                                    ), 
                                                    'fifth_bio'    => array(
                                                        'label'             =>  __( 'Bio', 'text-domain' ),
                                                        'type'              =>  'textarea',
                                                    ),
                                                ),
                                            ),
                                            'sixth_nested'     =>  array(
                                                'label'             =>  __( 'Section Size', 'text-domain' ),
                                                'description'       =>  __( 'A Size Nested Section.', 'text-domain' ),        
                                                'control'           =>  array(
                                                    'sixth_email'  => array(
                                                        'label'             =>  __( 'Email', 'text-domain' ),
                                                        'type'              =>  'email',
                                                        'sanitize_callback' =>  'sanitize_email',
                                                    ), 
                                                    'sixth_bio'    => array(
                                                        'label'             =>  __( 'Bio', 'text-domain' ),
                                                        'type'              =>  'textarea',
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);

return $pages;