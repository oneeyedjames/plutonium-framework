<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Filter\StringFilter;

class StringFilterTest extends TestCase {
	var $filter;

	public function setUp() {
		$this->filter = new StringFilter();
	}

	public function testFilterAlpha() {
		$this->assertNotFalse($this->filter->canHandle('alpha'));
		$this->assertSame('FooBar', $this->filter->filter('FooBar123@#!', 'alpha'));
	}

	public function testFilterAlnum() {
		$this->assertNotFalse($this->filter->canHandle('alnum'));
		$this->assertSame('FooBar123', $this->filter->filter('FooBar123@#!', 'alnum'));
	}

	public function testFilterDigit() {
		$this->assertNotFalse($this->filter->canHandle('digit'));
		$this->assertSame('123', $this->filter->filter('FooBar123@#!', 'digit'));
	}

	public function testFilterHexit() {
		$this->assertNotFalse($this->filter->canHandle('hexit'));
		$this->assertSame('FBa123', $this->filter->filter('FooBar123@#!', 'hexit'));
	}

	public function testFilterLCase() {
		$this->assertNotFalse($this->filter->canHandle('lcase'));
		$this->assertSame('ooar', $this->filter->filter('FooBar123@#!', 'lcase'));
	}

	public function testFilterUCase() {
		$this->assertNotFalse($this->filter->canHandle('ucase'));
		$this->assertSame('FB', $this->filter->filter('FooBar123@#!', 'ucase'));
	}
}