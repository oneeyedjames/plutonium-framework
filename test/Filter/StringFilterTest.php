<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Filter\StringFilter;

class StringFilterTest extends TestCase {
	public function setUp() {
		$this->filter = new StringFilter();
	}

	/*
	 * Tests that only alpha characters are returned.
	 */
	public function testFilterAlpha() {
		$this->assertNotFalse($this->filter->canHandle('alpha'));
		$this->assertSame('FooBar', $this->filter->filter('FooBar123@#!', 'alpha'));
	}

	/*
	 * Tests that only alphanumeric characters are returned.
	 */
	public function testFilterAlnum() {
		$this->assertNotFalse($this->filter->canHandle('alnum'));
		$this->assertSame('FooBar123', $this->filter->filter('FooBar123@#!', 'alnum'));
	}

	/*
	 * Tests that only decimal digits are returned.
	 */
	public function testFilterDigit() {
		$this->assertNotFalse($this->filter->canHandle('digit'));
		$this->assertSame('123', $this->filter->filter('FooBar123@#!', 'digit'));
	}

	/*
	 * Tests that only hexadecimal hexits are returned.
	 */
	public function testFilterHexit() {
		$this->assertNotFalse($this->filter->canHandle('hexit'));
		$this->assertSame('FBa123', $this->filter->filter('FooBar123@#!', 'hexit'));
	}

	/*
	 * Tests that only lower-case alpha characters are returned.
	 */
	public function testFilterLCase() {
		$this->assertNotFalse($this->filter->canHandle('lcase'));
		$this->assertSame('ooar', $this->filter->filter('FooBar123@#!', 'lcase'));
	}

	/*
	 * Tests that only upper-case alpha characters are returned.
	 */
	public function testFilterUCase() {
		$this->assertNotFalse($this->filter->canHandle('ucase'));
		$this->assertSame('FB', $this->filter->filter('FooBar123@#!', 'ucase'));
	}
}
