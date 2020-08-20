<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Collection\MutableCollection;

class MutableCollectionTest extends TestCase {
	public function setUp() {
		$this->obj = new MutableCollection(['foo' => 'bar']);
	}

	public function tearDown() {
		unset($this->obj);
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

	public function testSetMethod() {
		$this->assertFalse($this->obj->has('baz'));
		$this->assertNull($this->obj->get('baz'));

		$this->obj->set('baz', 'bat');

		$this->assertTrue($this->obj->has('baz'));
		$this->assertEquals('bat', $this->obj->get('baz'));
	}

	public function testSetDefault() {
		$this->assertTrue($this->obj->has('foo'));
		$this->assertEquals('bar', $this->obj->get('foo'));

		$this->obj->def('foo', 'baz');

		$this->assertTrue($this->obj->has('foo'));
		$this->assertEquals('bar', $this->obj->get('foo'));
	}

	public function testSetObject() {
		$this->assertFalse($this->obj->has('baz'));
		$this->assertNull($this->obj->get('baz'));

		$this->obj->baz = 'bat';

		$this->assertTrue($this->obj->has('baz'));
		$this->assertEquals('bat', $this->obj->get('baz'));
	}

	public function testSetArray() {
		$this->assertFalse($this->obj->has('baz'));
		$this->assertNull($this->obj->get('baz'));

		$this->obj['baz'] = 'bat';

		$this->assertTrue($this->obj->has('baz'));
		$this->assertEquals('bat', $this->obj->get('baz'));
	}

	public function testIsset() {
		$this->assertTrue($this->obj->has('foo'));
		$this->assertFalse($this->obj->has('baz'));

		$this->assertTrue(isset($this->obj->foo));
		$this->assertFalse(isset($this->obj->baz));

		$this->assertTrue(isset($this->obj['foo']));
		$this->assertFalse(isset($this->obj['baz']));
	}

	public function testUnsetMethod() {
		$this->assertTrue($this->obj->has('foo'));
		$this->assertEquals('bar', $this->obj->get('foo'));

		$this->obj->del('foo');

		$this->assertFalse($this->obj->has('foo'));
		$this->assertNull($this->obj->get('foo'));
	}

	public function testUnsetObject() {
		$this->assertTrue($this->obj->has('foo'));
		$this->assertEquals('bar', $this->obj->get('foo'));

		unset($this->obj->foo);

		$this->assertFalse($this->obj->has('foo'));
		$this->assertNull($this->obj->get('foo'));
	}

	public function testUnsetArray() {
		$this->assertTrue($this->obj->has('foo'));
		$this->assertEquals('bar', $this->obj->get('foo'));

		unset($this->obj['foo']);

		$this->assertFalse($this->obj->has('foo'));
		$this->assertNull($this->obj->get('foo'));
	}
}
