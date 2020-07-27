<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Http\CookieAuthenticator;
use Plutonium\Http\Jwt;

class CookieAuthenticatorTest extends TestCase {
	const SECRET = '12345678900987654321';
	const COOKIE = 'PlutoniumUser';
	const ISSUER = 'Plutonium';
	const SUBJECT = 'SubjectID';

	public function testAuthenticate() {
		$authenticator = new CookieAuthenticator(self::SECRET, self::COOKIE);

		$jwt = new jwt();
		$jwt->iss = self::ISSUER;
		$jwt->sub = self::SUBJECT;
		$jwt->sign(self::SECRET);

		$_COOKIE[self::COOKIE] = "$jwt";

		$this->assertEquals(self::SUBJECT, $authenticator->authenticate());
	}

	public function testExpired() {
		$authenticator = new CookieAuthenticator(self::SECRET, self::COOKIE);

		$jwt = new jwt();
		$jwt->exp = time() - 60;
		$jwt->iss = self::ISSUER;
		$jwt->sub = self::SUBJECT;
		$jwt->sign(self::SECRET);

		$_COOKIE[self::COOKIE] = "$jwt";

		$this->assertFalse($authenticator->authenticate());
	}
}
