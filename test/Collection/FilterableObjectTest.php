<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Collection\FilterableObject;

class FilterableObjectTest extends TestCase {
	/*
	 * Tests that type-casting getter methods are properly implemented.
	 */
	public function testType() {
		$object = new FilterableObject(array(
			'zero'  => 0,
			'one'   => 1,
			'array' => array(1, 2, 3)
		));

		$this->assertSame(false, $object->getBool('zero'));
		$this->assertSame(0, $object->getInt('zero'));
		$this->assertSame(0.0, $object->getFloat('zero'));
		$this->assertSame('0', $object->getString('zero'));
		$this->assertNull($object->getArray('zero'));
		$this->assertNull($object->getObject('zero'));

		$this->assertSame(true, $object->getBool('one'));
		$this->assertSame(1, $object->getInt('one'));
		$this->assertSame(1.0, $object->getFloat('one'));
		$this->assertSame('1', $object->getString('one'));
		$this->assertNull($object->getArray('one'));
		$this->assertNull($object->getObject('one'));

		$this->assertNull($object->getBool('array'));
		$this->assertNull($object->getInt('array'));
		$this->assertNull($object->getFloat('array'));
		$this->assertNull($object->getString('array'));
		$this->assertEquals(array(1, 2, 3), $object->getArray('array'));
		$this->assertEquals((object)array(1, 2, 3), $object->getObject('array'));
	}

	/*
	 * Tests that string-filtering getter methods are properly implemented.
	 */
	public function testString() {
		$object = new FilterableObject(array(
			'value' => 'FooBar123@#!'
		));

		$this->assertSame('FooBar', $object->getAlpha('value'));
		$this->assertSame('FooBar123', $object->getAlnum('value'));
		$this->assertSame('123', $object->getDigit('value'));
		$this->assertSame('FBa123', $object->getHexit('value'));
		$this->assertSame('ooar', $object->getLower('value'));
		$this->assertSame('FB', $object->getUpper('value'));
	}
}
