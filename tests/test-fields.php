<?php

/**
 * UIX tests misc
 *
 * @package   Tests_UIX
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      http://cramer.co.za
 * @group     uix-fields
 */
class Test_Fields extends WP_UnitTestCase {


	public function test_types() {

		$types = glob( __DIR__ . '/../classes/uix/ui/control/*.php' );
		$args  = [];
		$names = [];
		foreach ( $types as $type ) {
			$control_type                               = strtok( basename( $type ), '.' );
			$args['control'][ $control_type . '-test' ] = [
				'label'     => $control_type . '-label',
				'add_label' => $control_type . '-label-add',
				'type'      => $control_type,
				'value'     => $control_type . '-test',
				'choices'   => [
					'key' => 'KEY',
				],
			];
			$names[]                                    = $control_type;

		}

		$box = uix()->add( 'box', 'test-box', $args );

		// all loaded.
		$this->assertSame( count( $types ), count( $box->child ) );

		foreach ( $names as $control_type ) {
			$slug  = $control_type . '-test';
			$field = $box->child[ $slug ];

			$out = $field->render();

			$field->set_value( $slug );
			$data  = $field->get_data();
			$value = $slug;
			if ( 'separator' === $control_type ) {
				$value = null;
			}
			$this->assertSame( $value, $data[ $slug ] );
			if ( 'template' === $control_type ) {
				$this->assertSame( null, $out );
			} else {
				$this->assertTrue( false !== strpos( $out, $field->id() ) );
			}

			if ( in_array( $control_type, [ 'button', 'separator' ] ) ) {
				continue;
			}
			// second data.
			$field->set_data( [ $slug => [ 'woot' => 'toot' ] ] );
			$data = $field->get_data();

			$this->assertSame( 'toot', $data[ $slug ]['woot'] );

			if ( 'handlebars' === $control_type ) {
				$field->set_data( [ $slug => '{"woot":"toot"}' ] );
				$data = $field->get_data();
				$this->assertSame( 'toot', $data[ $slug ]['woot'] );
			}

		}


	}


}
