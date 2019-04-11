<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Http\Session;

class SessionTest extends TestCase {
	public function testAccessible() {
		$session = new Session();

		$this->assertFalse($session->has('foo'));

		$session->set('foo', 'bar');

		$this->assertTrue($session->has('foo'));
		$this->assertEquals('bar', $session->get('foo'));

		$session->def('foo', 'baz');

		$this->assertTrue($session->has('foo'));
		$this->assertEquals('bar', $session->get('foo'));

		$other_session = new Session();

		$this->assertTrue($other_session->has('foo'));
		$this->assertEquals('bar', $other_session->get('foo'));

		$other_session->del('foo');

		$this->assertFalse($session->has('foo'));
		$this->assertFalse($other_session->has('foo'));
	}

	public function testNamespace() {
		$session = new Session();

		$this->assertFalse($session->has('foo'));
		$this->assertFalse($session->has('foo', 'other'));

		$session->set('foo', 'bar');
		$session->set('foo', 'baz', 'other');

		$this->assertTrue($session->has('foo'));
		$this->assertTrue($session->has('foo', 'other'));
		$this->assertEquals('bar', $session->get('foo'));
		$this->assertEquals('baz', $session->get('foo', 'bar', 'other'));
	}
}
