<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Http\Session;

class SessionTest extends TestCase {
	public function setUp() {
		foreach ($_SESSION as $key => $value)
			unset($_SESSION[$key]);
	}

	/*
	 * Tests that session variables are properly stored in default namespace.
	 */
	public function testSet() {
		$session = new Session();

		$this->assertFalse($session->has('foo'));

		$session->set('foo', 'bar');

		$this->assertTrue($session->has('foo'));
		$this->assertEquals('bar', $session->get('foo'));
	}

	/*
	 * Tests that session variables are properly stored in arbitary namespace.
	 */
	public function testSetNamespace() {
		$session = new Session();

		$this->assertFalse($session->has('foo', 'other'));

		$session->set('foo', 'baz', 'other');

		$this->assertTrue($session->has('foo', 'other'));
		$this->assertEquals('baz', $session->get('foo', 'bar', 'other'));
	}

	/*
	 * Tests that defaulting values will not overwrite existing values.
	 */
	public function testDefault() {
		$session = new Session();

		$this->assertFalse($session->has('foo'));

		$session->set('foo', 'bar');
		$session->def('foo', 'baz');
		$session->def('baz', 'bat');

		$this->assertTrue($session->has('foo'));
		$this->assertTrue($session->has('baz'));
		$this->assertEquals('bar', $session->get('foo'));
		$this->assertEquals('bat', $session->get('baz'));
	}

	/*
	 * Tests that all session instances reference the same backing data store.
	 */
	public function testDelete() {
		$session1 = new Session();
		$session2 = new Session();

		$session1->set('foo', 'bar');

		$this->assertTrue($session1->has('foo'));
		$this->assertTrue($session2->has('foo'));
		$this->assertEquals('bar', $session1->get('foo'));
		$this->assertEquals('bar', $session2->get('foo'));

		$session2->del('foo');

		$this->assertFalse($session1->has('foo'));
		$this->assertFalse($session2->has('foo'));
	}
}
