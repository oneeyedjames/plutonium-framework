<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Http\Url;

class UrlTest extends TestCase {
	public function setUp() {
		Url::initialize('http://plutonium.local/');
	}

	/*
	 * Tests that query strings are properly constructed.
	 */
	public function testBuildQuery() {
		$vars = ['foo' => 'bar'];

		$this->assertEquals('?foo=bar', Url::buildQuery($vars));
	}

	/*
	 * Tests that query string is an accessible property.
	 */
	public function testQuery() {
		$url = new Url(['foo' => 'bar']);

		$this->assertEquals('?foo=bar', $url->query);
		$this->assertEquals('?foo=bar', $url->get('query'));
		$this->assertEquals('?foo=bar', $url['query']);
	}

	/*
	 * Tests that URL string representation is properly constructed.
	 */
	public function testString() {
		$url = new Url();

		$this->assertEquals('http://plutonium.local', $url->toString());
		$this->assertEquals('http://plutonium.local', "$url");
	}
}
