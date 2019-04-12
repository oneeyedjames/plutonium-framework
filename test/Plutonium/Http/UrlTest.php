<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Http\Url;

class RequestTest extends TestCase {
	public function setUp() {
		Url::initialize('http://plutonium.local/');
	}

	public function testBuildQuery() {
		$vars = ['foo' => 'bar'];

		$this->assertEquals('?foo=bar', Url::buildQuery($vars));
	}

	public function testQuery() {
		$url = new Url(['foo' => 'bar']);

		$this->assertEquals('?foo=bar', $url->query);
	}

	public function testString() {
		$url = new Url();

		$this->assertEquals('http://plutonium.local', $url->toString());
	}
}