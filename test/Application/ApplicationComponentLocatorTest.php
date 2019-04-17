<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\Application\ApplicationComponentLocator;

class ApplicationComponentLocatorTest extends TestCase {
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
}