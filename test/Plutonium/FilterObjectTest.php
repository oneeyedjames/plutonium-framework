<?php

use PHPUnit\Framework\TestCase;

use Plutonium\FilterObject;

class FilterObjectClass extends TestCase {
	var $object;

	public function testType() {
		$object = new FilterObject(array(
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
		$this->assertSame(array(1, 2, 3), $object->getArray('array'));
	}

	public function testString() {
		$object = new FilterObject(array(
			'string' => 'FooBar123@#!'
		));

		$this->assertSame('FooBar', $object->getAlpha('string'));
		$this->assertSame('FooBar123', $object->getAlnum('string'));
		$this->assertSame('123', $object->getDigit('string'));
		$this->assertSame('FBa123', $object->getHexit('string'));
		$this->assertSame('ooar', $object->getLower('string'));
		$this->assertSame('FB', $object->getUpper('string'));
	}
}
