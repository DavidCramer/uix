<?php

/**
 * UIX tests base
 *
 * @package   Tests_UIX
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      http://cramer.co.za
 */
class Test_Data extends WP_UnitTestCase {

	public static function setUpBeforeClass() {
		$uix     = uix();
		$control = $uix->add( 'control', 'test_field', array(
			'type'  => 'text',
			'value' => 'default',
		) );

		$data                         = $control->get_data();
		$_POST['uix-text-test_field'] = 'fake submit';
		$control->setup();
		$new_data = $control->get_data();
	}

	public function test_box_data() {

		$uix = uix();
		$this->assertNotEmpty( $uix->ui->box['saving'] );

		$data = $uix->ui->box['saving']->get_data();
		$hash = md5( json_encode( $data ) );

		$this->assertSame( $hash, '9a0f8eb7f633dca09385c45f6ccc259e' );

		$new_data = array(
			'checkbox'  => array( 'one', 'two' ),
			'radio'     => 'two',
			'select'    => 'two',
			'separator' => '',
			'slider'    => '50',
			'template'  => '',
			'file'      => '',
			'hidden'    => 'testing',
			'number'    => '41',
			'text'      => 'changed',
			'textarea'  => 'bio',
			'toggle'    => true,
		);
		$new_hash = md5( json_encode( $new_data ) );
		$uix->ui->box['saving']->set_data( $new_data );
		$changed_data = $uix->ui->box['saving']->get_data();

	}

	public function test_set_store_key() {

		$page = uix()->add( 'page', 'store_key_test', array(
			'page_title' => 'Test Key',
			'menu_title' => 'Test Key',
			'store_key'  => 'wooter-storage',
			'header'     => array(
				'head' => array(
					'label'   => 'Wooter',
					'version' => 'tooter',
				),
			),
		) );
		$key  = $page->store_key();
		$this->assertSame( $key, 'wooter-storage' );

		$html = $page->render();
		$this->assertTrue( is_string( $html ) );
	}


	public function test_new_box() {
		$uix = uix();

		$box = $uix->add( 'box', 'my_test', array(
			'label'       => 'Test Box',
			'description' => 'A New Added Box',
			'control'     => array(
				'box_input' => array(
					'type'  => 'text',
					'value' => 'default',
				),
			),
		) );
		$this->assertTrue( isset( $uix->ui->box['my_test'] ) );

		$box->init();

		$data = $box->get_data();
		$this->assertSame( $data, array( 'box_input' => 'default' ) );

		$nonce                                      = wp_create_nonce( $box->id() );
		$_POST[ 'uixNonce_' . $box->id() ]          = $nonce;
		$_POST['uix-text-box_inputuix-box-my_test'] = 'sweet';
		$box->init();

		$box->set_data( array( 'box_input' => 'sweet' ) );
		$new_data = $box->get_data();

		ob_start();
		$box->render();
		$rendered = ob_get_clean();

		$this->assertTrue( is_string( $rendered ) );

	}

}
