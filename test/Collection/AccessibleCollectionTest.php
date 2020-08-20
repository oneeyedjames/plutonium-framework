<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Collection\AccessibleCollection;

class AccessibleCollectionTest extends TestCase {
	public function setUp() {
		$this->obj = new AccessibleCollection(['foo' => 'bar']);

		set_error_handler($this);
	}

	public function tearDown() {
		unset($this->obj, $this->errno, $this->error);

		set_error_handler(null);
	}

	public function testGet() {
		$this->assertEquals('bar', $this->obj->get('foo'));
		$this->assertNull($this->obj->get('baz'));

		$this->assertEquals('bar', $this->obj->get('foo', 'bat'));
		$this->assertEquals('bat', $this->obj->get('baz', 'bat'));

		$this->assertEquals('bar', $this->obj->foo);
		$this->assertNull($this->obj->baz);

		$this->assertEquals('bar', $this->obj['foo']);
		$this->assertNull($this->obj['baz']);
	}

	public function testSet() {
		$this->assertFalse($this->obj->has('baz'));
		$this->assertFalse(isset($this->obj->baz));

		$this->obj->baz = 'bat';
		$this->obj['baz'] = 'bat';

		$this->assertFalse($this->obj->has('baz'));
		$this->assertFalse(isset($this->obj->baz));

		$this->assertEquals(E_USER_ERROR, $this->errno);
	}

	public function testIsset() {
		$this->assertTrue($this->obj->has('foo'));
		$this->assertFalse($this->obj->has('baz'));

		$this->assertTrue(isset($this->obj->foo));
		$this->assertFalse(isset($this->obj->baz));

		$this->assertTrue(isset($this->obj['foo']));
		$this->assertFalse(isset($this->obj['baz']));
	}

	public function testUnset() {
		$this->assertTrue($this->obj->has('foo'));
		$this->assertTrue(isset($this->obj->foo));

		unset($this->obj->foo);
		unset($this->obj['foo']);

		$this->assertTrue($this->obj->has('foo'));
		$this->assertTrue(isset($this->obj->foo));

		$this->assertEquals(E_USER_ERROR, $this->errno);
	}

	public function __invoke($errno, $error) {
		$this->errno = $errno;
		$this->error = $error;
	}
}
