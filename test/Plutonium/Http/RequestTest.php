<?php

use PHPUnit\Framework\TestCase;

use Plutonium\AccessObject;
use Plutonium\Http\Request;

class RequestTest extends TestCase {
	var $config;

	protected function reset() {
		$_SERVER['SERVER_NAME'] = 'plutonium.local';

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI']    = '/';

		$_SERVER['HTTP_HOST'] = 'plutonium.local';
	}

	public function setUp() {
		$this->reset();

		if (is_null($this->config)) {
			$this->config = new AccessObject(array(
				'system' => array(
					'hostname' => 'plutonium.local'
				)
			));
		}
	}

	public function tearDown() {
		$this->reset();
	}

	public function testMethodGet() {
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$request = new Request($this->config->system);

		$this->assertEquals('GET', $request->method);
	}

	public function testMethodPost() {
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$request = new Request($this->config->system);

		$this->assertEquals('POST', $request->method);
	}

	public function testMethodMapping() {
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$_GET['_method'] = $_REQUEST['_method'] = 'HEAD';

		$request = new Request($this->config->system);

		$this->assertEquals('HEAD', $request->method);

		$this->assertNull($request->get('_method', null, 'get'));
		$this->assertNull($request->get('_method', null));

		unset($_GET['_method'], $_REQUEST['_method']);

		$_SERVER['REQUEST_METHOD'] = 'POST';

		$_POST['_method'] = $_REQUEST['_method'] = 'PUT';

		$request = new Request($this->config->system);

		$this->assertEquals('PUT', $request->method);

		$this->assertNull($request->get('_method', null, 'post'));
		$this->assertNull($request->get('_method', null));

		unset($_POST['_method'], $_REQUEST['_method']);

		$_POST['_method'] = $_REQUEST['_method'] = 'DELETE';

		$request = new Request($this->config->system);

		$this->assertEquals('DELETE', $request->method);

		$this->assertNull($request->get('_method', null, 'post'));
		$this->assertNull($request->get('_method', null));

		unset($_POST['_method'], $_REQUEST['_method']);
	}

	public function testHost() {
		$request = new Request($this->config->system);

		$this->assertEquals('plutonium.local', $request->get('Host', null, 'headers'));
		$this->assertEquals(80, $request->get('Port', null, 'headers'));
	}

	public function testHostPort() {
		$_SERVER['HTTP_HOST'] .= ':8080';

		$request = new Request($this->config->system);

		$this->assertEquals('plutonium.local', $request->get('Host', null, 'headers'));
		$this->assertEquals(8080, $request->get('Port', null, 'headers'));
	}

	public function testUri() {
		$_SERVER['REQUEST_URI'] = '/path/to/resource';
		$request = new Request($this->config->system);

		$this->assertEquals($request->uri, '/path/to/resource');
		$this->assertNull($request->format);

		$_SERVER['REQUEST_URI'] = '/path/to/resource/';
		$request = new Request($this->config->system);

		$this->assertEquals($request->uri, '/path/to/resource');
		$this->assertNull($request->format);
	}

	public function testUriFormat() {
		$_SERVER['REQUEST_URI'] = '/path/to/resource.ext';
		$request = new Request($this->config->system);

		$this->assertEquals($request->uri, '/path/to/resource');
		$this->assertEquals($request->format, 'ext');

		$_SERVER['REQUEST_URI'] = '/path/to/resource.ext/';
		$request = new Request($this->config->system);

		$this->assertEquals($request->uri, '/path/to/resource');
		$this->assertEquals($request->format, 'ext');
	}

	/* public function testFormat() {
		$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml';

		$request = new Request($this->config->system);

		$this->assertEquals('html', $request->format);
	} */
}
