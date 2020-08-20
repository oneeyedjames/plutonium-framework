<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Collection\ArrayLike;

class ArrayLikeTest extends TestCase {
	const IMMUTABLE_ERROR = 'Cannot set value on immutable collection.';

	private $errno;
	private $error;

	/*
	 * Tests that ArrayAccess interface is properly implemented.
	 *
	 * Named properties can be accessed as associative array indices.
	 */
	public function testArrayAccess() {
		$object = new ArrayLikeObject();

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

	public function testReadonlySet() {
		$object = new ArrayLikeObject([], true);

		$this->assertEquals(0, count($object));
		$this->assertFalse(isset($object['foo']));
		$this->assertNull($object['foo']);

		set_error_handler($this, E_USER_ERROR);

		$object['foo'] = 'bar';

		$this->assertEquals(E_USER_ERROR, $this->errno);
		$this->assertEquals(self::IMMUTABLE_ERROR, $this->error);

		$this->assertEquals(0, count($object));
		$this->assertFalse(isset($object['foo']));
		$this->assertNull($object['foo']);
	}

	public function testReadonlyUnset() {
		$object = new ArrayLikeObject(['foo' => 'bar'], true);

		$this->assertEquals(1, count($object));
		$this->assertTrue(isset($object['foo']));
		$this->assertEquals('bar', $object['foo']);

		set_error_handler($this, E_USER_ERROR);

		$object['foo'] = 'bar';

		$this->assertEquals(E_USER_ERROR, $this->errno);
		$this->assertEquals(self::IMMUTABLE_ERROR, $this->error);

		$this->assertEquals(1, count($object));
		$this->assertTrue(isset($object['foo']));
		$this->assertEquals('bar', $object['foo']);
	}

	/*
	 * Tests that Iterator interface is properly implemented.
	 *
	 * Key/value pairs can be traversed with foreach loop.
	 */
	public function testIterable() {
		$vars = array('foo' => 'bar', 'baz' => 'baz');

		$object = new ArrayLikeObject($vars);

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
		$object = new ArrayLikeObject();

		$this->assertEquals(0, count($object));

		$object['foo'] = 'bar';
		$object['baz'] = 'bat';

		$this->assertEquals(2, count($object));

		$object['foo'] = 'baz';

		$this->assertEquals(2, count($object));
	}

	/*
	 * Tests that JsonSerializable interface is properly implemented.
	 *
	 * JSON representation can be created with json_serialize() function.
	 */
	public function testJsonSerializable() {
		$vars = array('foo' => 'bar', 'baz' => 'baz');
		$json = json_encode($vars);

		$object = new ArrayLikeObject($vars);

		$this->assertEquals($json, json_encode($object));
	}

	/*
	 * Tests that Traversable objects, plain objects, and arrays can all be
	 * normalized into matching array representation.
	 */
	public function testNormalize() {
		$vars = array('foo' => 'bar', 'baz' => 'baz');

		$data = ArrayLike::normalize($vars);

		$this->assertTrue(is_array($data));
		$this->assertSame($vars, $data);

		$data = ArrayLike::normalize((object) $vars);

		$this->assertTrue(is_array($data));
		$this->assertSame($vars, $data);

		$data = ArrayLike::normalize(new ArrayLikeObject($vars));

		$this->assertTrue(is_array($data));
		$this->assertSame($vars, $data);
	}

	public function __invoke($errno, $error) {
		$this->errno = $errno;
		$this->error = $error;

		return true;
	}
}

class ArrayLikeObject
implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable {
	use ArrayLike;

	public function __construct($data = [], $readonly = false) {
		$this->_vars = $data;
		$this->_readonly = $readonly;
	}
}
