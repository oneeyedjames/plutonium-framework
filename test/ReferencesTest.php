<?php

use PHPUnit\Framework\TestCase;

class ReferencesTest extends TestCase {
	public function testPassByValue() {
		$a = (object) ['var' => 0];
		$b = $a;

		$this->passByVal($a);

		$this->assertEquals($a->var, 1);
		$this->assertEquals($b->var, 1);
	}

	public function testPassByValueNull() {
		$a = (object) ['var' => 0];
		$b = $a;

		$this->passByVal($a);

		$a = null;

		$this->assertNull($a);
		$this->assertEquals($b->var, 1);
	}

	public function testPassByValueUndefined() {
		$a = (object) ['var' => 0];
		$b = $a;

		$this->passByVal($a);

		unset($a);

		$this->assertNull(@$a);
		$this->assertEquals($b->var, 1);
	}

	public function testPassByValueReassigned() {
		$a = (object) ['var' => 0];
		$b = $a;

		$this->passByVal($a);

		$a = (object) ['var' => 5];

		$this->assertEquals($a->var, 5);
		$this->assertEquals($b->var, 1);
	}

	public function testPassByReference() {
		$a = (object) ['var' => 0];
		$b =& $a;

		$this->passByRef($a);

		$this->assertEquals($a->var, 1);
		$this->assertEquals($b->var, 1);
	}

	public function testPassByReferenceNull() {
		$a = (object) ['var' => 0];
		$b =& $a;

		$this->passByRef($a);

		$a = null;

		$this->assertNull($a);
		$this->assertNull($b);
	}

	public function testPassByReferenceUndefined() {
		$a = (object) ['var' => 0];
		$b =& $a;

		$this->passByRef($a);

		unset($a);

		$this->assertNull(@$a);
		$this->assertEquals($b->var, 1);
	}

	public function testPassByReferenceReassigned() {
		$a = (object) ['var' => 0];
		$b =& $a;

		$this->passByRef($a);

		$a = (object) ['var' => 5];

		$this->assertEquals($a->var, 5);
		$this->assertEquals($b->var, 5);
	}

	public function testReturnByValue() {
		$this->var = (object) ['var' => 0];

		$val = $this->returnByVal();

		$this->assertEquals(0, $val->var);
		$this->assertEquals(0, $this->var->var);

		$this->var->var++;

		$this->assertEquals(1, $val->var);
		$this->assertEquals(1, $this->var->var);

		$val->var++;

		$this->assertEquals(2, $val->var);
		$this->assertEquals(2, $this->var->var);
	}

	public function testReturnByReference() {
		$this->var = (object) ['var' => 0];

		$ref = $this->returnByRef();

		$this->assertEquals(0, $ref->var);
		$this->assertEquals(0, $this->var->var);

		$this->var->var++;

		$this->assertEquals(1, $ref->var);
		$this->assertEquals(1, $this->var->var);

		$ref->var++;

		$this->assertEquals(2, $ref->var);
		$this->assertEquals(2, $this->var->var);
	}

	public function passByVal($obj) {
		$obj->var++;
	}

	public function passByRef(&$obj) {
		$obj->var++;
	}

	public function returnByVal() {
		return $this->var;
	}

	public function &returnByRef() {
		return $this->var;
	}
}
