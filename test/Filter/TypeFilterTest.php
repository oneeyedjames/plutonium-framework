<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Filter\TypeFilter;

class TypeFilterTest extends TestCase {
	public function setUp() {
		$this->filter = new TypeFilter();
	}

	/*
	 * Tests that passed value is returned as a boolean value.
	 */
	public function testFilterBool() {
		$this->assertNotFalse($this->filter->canHandle('bool'));
		$this->assertSame(false, $this->filter->filter(0, 'bool'));
		$this->assertSame(true, $this->filter->filter(1, 'bool'));
		$this->assertNull($this->filter->filter([1, 2, 3], 'bool'));
	}

	/*
	 * Tests that passed value is returned as an integer value.
	 */
	public function testFilterInt() {
		$this->assertNotFalse($this->filter->canHandle('int'));
		$this->assertSame(0, $this->filter->filter(0, 'int'));
		$this->assertSame(1, $this->filter->filter(1, 'int'));
		$this->assertNull($this->filter->filter([1, 2, 3], 'int'));
	}

	/*
	 * Tests that passed value is returned as a float value.
	 */
	public function testFilterFloat() {
		$this->assertNotFalse($this->filter->canHandle('float'));
		$this->assertSame(0.0, $this->filter->filter(0, 'float'));
		$this->assertSame(1.0, $this->filter->filter(1, 'float'));
		$this->assertNull($this->filter->filter([1, 2, 3], 'float'));
	}

	/*
	 * Tests that passed value is returned as a string value.
	 */
	public function testFilterString() {
		$this->assertNotFalse($this->filter->canHandle('string'));
		$this->assertSame('0', $this->filter->filter(0, 'string'));
		$this->assertSame('1', $this->filter->filter(1, 'string'));
		$this->assertNull($this->filter->filter([1, 2, 3], 'string'));
	}

	/*
	 * Tests that passed value is returned as an array.
	 */
	public function testFilterArray() {
		$this->assertNotFalse($this->filter->canHandle('array'));
		$this->assertNull($this->filter->filter(0, 'array'));
		$this->assertNull($this->filter->filter(1, 'array'));
		$this->assertSame([1, 2, 3], $this->filter->filter([1, 2, 3], 'array'));
	}

	/*
	 * Tests that passed value is returned as an object.
	 */
	public function testFilterObject() {
		$this->assertNotFalse($this->filter->canHandle('object'));
		$this->assertNull($this->filter->filter(0, 'object'));
		$this->assertNull($this->filter->filter(1, 'object'));
		$this->assertEquals((object) [1, 2, 3], $this->filter->filter([1, 2, 3], 'object'));
		$this->assertNotEquals((object) [1, 2, 3], [1, 2, 3]);
	}
}
