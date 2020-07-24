<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Http\Jwt;

class JwtTest extends TestCase {
	const SECRET = '12345678900987654321';
	const ISSUER = 'Plutonium';
	const SUBJECT = 'SubjectID';

	public function testCreate() {
		$jwt = new jwt();

		$header = $jwt->header;
		$payload = $jwt->payload;
		$signature = $jwt->signature;

		$this->assertEquals(2, count($header));
		$this->assertEquals(0, count($payload));

		$this->assertEquals('HS256', $header['alg']);
		$this->assertEquals('JWT', $header['typ']);

		$this->assertNull($signature);
	}

	public function testUpdate() {
		$jwt = new jwt();
		$jwt->iss = self::ISSUER;
		$jwt->sub = self::SUBJECT;

		$header = $jwt->header;
		$payload = $jwt->payload;
		$signature = $jwt->signature;

		$this->assertEquals(2, count($header));
		$this->assertEquals(2, count($payload));

		$this->assertEquals('HS256', $header['alg']);
		$this->assertEquals('JWT', $header['typ']);

		$this->assertEquals(self::ISSUER, $payload['iss']);
		$this->assertEquals(self::SUBJECT, $payload['sub']);

		$this->assertNull($signature);
	}

	public function testExpired() {
		$jwt = new jwt();
		$jwt->exp = time() - 60;

		$this->assertTrue($jwt->expired);
	}

	public function testNotExpired() {
		$jwt = new jwt();
		$jwt->exp = time() + 60;

		$this->assertFalse($jwt->expired);
	}

	public function testSign() {
		$jwt = new jwt();
		$jwt->iss = self::ISSUER;
		$jwt->sub = self::SUBJECT;

		$this->assertNull($jwt->signature);

		$jwt->sign(self::SECRET);

		$this->assertNotNull($jwt->signature);
	}

	public function testValidateSuccess() {
		$jwt = new jwt();
		$jwt->iss = self::ISSUER;
		$jwt->sub = self::SUBJECT;
		$jwt->sign(self::SECRET);

		$this->assertTrue($jwt->validate(self::SECRET));
	}

	public function testValidateFailure() {
		$jwt = new jwt();
		$jwt->iss = self::ISSUER;
		$jwt->sub = self::SUBJECT;
		$jwt->sign(self::SECRET);
		$jwt->exp = time();

		$this->assertFalse($jwt->validate(self::SECRET));
	}
}
