<?php

/**
 * @group admin
 */
class Tests_Admin_includesScreen extends WP_UnitTestCase {
    var $core_screens = array(
        'index.php' => array( 'base' => 'dashboard', 'id' => 'dashboard' ),
        'edit.php' => array( 'base' => 'edit', 'id' => 'edit-post', 'post_type' => 'post' ),
        'post-new.php'=> array( 'action' => 'add', 'base' => 'post', 'id' => 'post', 'post_type' => 'post' ),
        'edit-tags.php' => array( 'base' => 'edit-tags', 'id' => 'edit-post_tag', 'post_type' => 'post', 'taxonomy' => 'post_tag' ),
        'edit-tags.php?taxonomy=post_tag' => array( 'base' => 'edit-tags', 'id' => 'edit-post_tag', 'post_type' => 'post', 'taxonomy' => 'post_tag' ),
        'edit-tags.php?taxonomy=category' => array( 'base' => 'edit-tags', 'id' => 'edit-category', 'post_type' => 'post', 'taxonomy' => 'category' ),
        'upload.php' => array( 'base' => 'upload', 'id' => 'upload' ),
        'media-new.php' => array( 'action' => 'add', 'base' => 'media', 'id' => 'media' ),
        'edit.php?post_type=page' => array( 'base' => 'edit', 'id' => 'edit-page', 'post_type' => 'page' ),
        'link-manager.php' => array( 'base' => 'link-manager', 'id' => 'link-manager' ),
        'link-add.php' => array( 'action' => 'add', 'base' => 'link', 'id' => 'link' ),
        'edit-tags.php?taxonomy=link_category' => array( 'base' => 'edit-tags', 'id' => 'edit-link_category', 'taxonomy' => 'link_category', 'post_type' => '' ),
        'edit-comments.php' => array( 'base' => 'edit-comments', 'id' => 'edit-comments' ),
        'themes.php' => array( 'base' => 'themes', 'id' => 'themes' ),
        'widgets.php' => array( 'base' => 'widgets', 'id' => 'widgets' ),
        'nav-menus.php' => array( 'base' => 'nav-menus', 'id' => 'nav-menus' ),
        'plugins.php' => array( 'base' => 'plugins', 'id' => 'plugins' ),
        'users.php' => array( 'base' => 'users', 'id' => 'users' ),
        'user-new.php' => array( 'action' => 'add', 'base' => 'user', 'id' => 'user' ),
        'profile.php' => array( 'base' => 'profile', 'id' => 'profile' ),
        'tools.php' => array( 'base' => 'tools', 'id' => 'tools' ),
        'import.php' => array( 'base' => 'import', 'id' => 'import' ),
        'export.php' => array( 'base' => 'export', 'id' => 'export' ),
        'options-general.php' => array( 'base' => 'options-general', 'id' => 'options-general' ),
        'options-writing.php' => array( 'base' => 'options-writing', 'id' => 'options-writing' ),
    );

    function setUp() {
        set_current_screen( 'front' );
    }

    function tearDown() {
        parent::tearDown();
        set_current_screen( 'front' );
    }


    function test_help_tabs() {
        /*
        $page = uix()->ui->page['childpage'];
        $page->add_settings_page();
        set_current_screen( $page->screen_hook_suffix );
        $page->child['default-help']->render();
        $tab = rand_str();
        $tab_args = array(
            'title' => 'first help',
            'id' => 'default-help',
            'content' => 'first help content',
            'callback' => false,
            'priority' => 10,
        );

        $screen = get_current_screen();

        $this->assertEquals( $screen->get_help_tab( 'default-help' ), $tab_args );
        */
    }


    function test_page_screen() {

        /*
        $page = uix()->ui->page['uixdemo'];
        $page->add_settings_page();
        set_current_screen( $page->screen_hook_suffix );

        $screen = get_current_screen();

        $this->assertTrue( $page->is_active() );

        $this->assertTrue( get_current_screen()->in_admin() );
        $this->assertTrue( get_current_screen()->in_admin( 'site' ) );
        $this->assertFalse( get_current_screen()->in_admin( 'network' ) );
        $this->assertFalse( get_current_screen()->in_admin( 'user' ) );

        ob_start();
        $page->enqueue_core();
        $html = ob_get_clean();
        $this->assertTrue( is_string( $html ) );

        $GLOBALS['current_screen'] = $screen;

        */
    }

    function test_panel_template() {

        $uix = uix()->add('panel', 'torender', array(
            'template' => __DIR__ . '/ui/box/include_template.php',
        ) );

        $this->assertSame( $uix->render(), 'THIS IS A TEMPLATE' );
    }

    function test_post_type_as_hookname() {
        $type = uix()->ui->post_type['employee'];
        $type->render();

        $screen = convert_to_screen( 'employee' );
        $GLOBALS['current_screen'] = $screen;

        $this->assertEquals( $screen->post_type, 'employee' );
        $this->assertEquals( $screen->base, 'post' );
        $this->assertEquals( $screen->id, 'employee' );
        $this->assertTrue( $type->is_active() );

        ob_start();
        $type->enqueue_core();
        $html = ob_get_clean();
        $this->assertTrue( is_string( $html ) );
    }

    function test_post_type_metabox() {

        $type = uix()->ui->post_type['employee'];

        $_POST[ 'uix-text-textuix-section-one_sectionuix-metabox-myboxuix-post-employee' ] = 'saved';

        $metabox = $type->metabox('mybox', array(
            'name'        => 'my box',
            'description'  => 'my meta box',
            'template' => __DIR__ . '/ui/box/include_template.php',
            'chromeless' => true,
            'section'       =>  array(
                'one_section' => array(
                    'label' => 'test',
                    'control' => array(
                        'text' => array(
                            'type' => 'text',
                            'value' => 'demo'
                        ),
                        'no_update' => array(
                            'type' => 'text',
                            'value' => null
                        )

                    )
                )
            )
        ) );


        $screen = set_current_screen( 'employee' );

        $this->assertTrue( $metabox->is_active() );

        $screen = convert_to_screen( 'edit-employee' );

        //$_POST[ $metabox->id() ] = array
        $data = $metabox->get_data();
        $test_data = array(
            'one_section' => array(
                'text' => 'saved',
                'no_update' => null
            )
        );
        //$this->assertSame( $data, $test_data );

        $post_id = $this->factory->post->create_object( array(
            'post_type' => 'employee'
        ) );

        update_post_meta( $post_id, 'no_update', 'remove me' );

        $update_post = $this->factory->post->update_object( $post_id, array(
            'post_title' => 'updated'
        ) );

        $post = get_post( $post_id );

        $this->assertEmpty( $post->no_update );

        do_action( 'add_meta_boxes', 'employee', $post );

        ob_start();
        $metabox->enqueue_core();
        $html = ob_get_clean();
        $this->assertTrue( is_string( $html ) );
        ob_start();
        $metabox->create_metabox( $post );
        $html = ob_get_clean();
        $this->assertTrue( is_string( $html ) );


    }

    function test_fields() {

        $email = uix()->add('control', 'email_field', array(
            'type' => 'email'
        ));
        $this->assertSame( $email->type, 'email' );

        $file = uix()->add('control', 'file_field', array(
            'type' => 'file'
        ));
        $this->assertSame( $file->type, 'file' );

        $hidden = uix()->add('control', 'hidden_field', array(
            'type' => 'hidden'
        ));
        $this->assertSame( $hidden->type, 'hidden' );
        $this->assertSame( '<input type="hidden" value="" name="uix-hidden_field" class="widefat" id="uix-hidden_field-control">', $hidden->render() );


        $number = uix()->add('control', 'number_field', array(
            'type' => 'number'
        ));
        $this->assertSame( $number->type, 'number' );

        $color = uix()->add('control', 'color_field', array(
            'type' => 'color'
        ));
        $this->assertSame( $color->type, 'color' );
        $color->enqueue_core();

        $separator = uix()->add('control', 'separator_field', array(
            'type' => 'separator'
        ));
        $this->assertSame( $separator->type, 'separator' );

        $button = uix()->add('control', 'button_field', array(
            'type' => 'button',
            'label' => 'button',
            'attributes' => array(
                'class' => 'special'
            )
        ));
        $this->assertSame( $button->type, 'button' );
        $this->assertSame( '<button name="uix-button_field" class="special" id="uix-button_field-control">button</button>', $button->render() );


        ob_start();
        $separator->enqueue_core();
        $html = ob_get_clean();
        $this->assertTrue( is_string( $html ) );

        ob_start();
        $separator->render();
        $html = ob_get_clean();
        $this->assertTrue( is_string( $html ) );

        $template = uix()->add('control', 'template_field', array(
            'type' => 'template',
            'template' => __DIR__ . '/ui/page/template.php',
        ));
        $this->assertSame( $template->type, 'template' );

        ob_start();
        $template->render();
        $html = ob_get_clean();
        $this->assertTrue( is_string( $html ) );


        $textarea = uix()->add('control', 'textarea_field', array(
            'type' => 'textarea',
            'rows' => 12
        ));
        $this->assertSame( $textarea->type, 'textarea' );
        ob_start();
        $textarea->render();
        $html = ob_get_clean();
        $this->assertTrue( is_string( $html ) );

        $toggle = uix()->add('control', 'toggle_field', array(
            'type' => 'toggle',
        ));
        $this->assertSame( $toggle->type, 'toggle' );
        ob_start();
        $toggle->render();
        $html = ob_get_clean();
        $this->assertTrue( is_string( $html ) );


    }

}
