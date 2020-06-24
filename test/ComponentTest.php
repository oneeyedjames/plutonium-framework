<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Component;

class ComponentTest extends TestCase {
	public function setUp() {
		$this->comp = new ComponentImpl('TestName');
	}

	/*
	 * Tests that name property cannot be overwritten
	 */
	public function testName() {
		$comp = new ComponentImpl('TestName');
		$comp->name = 'AnotherName';

		$this->assertEquals('TestName', $comp->name);
	}

	/*
	 * Tests that arbitrary properties cannot be set
	 */
	public function testOther() {
		$comp = new ComponentImpl('TestName');
		$comp->other = 'OtherValue';

		$this->assertNull($comp->other);
	}
}

/**
 * Concrete Implementation Stub
 */
class ComponentImpl extends Component {
	public function install() {}
	public function uninstall() {}
	public function render() {}
}
