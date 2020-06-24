<?php

use PHPUnit\Framework\TestCase;

use Plutonium\AccessObject;
use Plutonium\Http\Response;

class ResponseTest extends TestCase {
	/*
	 * Tests that module output is properly formatted.
	 */
	public function testModule() {
		$args = new AccessObject(array(
			'module_start' => '<article>',
			'module_close' => '</article>'
		));

		$response = new Response();
		$response->setModuleOutput('Hello, World!');

		$expected = '<article>Hello, World!</article>';

		$this->assertEquals($expected, $response->getModuleOutput($args));
	}

	/*
	 * Tests that widget output is properly formatted.
	 */
	public function testWidget() {
		$args = new AccessObject(array(
			'widget_start' => '<aside>',
			'widget_delim' => '<hr>',
			'widget_close' => '</aside>'
		));

		$response = new Response();
		$response->setWidgetOutput('sidebar', '1');
		$response->setWidgetOutput('sidebar', '2');
		$response->setWidgetOutput('footer',  '3');

		$expected = '<aside>1</aside><hr><aside>2</aside>';
		$actual = $response->getWidgetOutput('sidebar', $args);

		$this->assertEquals($expected, $actual);

		$expected = '<aside>3</aside>';
		$actual = $response->getWidgetOutput('footer', $args);

		$this->assertEquals($expected, $actual);
	}
}
