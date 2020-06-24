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
	 * Tests that Iterator interface is properly implemented.
	 *
	 * Key/value pairs can be traversed with foreach loop.
	 */
	public function testIterable() {
		$vars = array('foo' => 'bar', 'baz' => 'baz');

		$object = new AccessObject($vars);

		$keys   = array_keys($vars);
		$values = array_values($vars);

		$i = 0;

		foreach ($object as $key => $value) {
			$this->assertEquals($keys[$i],   $key);
			$this->assertEquals($values[$i], $value);

			$i++;
		}

		// repeat to verify proper reset

		$i = 0;

		foreach ($object as $key => $value) {
			$this->assertEquals($keys[$i],   $key);
			$this->assertEquals($values[$i], $value);

			$i++;
		}
	}

	/*
	 * Tests that Countable interface is properly implemented.
	 *
	 * Number of stored properties can be determine with count() function.
	 */
	public function testCountable() {
		$object = new AccessObject();

		$this->assertEquals(0, count($object));

		$object->set('foo', 'bar');
		$object->set('baz', 'bat');

		$this->assertEquals(2, count($object));

		$object->set('foo', 'baz');

		$this->assertEquals(2, count($object));
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

	/*
	 * Tests that ArrayAccess interface is properly implemented.
	 *
	 * Named properties can be accessed as associative array indices.
	 */
	public function testArray() {
		$object = new AccessObject();

		$this->assertEquals(0, count($object));
		$this->assertFalse(isset($object['foo']));
		$this->assertNull($object['foo']);

		$object['foo'] = 'bar';

		$this->assertEquals(1, count($object));
		$this->assertTrue(isset($object['foo']));
		$this->assertEquals('bar', $object['foo']);

		unset($object['foo']);

		$this->assertEquals(0, count($object));
		$this->assertFalse(isset($object['foo']));
		$this->assertNull($object['foo']);
	}
}
