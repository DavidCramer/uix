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

    public function test_sumbit_data() {
        $uix = uix();
        $control = $uix->add('control', 'test_field', array(
            'type'  =>  'text',
            'value' => 'default'
        ) );

        $data = $control->get_data();        
        $_POST['uix-text-test_field'] = 'fake submit';
        $control->setup();
        $new_data = $control->get_data();
        $this->assertSame( $data, 'default' );
        $this->assertSame( $new_data, 'fake submit' );

    }

    public function test_box_data() {

        $uix = uix();
        $this->assertNotEmpty( $uix->ui->box['saving'] );

        $data = $uix->ui->box['saving']->get_data();
        $hash = md5( json_encode( $data ) );
        $this->assertArrayHasKey( 'text', $data );

        $this->assertEmpty( $data['text'] );

        $this->assertSame( $hash, 'ec59b646897208dad8d077dfd328a04d' );

        $new_data = array(
            'checkbox'      => array('one','two'),
            'radio'         => 'two',
            'select'        => 'two',
            'separator'     => '',
            'slider'        => '50',
            'template'      => '',
            'file'          => '',
            'hidden'        => 'testing',
            'number'        => '41',
            'text'          => 'changed',
            'textarea'      => 'bio',
            'toggle'        => true,
        );
        $new_hash = md5( json_encode( $new_data ) );
        $uix->ui->box['saving']->set_data( $new_data );
        $changed_data = $uix->ui->box['saving']->get_data();
        $this->assertSame( $changed_data['text'], 'changed' );
        $changed_hash = md5( json_encode( $changed_data ) );
        $this->assertSame( $new_hash, $changed_hash );

    }    

}
