<?php

$_SERVER['SERVER_NAME'] = 'plutonium.local';

// $_SERVER['DOCUMENT_ROOT'] = '/var/www';

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI']    = '/';

session_start();

spl_autoload_register(function ($class) {
	$file = str_replace(['Plutonium', BS], ['', DS], $class) . '.php';
	$path = realpath(PU_PATH_LIB . '/' . ltrim($file, '\\'));
	if (is_file($path)) require_once $path;
});

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('LS', PHP_EOL);
define('BS', '\\');
define('FS', '/');

define('PU_PATH_BASE', 'vfs://plutonium');
define('PU_PATH_LIB',  realpath(__DIR__ . '/src'));
define('PU_PATH_FUNC', realpath(__DIR__ . '/src/Functions'));

require_once PU_PATH_FUNC . '/Strings.php';
require_once PU_PATH_FUNC . '/Arrays.php';
require_once PU_PATH_FUNC . '/Files.php';

class ApplicationMock {
	public function broadcastEvent() {}
}

class LocaleMock {
	public function load() {}
}

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use Plutonium\AccessObject;

class ComponentTestCase extends TestCase {
	protected function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'themes' => [
				'light' => [
					'theme.php' => '<?php',
					'layouts' => [
						'default.html.php' => 'default layout'
					]
				],
				'dark' => [
					'theme.php' => '<?php',
					'layouts' => [
						'default.html.php' => 'default layout'
					]
				]
			],
			'widgets' => [
				'calendar' => [
					'widget.php' => '<?php',
					'layouts' => [
						'default.html.php' => 'default layout'
					]
				]
			],
			'modules' => [
				'blog' => [
					'module.php' => '<?php',
					'views' => [
						'feed' => [
							'view.php' => '<?php',
							'layouts' => [
								'default.html.php' => 'feed default layout'
							]
						],
						'post' => [
							'view.php' => '<?php',
							'layouts' => [
								'default.html.php' => 'post default layout'
							]
						]
					]
				]
			]
		]);
	}

	protected function addFile($path, $data) {
		vfsStream::newFile($path)->at($this->directory)->setContent($data);
	}

	protected function createApplication($layout = 'default', $format = 'html') {
		$app = new ApplicationMock;
		$app->locale = new LocaleMock;
		$app->request = new AccessObject(compact('layout', 'format'));

		return $app;
	}
}
