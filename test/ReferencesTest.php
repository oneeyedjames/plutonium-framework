f<?php

use PHPUnit\Framework\TestCase;

class ReferencesTest extends TestCase {
	public function setUp() {
		$this->var = 0;
		$this->obj = (object) ['var' => 0];
	}

	public function testPassByValue() {
		$var = $this->var;
		$obj = $this->obj;

		$this->passScalarByValue($var);
		$this->passObjectByValue($obj);

		$this->assertEquals($var, 0);
		$this->assertEquals($obj->var, 1);
	}

	// public function testPassByValueNull() {
	// 	$a = (object) ['var' => 0];
	// 	$b = $a;
	//
	// 	$this->passObjectByValue($a);
	//
	// 	$a = null;
	//
	// 	$this->assertNull($a);
	// 	$this->assertEquals($b->var, 1);
	// }

	// public function testPassByValueUndefined() {
	// 	$a = (object) ['var' => 0];
	// 	$b = $a;
	//
	// 	$this->passObjectByValue($a);
	//
	// 	unset($a);
	//
	// 	$this->assertNull(@$a);
	// 	$this->assertEquals($b->var, 1);
	// }

	// public function testPassByValueReassigned() {
	// 	$a = (object) ['var' => 0];
	// 	$b = $a;
	//
	// 	$this->passObjectByValue($a);
	//
	// 	$a = (object) ['var' => 5];
	//
	// 	$this->assertEquals($a->var, 5);
	// 	$this->assertEquals($b->var, 1);
	// }

	public function testPassByReference() {
		$var = $this->var;
		$obj = $this->obj;

		$this->passScalarByReference($var);
		$this->passObjectByReference($obj);

		$this->assertEquals($var, 1);
		$this->assertEquals($obj->var, 1);
	}

	// public function testPassByReferenceNull() {
	// 	$a = (object) ['var' => 0];
	// 	$b =& $a;
	//
	// 	$this->passObjectByReference($a);
	//
	// 	$a = null;
	//
	// 	$this->assertNull($a);
	// 	$this->assertNull($b);
	// }

	// public function testPassByReferenceUndefined() {
	// 	$a = (object) ['var' => 0];
	// 	$b =& $a;
	//
	// 	$this->passObjectByReference($a);
	//
	// 	unset($a);
	//
	// 	$this->assertNull(@$a);
	// 	$this->assertEquals($b->var, 1);
	// }

	// public function testPassByReferenceReassigned() {
	// 	$a = (object) ['var' => 0];
	// 	$b =& $a;
	//
	// 	$this->passObjectByReference($a);
	//
	// 	$a = (object) ['var' => 5];
	//
	// 	$this->assertEquals($a->var, 5);
	// 	$this->assertEquals($b->var, 5);
	// }

	public function testReturnByValue() {
		$var = $this->returnScalarByValue();
		$obj = $this->returnObjectByValue();

		$this->assertEquals(0, $var);
		$this->assertEquals(0, $this->var);
		$this->assertEquals(0, $obj->var);
		$this->assertEquals(0, $this->obj->var);

		$this->var++;
		$this->obj->var++;

		// different values because scalars are returned by value
		$this->assertEquals(0, $var);
		$this->assertEquals(1, $this->var);

		// same values because objects are always returned by reference
		$this->assertEquals(1, $obj->var);
		$this->assertEquals(1, $this->obj->var);

		$var++;
		$obj->var++;

		$this->assertEquals(1, $var);
		$this->assertEquals(1, $this->var);
		$this->assertEquals(2, $obj->var);
		$this->assertEquals(2, $this->obj->var);
	}

	public function testReturnByReference() {
		$var =& $this->returnScalarByReference();
		$obj =& $this->returnObjectByReference();

		$this->assertEquals(0, $var);
		$this->assertEquals(0, $this->var);
		$this->assertEquals(0, $obj->var);
		$this->assertEquals(0, $this->obj->var);

		$this->var++;
		$this->obj->var++;

		$this->assertEquals(1, $var);
		$this->assertEquals(1, $this->var);
		$this->assertEquals(1, $obj->var);
		$this->assertEquals(1, $this->obj->var);

		$var++;
		$obj->var++;

		$this->assertEquals(2, $var);
		$this->assertEquals(2, $this->var);
		$this->assertEquals(2, $obj->var);
		$this->assertEquals(2, $this->obj->var);
	}

	public function passScalarByValue($var) {
		$var++;
	}

	public function passObjectByValue($obj) {
		$obj->var++;
	}

	public function passScalarByReference(&$var) {
		$var++;
	}

	public function passObjectByReference(&$obj) {
		$obj->var++;
	}

	public function returnScalarByValue() {
		return $this->var;
	}

	public function returnObjectByValue() {
		return $this->obj;
	}

	public function &returnScalarByReference() {
		return $this->var;
	}

	public function &returnObjectByReference() {
		return $this->obj;
	}
}
