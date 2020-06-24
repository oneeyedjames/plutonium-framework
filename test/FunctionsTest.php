<?php

use PHPUnit\Framework\TestCase;

use function Plutonium\Functions\paragraphize;
use function Plutonium\Functions\slugify;

use function Plutonium\Functions\array_peek;

use function Plutonium\Functions\is_assoc;
use function Plutonium\Functions\is_range;

use function Plutonium\Functions\filepath;

class FunctionsTest extends TestCase {
	public function testStrings() {
		$strings = array(
			array(
				'input' => "Hello, World!",
				'output' => "<p>Hello, World!</p>"
			),
			array(
				'input' => "Hello, World!\nGoodbye, Romance.",
				'output' => "<p>Hello, World!<br>\nGoodbye, Romance.</p>"
			),
			array(
				'input' => "Hello, World!\nGoodbye, Romance.",
				'output' => "<p>Hello, World!<br>\nGoodbye, Romance.</p>"
			),
			array(
				'input' => "Hello, World!\r\nGoodbye, Romance.",
				'output' => "<p>Hello, World!<br>\nGoodbye, Romance.</p>"
			),
			array(
				'input' => "Hello, World!\n\rGoodbye, Romance.",
				'output' => "<p>Hello, World!</p>\n<p>Goodbye, Romance.</p>"
			),
			array(
				'input' => "Hello, World!\n\nGoodbye, Romance.",
				'output' => "<p>Hello, World!</p>\n<p>Goodbye, Romance.</p>"
			)
		);

		foreach ($strings as $assertion) {
			extract($assertion);
			$this->assertEquals($output, paragraphize($input));
		}

		$string = "Hello, World!";

		$this->assertEquals("hello-world", slugify($string));
	}

	public function testArrays() {
		$array = array('foo', 'bar');
		$mixed = array('foo' => 'bar', 'baz');
		$assoc = array('foo' => 'bar', 'baz' => false);

		$this->assertFalse(is_assoc($array));
		$this->assertTrue(is_assoc($mixed));
		$this->assertTrue(is_assoc($assoc));

		$this->assertEquals('bar', array_peek($array));
		$this->assertEquals('baz', array_peek($mixed));
		$this->assertEquals(false, array_peek($assoc));

		$range = array('min' => 1, 'max' => 10);
		$rangePart = array('min' => 1);
		$rangePlus = array('min' => 1, 'max' => 10, 'foo' => 'bar');

		$this->assertTrue(is_range($range));
		$this->assertTrue(is_range($rangePart));
		$this->assertFalse(is_range($rangePlus));
	}

	public function testFiles() {
		$this->assertEquals('/bin/script.sh', filepath('/bin/script.sh'));
		$this->assertEquals('/bin/script.sh', filepath('/bin//script.sh'));
		$this->assertEquals('file:///bin/script.sh', filepath('file:///bin/script.sh'));

		$this->assertEquals(getenv('HOME') . '/bin/script.sh', filepath('~/bin/script.sh'));
		$this->assertEquals(realpath('.') . '/bin/script.sh', filepath('bin/script.sh'));
		$this->assertEquals(realpath('.') . '/bin/script.sh', filepath('./bin/script.sh'));
		$this->assertEquals(realpath('..') . '/bin/script.sh', filepath('../bin/script.sh'));
	}
}
