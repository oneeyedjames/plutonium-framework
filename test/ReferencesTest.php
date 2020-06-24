f<?php

use PHPUnit\Framework\TestCase;

class ReferencesTest extends TestCase {
	public function setUp() {
		$this->var = 0;
		$this->arr = ['var' => 0];
		$this->obj = (object) ['var' => 0];
	}

	public function testPassByValue() {
		$var = $this->var;
		$arr = $this->arr;
		$obj = $this->obj;

		$this->passScalarByValue($var);
		$this->passArrayByValue($arr);
		$this->passObjectByValue($obj);

		$this->assertEquals($var, 0);
		$this->assertEquals($arr['var'], 0);
		$this->assertEquals($obj->var, 1);
	}

	public function testPassByReference() {
		$var = $this->var;
		$arr = $this->arr;
		$obj = $this->obj;

		$this->passScalarByReference($var);
		$this->passArrayByReference($arr);
		$this->passObjectByReference($obj);

		$this->assertEquals($var, 1);
		$this->assertEquals($arr['var'], 1);
		$this->assertEquals($obj->var, 1);
	}

	public function testReturnByValue() {
		$var = $this->returnScalarByValue();
		$arr = $this->returnArrayByValue();
		$obj = $this->returnObjectByValue();

		$this->assertEquals(0, $var);
		$this->assertEquals(0, $this->var);
		$this->assertEquals(0, $arr['var']);
		$this->assertEquals(0, $this->arr['var']);
		$this->assertEquals(0, $obj->var);
		$this->assertEquals(0, $this->obj->var);

		$this->var++;
		$this->arr['var']++;
		$this->obj->var++;

		// different values because scalars are returned by value
		$this->assertEquals(0, $var);
		$this->assertEquals(1, $this->var);

		// different values because arrays are returned by value
		$this->assertEquals(0, $arr['var']);
		$this->assertEquals(1, $this->arr['var']);

		// same values because objects are always returned by reference
		$this->assertEquals(1, $obj->var);
		$this->assertEquals(1, $this->obj->var);

		$var++;
		$arr['var']++;
		$obj->var++;

		$this->assertEquals(1, $var);
		$this->assertEquals(1, $this->var);
		$this->assertEquals(1, $arr['var']);
		$this->assertEquals(1, $this->arr['var']);
		$this->assertEquals(2, $obj->var);
		$this->assertEquals(2, $this->obj->var);
	}

	public function testReturnByReference() {
		$var =& $this->returnScalarByReference();
		$arr =& $this->returnArrayByReference();
		$obj =& $this->returnObjectByReference();

		$this->assertEquals(0, $var);
		$this->assertEquals(0, $this->var);
		$this->assertEquals(0, $arr['var']);
		$this->assertEquals(0, $this->arr['var']);
		$this->assertEquals(0, $obj->var);
		$this->assertEquals(0, $this->obj->var);

		$this->var++;
		$this->arr['var']++;
		$this->obj->var++;

		$this->assertEquals(1, $var);
		$this->assertEquals(1, $this->var);
		$this->assertEquals(1, $arr['var']);
		$this->assertEquals(1, $this->arr['var']);
		$this->assertEquals(1, $obj->var);
		$this->assertEquals(1, $this->obj->var);

		$var++;
		$arr['var']++;
		$obj->var++;

		$this->assertEquals(2, $var);
		$this->assertEquals(2, $this->var);
		$this->assertEquals(2, $arr['var']);
		$this->assertEquals(2, $this->arr['var']);
		$this->assertEquals(2, $obj->var);
		$this->assertEquals(2, $this->obj->var);
	}

	protected function passScalarByValue($var) {
		$var++;
	}

	protected function passArrayByValue($arr) {
		$arr['var']++;
	}

	protected function passObjectByValue($obj) {
		$obj->var++;
	}

	protected function passScalarByReference(&$var) {
		$var++;
	}

	protected function passArrayByReference(&$arr) {
		$arr['var']++;
	}

	protected function passObjectByReference(&$obj) {
		$obj->var++;
	}

	protected function returnScalarByValue() {
		return $this->var;
	}

	protected function returnArrayByValue() {
		return $this->arr;
	}

	protected function returnObjectByValue() {
		return $this->obj;
	}

	protected function &returnScalarByReference() {
		return $this->var;
	}

	protected function &returnArrayByReference() {
		return $this->arr;
	}

	protected function &returnObjectByReference() {
		return $this->obj;
	}
}
