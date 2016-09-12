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
        $this->assertNotEmpty( $uix->ui->page['uixdemo'] );
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

        ob_start();
        $notice->render();
        $html = ob_get_clean();
        $hash = md5( $html );
        $this->assertSame( $hash, '07ce1d7a186fc0e98b5942003bec63ac');

    }

}

