<?php

use PHPUnit\Framework\TestCase;

use Plutonium\AccessObject;
use Plutonium\Http\Request;

class RequestTest extends TestCase {
	var $config;

	protected function reset() {
		$_SERVER['SERVER_NAME'] = 'plutonium.dev';

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI']    = '/';

		$_SERVER['HTTP_HOST'] = 'plutonium.dev';
	}

	public function setUp() {
		$this->reset();

		if (is_null($this->config)) {
			$this->config = new AccessObject(array(
				'system' => array(
					'hostname' => 'plutonium.dev'
				)
			));
		}
	}

	public function tearDown() {
		$this->reset();
	}

	public function testMethod() {
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$request = new Request($this->config->system);

		$this->assertEquals('GET', $request->method);

		$_SERVER['REQUEST_METHOD'] = 'HEAD';

		$request = new Request($this->config->system);

		$this->assertEquals('HEAD', $request->method);

		$_SERVER['REQUEST_METHOD'] = 'POST';

		$request = new Request($this->config->system);

		$this->assertEquals('POST', $request->method);

		$_SERVER['REQUEST_METHOD'] = 'PUT';

		$request = new Request($this->config->system);

		$this->assertEquals('PUT', $request->method);

		$_SERVER['REQUEST_METHOD'] = 'DELETE';

		$request = new Request($this->config->system);

		$this->assertEquals('DELETE', $request->method);
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
		$request->parseHost('plutonium.dev', 'plutonium.dev');

		$this->assertNull($request->host);
		$this->assertNull($request->module);

		$request->parseHost('main.plutonium.dev', 'plutonium.dev');

		$this->assertEquals($request->host, 'main');
		$this->assertNull($request->module);

		$request->parseHost('site.main.plutonium.dev', 'plutonium.dev');

		$this->assertEquals($request->host,   'main');
		$this->assertEquals($request->module, 'site');
	}

	public function testPath() {
		$request = new Request($this->config->system);
		$request->parsePath('/path/to/resource.ext');

		$this->assertEquals($request->path, 'path/to/resource');
		$this->assertEquals($request->format, 'ext');

		$request->parsePath('/path/to/resource');

		$this->assertEquals($request->path, 'path/to/resource');
		$this->assertNull($request->format);

		$request->parsePath('path/to/resource.ext');

		$this->assertEquals($request->path, 'path/to/resource');
		$this->assertEquals($request->format, 'ext');

		$request->parsePath('path/to/resource');

		$this->assertEquals($request->path, 'path/to/resource');
		$this->assertNull($request->format);

		$request->parsePath('/path/to/resource.ext/');

		$this->assertEquals($request->path, 'path/to/resource');
		$this->assertEquals($request->format, 'ext');

		$request->parsePath('/path/to/resource/');

		$this->assertEquals($request->path, 'path/to/resource');
		$this->assertNull($request->format);
	}

	public function testFormat() {
		$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml';

		$request = new Request($this->config->system);

		$this->assertEquals('html', $request->format);
	}
}
