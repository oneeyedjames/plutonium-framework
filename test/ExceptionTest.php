<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Exception;

class ExceptionTest extends TestCase {
	public function testProperties() {
		$e = new Exception('Error Message', 2, E_USER_WARNING);

		$this->assertEquals('Error Message', $e->message);
		$this->assertEquals(2, $e->code);
		$this->assertEquals(E_USER_WARNING, $e->severity);

		$this->assertNotEmpty($e->file);
		$this->assertNotEmpty($e->line);
	}

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

		$this->assertFalse(isset($e->foo));
		$this->assertFalse(isset($e->baz));

		$this->assertNull($e->foo);
		$this->assertNull($e->baz);
	}
}
