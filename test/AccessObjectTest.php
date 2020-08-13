<?php

use PHPUnit\Framework\TestCase;

use Plutonium\AccessObject;

class AccessObjectTest extends TestCase {
	/*
	 * Tests that Plutonium\Accessible interface is properly implemented.
	 *
	 * Named properties can be accessed by standard getter/setter methods.
	 */
	public function testAccessible() {
		$object = new AccessObject();

		$this->assertFalse($object->has('foo'));
		$this->assertNull($object->get('foo'));

		$object->set('foo', 'bar');

		$this->assertTrue($object->has('foo'));
		$this->assertEquals('bar', $object->get('foo'));

		$object->def('foo', 'baz');

		$this->assertTrue($object->has('foo'));
		$this->assertEquals('bar', $object->get('foo'));

		$object->del('foo');

		$this->assertFalse($object->has('foo'));
		$this->assertNull($object->get('foo'));

		$object->def('foo', 'baz');

		$this->assertTrue($object->has('foo'));
		$this->assertEquals('baz', $object->get('foo'));
	}

	/*
	 * Tests that magic methods are properly implemented:
	 *   __get($key)
	 *   __set($key, $value)
	 *   __isset($key)
	 *   __unset($key)
	 *
	 * Named properties can be accessed as standard object properties.
	 */
	public function testMagic() {
		$object = new AccessObject();

		$this->assertFalse(isset($object->foo));
		$this->assertNull($object->foo);

		$object->foo = 'bar';

		$this->assertTrue(isset($object->foo));
		$this->assertEquals('bar', $object->foo);

		unset($object->foo);

		$this->assertFalse(isset($object->foo));
		$this->assertNull($object->foo);
	}
}
