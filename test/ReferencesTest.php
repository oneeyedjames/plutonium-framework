<?php

use PHPUnit\Framework\TestCase;

class ReferencesTest extends TestCase {
	public function testPass() {
		$a = (object) ['var' => 0];
		$b = $a;
		$c =& $a;

		$this->assertEquals($a->var, 0);
		$this->assertEquals($b->var, 0);
		$this->assertEquals($c->var, 0);

		$this->passByVal($a);

		$this->assertEquals($a->var, 1);
		$this->assertEquals($b->var, 1);
		$this->assertEquals($c->var, 1);

		$this->passByRef($a);

		$this->assertEquals($a->var, 2);
		$this->assertEquals($b->var, 2);
		$this->assertEquals($c->var, 2);

		$this->passByVal($b);

		$this->assertEquals($a->var, 3);
		$this->assertEquals($b->var, 3);
		$this->assertEquals($c->var, 3);

		$this->passByRef($b);

		$this->assertEquals($a->var, 4);
		$this->assertEquals($b->var, 4);
		$this->assertEquals($c->var, 4);

		$this->passByVal($c);

		$this->assertEquals($a->var, 5);
		$this->assertEquals($b->var, 5);
		$this->assertEquals($c->var, 5);

		$this->passByRef($c);

		$this->assertEquals($a->var, 6);
		$this->assertEquals($b->var, 6);
		$this->assertEquals($c->var, 6);

		$a = (object) ['var' => 7];

		$this->assertEquals($a->var, 7);
		$this->assertEquals($b->var, 6);
		$this->assertEquals($c->var, 7);

		$a = null;

		$this->assertNull($a);
		$this->assertEquals($b->var, 6);
		$this->assertNull($c);

		$a = (object) ['var' => 8];

		$this->assertEquals($a->var, 8);
		$this->assertEquals($b->var, 6);
		$this->assertEquals($c->var, 8);

		unset($a);

		@$this->assertNull($a);
		$this->assertEquals($b->var, 6);
		$this->assertEquals($c->var, 8);

		$a = (object) ['var' => 9];

		$this->assertEquals($a->var, 9);
		$this->assertEquals($b->var, 6);
		$this->assertEquals($c->var, 8);
	}

	public function testReturn() {
		$this->var = (object) ['var' => 0];

		$val = $this->returnByVal();
		$ref = $this->returnByRef();

		$this->assertEquals(0, $val->var);
		$this->assertEquals(0, $ref->var);
		$this->assertEquals(0, $this->var->var);

		$this->var->var++;

		$this->assertEquals(1, $val->var);
		$this->assertEquals(1, $ref->var);
		$this->assertEquals(1, $this->var->var);

		$val->var++;

		$this->assertEquals(2, $val->var);
		$this->assertEquals(2, $ref->var);
		$this->assertEquals(2, $this->var->var);

		$ref->var++;

		$this->assertEquals(3, $val->var);
		$this->assertEquals(3, $ref->var);
		$this->assertEquals(3, $this->var->var);
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
