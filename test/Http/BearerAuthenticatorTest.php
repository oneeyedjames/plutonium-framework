<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Http\BearerAuthenticator;
use Plutonium\Http\Jwt;

class BearerAuthenticatorTest extends TestCase {
	const SECRET = '12345678900987654321';
	const ISSUER = 'Plutonium';
	const SUBJECT = 'SubjectID';

	public function testAuthenticate() {
		$authenticator = new BearerAuthenticator(self::SECRET);

		$jwt = new jwt();
		$jwt->iss = self::ISSUER;
		$jwt->sub = self::SUBJECT;
		$jwt->sign(self::SECRET);

		$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $jwt;

		$this->assertEquals(self::SUBJECT, $authenticator->authenticate());
	}

	public function testExpired() {
		$authenticator = new BearerAuthenticator(self::SECRET);

		$jwt = new jwt();
		$jwt->exp = time() - 60;
		$jwt->iss = self::ISSUER;
		$jwt->sub = self::SUBJECT;
		$jwt->sign(self::SECRET);

		$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $jwt;

		$this->assertFalse($authenticator->authenticate());
	}
}
