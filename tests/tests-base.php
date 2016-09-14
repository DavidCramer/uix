<?php
/**
 * UIX tests base
 *
 * @package   Tests_UIX
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      http://cramer.co.za
 */

class Test_UIX extends WP_UnitTestCase {

    public function register_paths( $uix ) {
        $uix->register( __DIR__ . '/ui' );
    }


    public function test_register_hook() {
        // checks to see if the auto loader loaded structures
        $uix = uix();
        $uix->auto_load();
        $this->assertNotEmpty( $uix->ui );

    }

    public function test_locations() {
        // add register action loaded the page demo
        $uix = uix();
        $manual_add = $uix->add( 'page', 'manual_add', array(                                                         // this is the settings array. The key is the page slug
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
        ) );
        $this->assertNotEmpty( $uix->ui->page['manual_add'] );

    }

    public function test_auto_adding() {
        $uix    = uix();
        $panel  = $uix->add( 'panel', 'test_panel', array(
            'label' =>  'test panel'
        ) );
        // check slug
        $this->assertSame( $panel->slug, 'test_panel' );
        // check id
        $this->assertSame( $panel->id(), 'uix-panel-test_panel' );
        // check child
        $this->assertEmpty( $panel->child );
        // check data
        $this->assertEmpty( $panel->get_data() );

        return $panel;
    }

    public function test_invalid_add() {
        $uix = uix();
        $object = $uix->add('invalid', 'thing', array() );
        $this->assertTrue( is_null( $object ) );
    }

    public function test_failed_auto_adding() {
        $uix = uix();
        $this->assertNotEmpty( $uix->ui->notice );
    }

    public function test_admin_menu() {
        global $submenu, $admin_page_hooks;

        $this->assertEmpty( $admin_page_hooks );

        // add register action loaded the page demo
        $uix = uix();
        do_action( 'admin_menu' );

        $this->assertNotEmpty( $admin_page_hooks );

        $this->assertSame( $admin_page_hooks['uixdemo'], 'uix-demo' );
        $this->assertFalse( isset( $admin_page_hooks['nopage'] ) );
        ob_start();
        $uix->ui->page['uixdemo']->render();
        $template = ob_get_clean();
        $this->assertTrue( is_string( $template ) );
    }

    public function test_control_register() {

        $uix = uix();

        $control = $uix->add('control', 'test_field', array(
            'type'  =>  'text'
        ) );

        $this->assertTrue( is_a( $control, 'uix\ui\control\text' ) );

        $this->assertTrue( $control->is_active() );

        return $control;

    }
    public function test_control_scripts_styles() {
        global $wp_scripts, $wp_styles;
        
        $this->assertEmpty( $wp_scripts );
        $this->assertFalse( isset( $wp_styles->registered['slider-control'] ) );
        $uix = uix();

        $control = $uix->add('control', 'slider_field', array(
            'type'  =>  'slider'
        ) );

        ob_start();
        do_action( 'admin_enqueue_scripts' );
        do_action( 'wp_print_styles' );
        $styles = ob_get_clean();

        $this->assertNotEmpty( $wp_styles->registered['slider-control'] );
        $this->assertNotEmpty( $styles );
        $this->assertNotEmpty( $wp_scripts );

    }
    public function test_post_type() {

        $uix = uix();

        $post_type = $uix->add('post_type', 'my_type', array(
            'name'
        ) );

        $this->assertTrue( is_a( $post_type, 'uix\ui\post_type' ) );

        $this->assertfalse( $post_type->is_active() );

        return $post_type;

    }

    public function test_notice(){

        $uix = uix();

        $notice = $uix->add('notice', 'error', array(
            'description' => 'error',
            'dismissable' => true
        ));

        $html = $notice->render();
        $hash = md5( $html );
        $this->assertSame( $hash, '07ce1d7a186fc0e98b5942003bec63ac');

    }


    public function test_controls(){

        $uix = uix();

        // radio atts
        $uix->ui->box['saving']->child['radio']->set_attributes();
        $radio_atts = $uix->ui->box['saving']->child['radio']->attributes;

        $test_atts = array(
            'name' => 'uix-radio-radiouix-box-saving',
            'class' => 'widefat',
            'type' => 'radio',
        );
        $this->assertSame( $radio_atts, $test_atts );

        // checkbox atts
        $uix->ui->box['saving']->child['checkbox']->set_attributes();
        $check_atts = $uix->ui->box['saving']->child['checkbox']->attributes;

        $test_atts = array(
            'name' => "uix-checkbox-checkboxuix-box-saving[]",
            'class' =>"widefat",
            'type' => "checkbox"
        );
        $this->assertSame( $check_atts, $test_atts );

        // render checks

        $check_html = $uix->ui->box['saving']->child['checkbox']->render();

        $hash = md5( $check_html );
        $this->assertSame( $hash, '15f1f25dc5b62a89cd20b2a92d14c81a' );


        // text classes
        $text_classes = $uix->ui->box['saving']->child['text']->classes();
        $test_classes = array( 'regular-text' );
        $this->assertSame( $text_classes, $test_classes );



    }


}

