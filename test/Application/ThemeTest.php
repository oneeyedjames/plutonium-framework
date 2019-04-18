<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

use Plutonium\AccessObject;
use Plutonium\Application\Theme;

class ThemeTest extends TestCase {
	const NAME = 'ThemeName';

	public function setUp() {
		$this->directory = vfsStream::setup('/plutonium', 644, [
			'themes' => [
				'themename' => [
					'theme.php' => '<?php',
					'layouts' => [
						'default.html.php' => 'default layout'
					]
				]
			]
		]);
	}

	public function testGetLayout() {
		$layout1 = $this->createTheme('default')->getLayout();
		$layout2 = $this->createTheme('item')->getLayout();

		$this->assertEquals(PU_PATH_BASE . '/themes/themename/layouts/default.html.php', $layout1);
		$this->assertEquals(PU_PATH_BASE . '/themes/themename/layouts/default.html.php', $layout2);

		$this->addFile('themes/themename/layouts/item.html.php', 'item layout');

		$layout3 = $this->createTheme('item')->getLayout();

		$this->assertEquals(PU_PATH_BASE . '/themes/themename/layouts/item.html.php', $layout3);
	}

	public function testRender() {
		$output1 = $this->createTheme('default')->render();
		$output2 = $this->createTheme('item')->render();

		$this->addFile('themes/themename/layouts/item.html.php', 'item layout');

		$output3 = $this->createTheme('item')->render();

		$this->assertEquals('default layout', $output1);
		$this->assertEquals('default layout', $output2);
		$this->assertEquals('item layout', $output3);
	}

	protected function addFile($path, $data) {
		vfsStream::newFile($path)->at($this->directory)->setContent($data);
	}

	protected function createTheme($layout = 'default', $format = 'html') {
		$app = new ApplicationMock;
		$app->locale = new LocaleMock;
		$app->request = new AccessObject(compact('layout', 'format'));

		return new Theme(new AccessObject([
			'name' => self::NAME,
			'application' => $app
		]));
	}
}