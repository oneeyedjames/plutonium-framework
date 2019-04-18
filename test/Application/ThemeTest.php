<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\AccessObject;
use Plutonium\Application\Theme;

class ThemeTest extends TestCase {
	public function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'themes' => [
				'themename' => [
					'theme.php' => '<?php',
					'layouts' => [
						'default.html.php' => ''
					]
				]
			]
		]);
	}

	public function testGetPath() {
		$path = Theme::getLocator()->getPath('ThemeName');
		$this->assertTrue(file_exists($path));
	}

	public function testGetFile() {
		$file = Theme::getLocator()->getFile('ThemeName', 'theme.php');
		$this->assertTrue(file_exists($file));
	}

	public function testGetLayout() {
		$theme = new Theme(new AccessObject([
			'name' => 'ThemeName',
			'application' => (object) [
				'locale' => new LocaleMock
			]
		]));

		$request = new AccessObject([
			'layout' => 'item',
			'format' => 'html'
		]);

		$layout = $theme->getLayout($request);

		$this->assertEquals(PU_PATH_BASE . '/themes/themename/layouts/default.html.php', $layout);
	}
}