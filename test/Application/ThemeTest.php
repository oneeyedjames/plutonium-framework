<?php

use Plutonium\Collection\AccessibleCollection;
use Plutonium\Application\Theme;

class ThemeTest extends ComponentTestCase {
	/*
	 * Tests that layout templates are properly located.
	 */
	public function testGetLayout() {
		$layout1 = $this->createTheme()->getLayout();
		$layout2 = $this->createTheme('item')->getLayout();

		$this->addFile('themes/light/layouts/item.html.php', 'item layout');

		$layout3 = $this->createTheme('item')->getLayout();

		$this->assertEquals(PU_PATH_BASE . '/themes/light/layouts/default.html.php', $layout1);
		$this->assertEquals(PU_PATH_BASE . '/themes/light/layouts/default.html.php', $layout2);
		$this->assertEquals(PU_PATH_BASE . '/themes/light/layouts/item.html.php', $layout3);
	}

	/*
	 * Tests that layout templates are properly rendereds.
	 */
	public function testRender() {
		$output1 = $this->createTheme()->render();
		$output2 = $this->createTheme('item')->render();

		$this->addFile('themes/light/layouts/item.html.php', 'item layout');

		$output3 = $this->createTheme('item')->render();

		$this->assertEquals('default layout', $output1);
		$this->assertEquals('default layout', $output2);
		$this->assertEquals('item layout', $output3);
	}

	protected function createTheme($layout = 'default', $format = 'html') {
		return new Theme(new AccessibleCollection([
			'name' => 'Light',
			'application' => $this->createApplication($layout, $format)
		]));
	}
}
