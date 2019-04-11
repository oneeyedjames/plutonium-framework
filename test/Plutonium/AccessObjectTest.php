<?php

use PHPUnit\Framework\TestCase;

use Plutonium\AccessObject;

class AccessObjectTest extends TestCase {
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
