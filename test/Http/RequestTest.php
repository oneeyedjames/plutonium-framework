<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Collection\AccessibleObject;
use Plutonium\Http\Request;

class RequestTest extends TestCase {
	public function setUp() {
		$_SERVER['SERVER_NAME'] = 'plutonium.local';

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI']    = '/';

		$_SERVER['HTTP_HOST'] = 'plutonium.local';

		$this->config = new AccessibleObject(array(
			'system' => array(
				'hostname' => 'plutonium.local'
			)
		));
	}

	/*
	 * Tests that GET requests are properly recognized.
	 */
	public function testMethodGet() {
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$request = new Request();

		$this->assertEquals('GET', $request->method);
	}

	/*
	 * Tests that POST requests are properly recognized.
	 */
	public function testMethodPost() {
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$request = new Request();

		$this->assertEquals('POST', $request->method);
	}

	/*
	 * Tests that PUT requests are properly recognized.
	 */
	public function testMethodPut() {
		$_SERVER['REQUEST_METHOD'] = 'PUT';

		$request = new Request();

		$this->assertEquals('PUT', $request->method);
	}

	/*
	 * Tests that POST requests can be aliased as PUT requests
	 */
	public function testMethodPutAlias() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST['_method'] = $_REQUEST['_method'] = 'PUT';

		$request = new Request();

		$this->assertEquals('PUT', $request->method);

		$this->assertNull($request->get('_method', null, 'post'));
		$this->assertNull($request->get('_method', null));
	}

	/*
	 * Tests that DELETE requests are properly recognized.
	 */
	public function testMethodDelete() {
		$_SERVER['REQUEST_METHOD'] = 'DELETE';

		$request = new Request();

		$this->assertEquals('DELETE', $request->method);
	}

	/*
	 * Tests that POST requests can be aliased as DELETE requests
	 */
	public function testMethodDeleteAlias() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST['_method'] = $_REQUEST['_method'] = 'DELETE';

		$request = new Request();

		$this->assertEquals('DELETE', $request->method);

		$this->assertNull($request->get('_method', null, 'post'));
		$this->assertNull($request->get('_method', null));
	}

	/*
	 * Tests that HEAD requests are properly recognized.
	 */
	public function testMethodHead() {
		$_SERVER['REQUEST_METHOD'] = 'HEAD';

		$request = new Request();

		$this->assertEquals('HEAD', $request->method);
	}

	/*
	 * Tests that GET requests can be aliased as HEAD requests
	 */
	public function testMethodHeadAlias() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['_method'] = $_REQUEST['_method'] = 'HEAD';

		$request = new Request();

		$this->assertEquals('HEAD', $request->method);

		$this->assertNull($request->get('_method', null, 'get'));
		$this->assertNull($request->get('_method', null));
	}

	/*
	 * Tests that OPTIONS requests are properly recognized.
	 */
	public function testMethodOptions() {
		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';

		$request = new Request();

		$this->assertEquals('OPTIONS', $request->method);
	}

	/*
	 * Tests that GET requests can be aliased as OPTIONS requests
	 */
	public function testMethodOptionsAlias() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['_method'] = $_REQUEST['_method'] = 'OPTIONS';

		$request = new Request();

		$this->assertEquals('OPTIONS', $request->method);

		$this->assertNull($request->get('_method', null, 'get'));
		$this->assertNull($request->get('_method', null));
	}

	/*
	 * Tests that hostname and default port number are properly identified.
	 */
	public function testHost() {
		$request = new Request();

		$this->assertEquals('plutonium.local', $request->get('Host', null, 'headers'));
		$this->assertEquals(80, $request->get('Port', null, 'headers'));
	}

	/*
	 * Tests that hostname and custom port number are properly identified.
	 */
	public function testHostPort() {
		$_SERVER['HTTP_HOST'] .= ':8080';

		$request = new Request();

		$this->assertEquals('plutonium.local', $request->get('Host', null, 'headers'));
		$this->assertEquals(8080, $request->get('Port', null, 'headers'));
	}

	/*
	 * Tests that URL path is properly recognized.
	 */
	public function testUri() {
		$_SERVER['REQUEST_URI'] = '/path/to/resource';
		$request = new Request($this->config->system);

		$this->assertEquals($request->uri, '/path/to/resource');
		$this->assertNull($request->format);
	}

	/*
	 * Tests that URL path is properly recognized and trailing slash is removed.
	 */
	public function testUriSlash() {
		$_SERVER['REQUEST_URI'] = '/path/to/resource/';
		$request = new Request($this->config->system);

		$this->assertEquals($request->uri, '/path/to/resource');
		$this->assertNull($request->format);
	}

	/*
	 * Tests that URL path and file extension are properly recognized.
	 */
	public function testUriFormat() {
		$_SERVER['REQUEST_URI'] = '/path/to/resource.ext';
		$request = new Request($this->config->system);

		$this->assertEquals($request->uri, '/path/to/resource');
		$this->assertEquals($request->format, 'ext');
	}

	/*
	 * Tests that URL path and file extension are properly recognized and
	 * trailing slash is removed.
	 */
	public function testUriFormatSlash() {
		$_SERVER['REQUEST_URI'] = '/path/to/resource.ext/';
		$request = new Request($this->config->system);

		$this->assertEquals($request->uri, '/path/to/resource');
		$this->assertEquals($request->format, 'ext');
	}

	/*
	 * Tests that HTTP Accept header is properly recognized.
	 */
	public function testFormat() {
		$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml';

		$request = new Request();

		$this->assertEquals('text/html,application/xhtml+xml',
			$request->get('Accept', null, 'headers'));
	}
}
