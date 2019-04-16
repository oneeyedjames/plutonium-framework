<?php

use PHPUnit\Framework\TestCase;

use Plutonium\Application\Theme;

class ThemeTest extends TestCase {
	public function testGetPath() {
		$path = Theme::getPath('ThemeName');

		$this->assertEquals(PU_PATH_BASE . '/themes/themename', $path);
	}

	public function testGetFile() {
		$file = Theme::getFile('ThemeName', 'theme.php');
		$phar = Theme::getFile('ThemeName', 'theme.php', true);

		$this->assertEquals(PU_PATH_BASE . '/themes/themename/theme.php', $file);
		$this->assertEquals(PU_PATH_BASE . '/themes/themename.phar/theme.php', $phar);
	}
}