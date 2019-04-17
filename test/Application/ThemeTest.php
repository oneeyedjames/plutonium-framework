<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\Application\Theme;

class ThemeTest extends TestCase {
	public function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'themes' => [
				'themename' => [
					'theme.php' => '<?php',
					'layouts' => []
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
}