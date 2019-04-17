<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\Application\ApplicationComponentLocator;

class ApplicationComponentLocatorTest extends TestCase {
	public function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'themes' => [
				'themename' => [
					'theme.php' => '<?php',
					'layouts' => [
						'default.html.php' => ''
					]
				]
			],
			'widgets' => [
				'widgetname' => [
					'widget.php' => '<?php',
					'layouts' => [
						'default.html.php' => ''
					]
				]
			]
		]);
	}

	public function testGetPath() {
		$locator = new ApplicationComponentLocator('modules');

		$path = $locator->getPath('ModuleName');
		$phar = $locator->getPath('ModuleName', true);

		$this->assertEquals(PU_PATH_BASE . '/modules/modulename', $path);
		$this->assertEquals(PU_PATH_BASE . '/modules/modulename.phar', $phar);
	}

	public function testGetFile() {
		$locator = new ApplicationComponentLocator('modules');

		$file = $locator->getFile('ModuleName', 'module.php');
		$phar = $locator->getFile('ModuleName', 'module.php', true);

		$this->assertEquals(PU_PATH_BASE . '/modules/modulename/module.php', $file);
		$this->assertEquals(PU_PATH_BASE . '/modules/modulename.phar/module.php', $phar);
	}

	public function testLocateFile() {
		$locator = new ApplicationComponentLocator('themes');

		$file = $locator->locateFile('ThemeName',
			'layouts/item.html.php', 'layouts/default.html.php');

		$this->assertEquals(PU_PATH_BASE . '/themes/themename/layouts/default.html.php', $file);
		$this->assertFalse($locator->locateFile('ThemeName', 'layouts/item.html.php'));
	}
}