<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Http\BasicAuthenticator;

class BasicAuthenticatorTest extends TestCase {
	public function testAuthenticate() {
		$authenticator = new BasicAuthenticator([$this, 'getUser']);

		$_SERVER['HTTP_AUTHORIZATION'] = 'Basic ' . base64_encode('username123:password123');

		$this->assertEquals(123, $authenticator->authenticate());
	}

	public function testBadUsername() {
		$authenticator = new BasicAuthenticator([$this, 'getUser']);

		$_SERVER['HTTP_AUTHORIZATION'] = 'Basic ' . base64_encode('username:password123');

		$this->assertFalse($authenticator->authenticate());
	}

	public function testBadPassword() {
		$authenticator = new BasicAuthenticator([$this, 'getUser']);

		$_SERVER['HTTP_AUTHORIZATION'] = 'Basic ' . base64_encode('username123:password');

		$this->assertFalse($authenticator->authenticate());
	}

	public function getUser($username, $password) {
		if ($username != 'username123') return false;
		if ($password != 'password123') return false;
		return 123;
	}
}
