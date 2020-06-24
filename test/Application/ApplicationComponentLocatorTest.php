<?php

use org\bovigo\vfs\vfsStream;

use Plutonium\Application\ApplicationComponentLocator;

class ApplicationComponentLocatorTest extends ComponentTestCase {
	/*
	 * Tests that the path to the component is properly formed.
	 */
	public function testGetPath() {
		$locator = new ApplicationComponentLocator('modules');

		$path = $locator->getPath('Blog');
		$phar = $locator->getPath('Blog', true);

		$this->assertEquals(PU_PATH_BASE . '/modules/blog', $path);
		$this->assertEquals(PU_PATH_BASE . '/modules/blog.phar', $phar);
	}

	/*
	 * Tests that the paths to the component files are properly formed.
	 */
	public function testGetFile() {
		$locator = new ApplicationComponentLocator('modules');

		$file = $locator->getFile('Blog', 'module.php');
		$phar = $locator->getFile('Blog', 'module.php', true);

		$this->assertEquals(PU_PATH_BASE . '/modules/blog/module.php', $file);
		$this->assertEquals(PU_PATH_BASE . '/modules/blog.phar/module.php', $phar);
	}

	/*
	 * Tests that the component files are properly located.
	 */
	public function testLocateFile() {
		$locator = new ApplicationComponentLocator('modules');

		$file = $locator->locateFile('Blog',
			'views/post/layouts/item.html.php',
			'views/post/layouts/default.html.php');

		$this->assertEquals(PU_PATH_BASE . '/modules/blog/views/post/layouts/default.html.php', $file);
		$this->assertFalse($locator->locateFile('Blog', 'views/post/layouts/item.html.php'));
	}
}
