<?php

use Plutonium\AccessObject;
use Plutonium\Application\Widget;

class WidgetTest extends ComponentTestCase {
	/*
	 * Tests that layout templates are properly located.
	 */
	public function testGetLayout() {
		$layout1 = $this->createWidget()->getLayout();
		$layout2 = $this->createWidget('item')->getLayout();

		$this->addFile('widgets/widgetname/layouts/item.html.php', 'item layout');

		$layout3 = $this->createWidget('item')->getLayout();

		$this->assertEquals(PU_PATH_BASE . '/widgets/calendar/layouts/default.html.php', $layout1);
		$this->assertEquals(PU_PATH_BASE . '/widgets/calendar/layouts/default.html.php', $layout2);
		$this->assertEquals(PU_PATH_BASE . '/widgets/calendar/layouts/default.html.php', $layout3);
	}

	/*
	 * Tests that layout templates are properly rendered.
	 */
	public function testRender() {
		$output1 = $this->createWidget()->render();
		$output2 = $this->createWidget('item')->render();

		$this->addFile('widgets/calendar/layouts/item.html.php', 'item layout');

		$output3 = $this->createWidget('item')->render();

		$this->assertEquals('default layout', $output1);
		$this->assertEquals('default layout', $output2);
		$this->assertEquals('default layout', $output3);
	}

	protected function createWidget($layout = 'default', $format = 'html') {
		return new Widget(new AccessObject([
			'name' => 'Calendar',
			'application' => $this->createApplication($layout, $format)
		]));
	}
}
