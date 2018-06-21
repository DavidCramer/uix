<?php

/**
 * UIX tests misc
 *
 * @package   Tests_UIX
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      http://cramer.co.za
 * @group     uix-misc
 */
class Test_Misc extends WP_UnitTestCase {

	public function test_children() {
		$ob  = uix()->add( 'box', 'test', [
			'control' => [
				'id' => 'test-id',
			],
		] );
		$ob2 = uix()->add( 'box', 'test2', [
			'control' => [
				'test-item' => [],
				[
					'id' => 'another-item',
				],
			],
		] );

		$this->asserttrue( isset( $ob->child['test-id'] ) );

		$this->asserttrue( isset( $ob2->child['test-item'] ) );
		$this->asserttrue( isset( $ob2->child['another-item'] ) );

	}

	public function test_assets() {
		$ob = uix()->add( 'box', 'test', [
			'style'  => [
				'fake' => 'url',
			],
			'script' => [
				'fake' => 'url',
			],
		] );

		$this->assertSame( 'url', $ob->assets['style']['fake'] );
		$this->assertSame( 'url', $ob->assets['script']['fake'] );

	}



}
