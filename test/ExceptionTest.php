<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Exception;

class ExceptionTest extends TestCase {
	public function testArray() {
		$e = new Exception('Error Message', 2, E_USER_WARNING, [
			'foo' => 'bar'
		]);

		$e['baz'] = 'bat';

		$this->assertTrue(isset($e['foo']));
		$this->assertFalse(isset($e['baz']));

		$this->assertEquals('bar', $e['foo']);
		$this->assertNull($e['baz']);
	}

	public function testObject() {
		$e = new Exception('Error Message', 2, E_USER_WARNING, [
			'foo' => 'bar'
		]);

		$e->baz = 'bat';

		$this->assertTrue(isset($e->foo));
		$this->assertFalse(isset($e->baz));

		$this->assertEquals('bar', $e->foo);
		$this->assertNull($e->baz);
	}
}
